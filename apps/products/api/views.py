from django.db.models import Prefetch
from rest_framework import serializers, viewsets
from rest_framework.decorators import action, api_view, permission_classes
from rest_framework.parsers import FormParser, JSONParser, MultiPartParser
from rest_framework.permissions import AllowAny
from rest_framework.response import Response

from apps.accounts.permissions import require_perm
from apps.core.visibility import can_access_product, filter_products_visible
from apps.products.api.serializers import PublicProductDetailSerializer
from apps.products.models import (
    Product,
    ProductAudience,
    ProductCapability,
    ProductCapabilityGroup,
    ProductControl,
    ProductCustomSection,
    ProductFAQ,
    ProductHighlight,
    ProductImplementationStep,
    ProductIntegration,
    ProductMedia,
    ProductOutcome,
    ProductPageSection,
    ProductProblem,
    ProductWorkflowStep,
    validate_product_poster,
)


class ProductSerializer(serializers.ModelSerializer):
    poster_url = serializers.SerializerMethodField()
    short_description = serializers.CharField(source="short", required=False, allow_blank=True)
    display_order = serializers.IntegerField(source="order", required=False)
    cta_url = serializers.CharField(source="url", required=False, allow_blank=True, allow_null=True)

    class Meta:
        model = Product
        fields = [
            "id",
            "name",
            "slug",
            "description",
            "short",
            "short_description",
            "type",
            "market",
            "code",
            "url",
            "cta_url",
            "cta_label",
            "cta_type",
            "poster",
            "poster_url",
            "hero_image",
            "mobile_image",
            "tagline",
            "icon",
            "secondary_cta_label",
            "secondary_cta_url",
            "secondary_cta_type",
            "seo_title",
            "seo_description",
            "is_active",
            "is_featured",
            "order",
            "display_order",
            "group_key",
            "last_marketed_at",
            "created_at",
            "updated_at",
        ]
        read_only_fields = ["created_at", "updated_at", "poster_url"]
        extra_kwargs = {
            "poster": {"required": False, "allow_null": True},
            "hero_image": {"required": False, "allow_null": True},
            "mobile_image": {"required": False, "allow_null": True},
            "slug": {"required": False, "allow_blank": True},
        }

    def get_poster_url(self, obj):
        if not obj.poster:
            return None
        # Relative /media/… URL — Apache/Django serve same-origin media in production.
        return obj.poster.url

    def validate_poster(self, value):
        if value:
            validate_product_poster(value)
        return value

    def validate_hero_image(self, value):
        if value:
            validate_product_poster(value)
        return value

    def validate_mobile_image(self, value):
        if value:
            validate_product_poster(value)
        return value

    def validate_slug(self, value):
        if not value:
            return value
        qs = Product.objects.filter(slug=value)
        if self.instance:
            qs = qs.exclude(pk=self.instance.pk)
        if qs.exists():
            raise serializers.ValidationError("A product with this slug already exists.")
        return value


class PublicProductSerializer(serializers.ModelSerializer):
    poster_url = serializers.SerializerMethodField()
    short_description = serializers.CharField(source="short", read_only=True)
    display_order = serializers.IntegerField(source="order", read_only=True)
    cta_url = serializers.CharField(source="url", read_only=True)

    class Meta:
        model = Product
        fields = [
            "id",
            "name",
            "slug",
            "description",
            "short",
            "short_description",
            "market",
            "poster_url",
            "icon",
            "cta_label",
            "cta_type",
            "cta_url",
            "is_featured",
            "display_order",
            "group_key",
        ]

    def get_poster_url(self, obj):
        if not obj.poster:
            return None
        # Relative /media/… URL — Apache/Django serve same-origin media in production.
        return obj.poster.url


