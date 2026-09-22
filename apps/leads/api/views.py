import re
from datetime import datetime

from django.utils import timezone
from django.utils.dateparse import parse_datetime
from rest_framework import serializers, viewsets
from rest_framework.decorators import action, api_view, permission_classes
from rest_framework.permissions import AllowAny
from rest_framework.response import Response

from apps.accounts.permissions import require_perm
from apps.core.visibility import filter_by_product_scope
from apps.leads.models import ContactMessage, DemoRequest, LeadCommunication
from apps.leads.tasks import send_lead_email, send_lead_sms, send_lead_whatsapp
from apps.products.models import Product

ENQUIRY_STATUSES = ("new", "contacted", "in_progress", "completed", "closed")

PHONE_RE = re.compile(r"^[\d\s\-()+.]{7,32}$")


class ContactMessageSerializer(serializers.ModelSerializer):
    product_name = serializers.CharField(source="product.name", read_only=True, allow_null=True)
    product_slug = serializers.CharField(source="product.slug", read_only=True, allow_null=True)
    preferred_at = serializers.SerializerMethodField()

    class Meta:
        model = ContactMessage
        fields = "__all__"

    def get_preferred_at(self, obj):
        demo = obj.demo_requests.order_by("-id").first()
        if demo and demo.preferred_at:
            return demo.preferred_at.isoformat()
        return None


class PublicContactSerializer(serializers.Serializer):
    name = serializers.CharField(max_length=255)
    company = serializers.CharField(required=False, allow_blank=True, max_length=255)
    email = serializers.EmailField()
    phone = serializers.CharField(required=False, allow_blank=True, max_length=255)
    topic = serializers.CharField(required=False, allow_blank=True, max_length=255)
    subject = serializers.CharField(required=False, allow_blank=True, max_length=255)
    message = serializers.CharField()
    request_type = serializers.CharField(required=False, allow_blank=True, max_length=64)
    product_id = serializers.IntegerField(required=False, allow_null=True)
    product_slug = serializers.SlugField(required=False, allow_blank=True)
    preferred_at = serializers.CharField(required=False, allow_blank=True)
    source = serializers.CharField(required=False, allow_blank=True, max_length=255)
    landing_page = serializers.CharField(required=False, allow_blank=True, max_length=255)
    referrer = serializers.CharField(required=False, allow_blank=True, max_length=255)

    def validate_phone(self, value):
        if not value:
            return value
        if not PHONE_RE.match(value.strip()):
            raise serializers.ValidationError("Enter a valid phone number.")
        return value.strip()

    def validate(self, attrs):
        product = None
        product_id = attrs.get("product_id")
        product_slug = (attrs.get("product_slug") or "").strip()
        if product_id:
            product = Product.objects.filter(pk=product_id, is_active=True).first()
            if not product:
                raise serializers.ValidationError({"product_id": "Product not found."})
        elif product_slug:
            product = Product.objects.filter(slug=product_slug, is_active=True).first()
            if not product:
                raise serializers.ValidationError({"product_slug": "Product not found."})
        attrs["_product"] = product

        preferred_raw = (attrs.get("preferred_at") or "").strip()
        preferred = None
        if preferred_raw:
            preferred = parse_datetime(preferred_raw)
            if preferred is None:
                try:
                    preferred = datetime.fromisoformat(preferred_raw)
                except ValueError as exc:
                    raise serializers.ValidationError(
                        {"preferred_at": "Use an ISO date/time value."}
                    ) from exc
            if timezone.is_naive(preferred):
                preferred = timezone.make_aware(preferred, timezone.get_current_timezone())
        attrs["_preferred_at"] = preferred

        if not (attrs.get("subject") or "").strip():
            if product:
                attrs["subject"] = f"Demo: {product.name}"
            else:
                attrs["subject"] = "Website enquiry"

        return attrs


@api_view(["POST"])
@permission_classes([AllowAny])
def public_contact(request):
    ser = PublicContactSerializer(data=request.data)
    ser.is_valid(raise_exception=True)
    data = ser.validated_data
    product = data.get("_product")
    preferred_at = data.get("_preferred_at")
    session = request.session
    request_type = (data.get("request_type") or "").strip() or (
        "demo" if product else "contact"
    )
    source = (data.get("source") or "").strip() or "website"
    landing_page = (data.get("landing_page") or "").strip() or request.META.get("HTTP_REFERER")

    msg = ContactMessage.objects.create(
        name=data["name"].strip(),
        company=(data.get("company") or "").strip() or None,
        email=data["email"].strip().lower(),
        phone=(data.get("phone") or "").strip() or None,
        topic=(data.get("topic") or "").strip() or None,
        subject=data["subject"].strip(),
        message=data["message"].strip(),
        request_type=request_type,
        product=product,
        source=source,
        landing_page=landing_page,
        referrer=(data.get("referrer") or "").strip() or None,
        utm_source=session.get("utm.utm_source"),
        utm_medium=session.get("utm.utm_medium"),
        utm_campaign=session.get("utm.utm_campaign"),
        utm_content=session.get("utm.utm_content"),
        utm_term=session.get("utm.utm_term"),
        status="new",
    )

    if request_type in ("demo", "product_demo", "book_demo") or product is not None:
        DemoRequest.objects.create(
            contact_message=msg,
            product=product,
            request_type=request_type if request_type else "demo",
            preferred_at=preferred_at,
            status="requested",
        )

    return Response(
        {
            "id": msg.id,
            "detail": "Received.",
            "product_id": product.id if product else None,
            "status": msg.status,
        },
        status=201,
    )


