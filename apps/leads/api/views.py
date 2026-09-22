from django.utils import timezone
from rest_framework import serializers, viewsets
from rest_framework.decorators import action, api_view, permission_classes
from rest_framework.permissions import AllowAny
from rest_framework.response import Response

from apps.accounts.permissions import require_perm
from apps.core.visibility import filter_by_product_scope
from apps.leads.models import ContactMessage, DemoRequest, LeadCommunication
from apps.leads.tasks import send_lead_email, send_lead_sms, send_lead_whatsapp


class ContactMessageSerializer(serializers.ModelSerializer):
    class Meta:
        model = ContactMessage
        fields = "__all__"


class PublicContactSerializer(serializers.Serializer):
    name = serializers.CharField()
    company = serializers.CharField(required=False, allow_blank=True)
    email = serializers.EmailField()
    phone = serializers.CharField(required=False, allow_blank=True)
    topic = serializers.CharField(required=False, allow_blank=True)
    subject = serializers.CharField()
    message = serializers.CharField()
    request_type = serializers.CharField(required=False, allow_blank=True)
    product_id = serializers.IntegerField(required=False, allow_null=True)


@api_view(["POST"])
@permission_classes([AllowAny])
def public_contact(request):
    ser = PublicContactSerializer(data=request.data)
    ser.is_valid(raise_exception=True)
    data = ser.validated_data
    session = request.session
    msg = ContactMessage.objects.create(
        name=data["name"],
        company=data.get("company") or None,
        email=data["email"],
        phone=data.get("phone") or None,
        topic=data.get("topic") or None,
        subject=data["subject"],
        message=data["message"],
        request_type=data.get("request_type") or None,
        product_id=data.get("product_id"),
        source="website",
        utm_source=session.get("utm.utm_source"),
        utm_medium=session.get("utm.utm_medium"),
        utm_campaign=session.get("utm.utm_campaign"),
        utm_content=session.get("utm.utm_content"),
        utm_term=session.get("utm.utm_term"),
        status="new",
    )
    return Response({"id": msg.id, "detail": "Received."}, status=201)


class EnquiryViewSet(viewsets.ModelViewSet):
    serializer_class = ContactMessageSerializer
    permission_classes = [require_perm("leads.view|leads.manage")]

    def get_queryset(self):
        qs = ContactMessage.objects.select_related("product", "campaign").all()
        return filter_by_product_scope(qs, self.request.user)

    def get_permissions(self):
        if self.action in ("create", "update", "partial_update", "destroy", "reply", "assign"):
            return [require_perm("leads.manage|inbox.reply|leads.assign")()]
        return super().get_permissions()

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
        return Response(ContactMessageSerializer(msg).data)


class DemoRequestSerializer(serializers.ModelSerializer):
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