class ProductViewSet(viewsets.ModelViewSet):
    serializer_class = ProductSerializer
    permission_classes = [require_perm("products.view|products.manage")]
    parser_classes = [MultiPartParser, FormParser, JSONParser]
    lookup_field = "pk"

    def get_queryset(self):
        qs = Product.objects.all()
        return filter_products_visible(qs, self.request.user)

    def get_permissions(self):
        if self.action in ("create", "update", "partial_update", "destroy", "toggle_status", "upload_poster"):
            return [require_perm("products.manage")()]
        return super().get_permissions()

    def retrieve(self, request, *args, **kwargs):
        obj = self.get_object()
        if not can_access_product(request.user, obj.pk):
            return Response({"detail": "Forbidden"}, status=403)
        return super().retrieve(request, *args, **kwargs)

    def perform_destroy(self, instance):
        # FKs on ContactMessage / DemoRequest use SET_NULL — safe delete.
        for field_name in ("poster", "hero_image", "mobile_image"):
            image = getattr(instance, field_name, None)
            if image:
                image.delete(save=False)
        instance.delete()

    @action(detail=True, methods=["post"])
    def toggle_status(self, request, pk=None):
        product = self.get_object()
        if not can_access_product(request.user, product.pk):
            return Response({"detail": "Forbidden"}, status=403)
        product.is_active = not product.is_active
        product.save(update_fields=["is_active", "updated_at"])
        return Response(ProductSerializer(product, context={"request": request}).data)

    @action(detail=True, methods=["post"], url_path="upload-poster")
    def upload_poster(self, request, pk=None):
        product = self.get_object()
        if not can_access_product(request.user, product.pk):
            return Response({"detail": "Forbidden"}, status=403)
        poster = request.FILES.get("poster")
        if not poster:
            return Response({"detail": "poster file is required."}, status=400)
        try:
            validate_product_poster(poster)
        except Exception as exc:
            return Response({"detail": str(exc)}, status=400)
        if product.poster:
            product.poster.delete(save=False)
        product.poster = poster
        product.save(update_fields=["poster", "updated_at"])
        return Response(ProductSerializer(product, context={"request": request}).data)


@api_view(["GET"])
@permission_classes([AllowAny])
def public_product_list(request):
    qs = Product.objects.filter(is_active=True).order_by("order", "name")
    featured = request.query_params.get("featured")
    if featured in ("1", "true", "yes"):
        qs = qs.filter(is_featured=True)
    return Response(PublicProductSerializer(qs, many=True, context={"request": request}).data)


def _active_ordered(model):
    return model.objects.filter(is_active=True).order_by("display_order", "id")


def public_product_detail_queryset():
    """Active product plus active related rows, ordered. Used only by the detail endpoint."""
    return Product.objects.filter(is_active=True).prefetch_related(
        Prefetch("highlights", queryset=_active_ordered(ProductHighlight)),
        Prefetch("audiences", queryset=_active_ordered(ProductAudience)),
        Prefetch("problems", queryset=_active_ordered(ProductProblem)),
        Prefetch(
            "capability_groups",
            queryset=_active_ordered(ProductCapabilityGroup).prefetch_related(
                Prefetch("capabilities", queryset=_active_ordered(ProductCapability))
            ),
        ),
        Prefetch("workflow_steps", queryset=_active_ordered(ProductWorkflowStep)),
        Prefetch("media_items", queryset=ProductMedia.objects.publicly_visible()),
        Prefetch("integrations", queryset=_active_ordered(ProductIntegration)),
        Prefetch("controls", queryset=_active_ordered(ProductControl)),
        Prefetch("outcomes", queryset=_active_ordered(ProductOutcome)),
        Prefetch("implementation_steps", queryset=_active_ordered(ProductImplementationStep)),
        Prefetch("faqs", queryset=_active_ordered(ProductFAQ)),
        Prefetch("custom_sections", queryset=_active_ordered(ProductCustomSection)),
        Prefetch(
            "page_sections",
            queryset=ProductPageSection.objects.order_by("display_order", "id"),
        ),
    )


@api_view(["GET"])
@permission_classes([AllowAny])
def public_product_detail(request, slug):
    product = public_product_detail_queryset().filter(slug=slug).first()
    if not product:
        return Response({"detail": "Not found."}, status=404)
    return Response(PublicProductDetailSerializer(product, context={"request": request}).data)