class EnquiryViewSet(viewsets.ModelViewSet):
    serializer_class = ContactMessageSerializer
    permission_classes = [require_perm("leads.view|leads.manage")]
    http_method_names = ["get", "post", "patch", "put", "head", "options"]

    def get_queryset(self):
        qs = ContactMessage.objects.select_related("product", "campaign").prefetch_related(
            "demo_requests"
        )
        return filter_by_product_scope(qs, self.request.user)

    def get_permissions(self):
        if self.action in ("create", "update", "partial_update", "destroy", "reply", "assign", "set_status"):
            return [require_perm("leads.manage|inbox.reply|leads.assign")()]
        return super().get_permissions()

    def update(self, request, *args, **kwargs):
        if "status" in request.data:
            status_norm = str(request.data.get("status") or "").strip().lower().replace(" ", "_")
            if status_norm not in ENQUIRY_STATUSES:
                return Response(
                    {"detail": f"Invalid status. Allowed: {', '.join(ENQUIRY_STATUSES)}"},
                    status=400,
                )
            partial = kwargs.pop("partial", False)
            instance = self.get_object()
            data = {k: v for k, v in request.data.items()}
            data["status"] = status_norm
            serializer = self.get_serializer(instance, data=data, partial=partial)
            serializer.is_valid(raise_exception=True)
            self.perform_update(serializer)
            return Response(serializer.data)
        return super().update(request, *args, **kwargs)

    @action(detail=True, methods=["post"])
    def set_status(self, request, pk=None):
        msg = self.get_object()
        status_value = str(request.data.get("status") or "").strip().lower().replace(" ", "_")
        if status_value not in ENQUIRY_STATUSES:
            return Response(
                {"detail": f"Invalid status. Allowed: {', '.join(ENQUIRY_STATUSES)}"},
                status=400,
            )
        msg.status = status_value
        if status_value == "contacted" and not msg.responded_at:
            msg.responded_at = timezone.now()
            msg.responded_by = request.user
        msg.save()
        return Response(ContactMessageSerializer(msg, context={"request": request}).data)

    @action(detail=True, methods=["post"])
    def reply(self, request, pk=None):
        msg = self.get_object()
        channel = request.data.get("channel", "email")
        subject = request.data.get("subject") or f"Re: {msg.subject}"
        body = request.data.get("body", "")
        comm = LeadCommunication.objects.create(
            contact_message=msg,
            user=request.user,
            channel=channel,
            subject=subject,
            body=body,
            status="queued",
        )
        if channel == "email":
            send_lead_email.delay(comm.id)
        elif channel == "sms":
            send_lead_sms.delay(comm.id)
        elif channel == "whatsapp":
            send_lead_whatsapp.delay(comm.id)
        msg.responded_by = request.user
        msg.responded_at = timezone.now()
        if not msg.first_responded_at:
            msg.first_responded_at = timezone.now()
        msg.status = "contacted"
        msg.save()
        return Response({"communication_id": comm.id, "status": "queued"})

    @action(detail=True, methods=["post"])
    def assign(self, request, pk=None):
        msg = self.get_object()
        msg.assigned_to_id = request.data.get("assigned_to")
        msg.save(update_fields=["assigned_to", "updated_at"])
        return Response(ContactMessageSerializer(msg, context={"request": request}).data)


class DemoRequestSerializer(serializers.ModelSerializer):
    contact_name = serializers.CharField(source="contact_message.name", read_only=True)
    contact_email = serializers.CharField(source="contact_message.email", read_only=True)
    product_name = serializers.CharField(source="product.name", read_only=True, allow_null=True)

    class Meta:
        model = DemoRequest
        fields = "__all__"


class DemoRequestViewSet(viewsets.ModelViewSet):
    serializer_class = DemoRequestSerializer
    permission_classes = [require_perm("demo_requests.view|demo_requests.manage")]

    def get_queryset(self):
        qs = DemoRequest.objects.select_related("contact_message", "product").all()
        return filter_by_product_scope(qs, self.request.user)

    def get_permissions(self):
        if self.action in ("create", "update", "partial_update", "destroy"):
            return [require_perm("demo_requests.manage")()]
        return super().get_permissions()
