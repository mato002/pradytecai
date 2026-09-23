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
    hero_image_url = serializers.SerializerMethodField()
    mobile_image_url = serializers.SerializerMethodField()
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
            "hero_image_url",
            "mobile_image",
            "mobile_image_url",
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
        read_only_fields = [
            "created_at",
            "updated_at",
            "poster_url",
            "hero_image_url",
            "mobile_image_url",
        ]
        extra_kwargs = {
            "poster": {"required": False, "allow_null": True},
            "hero_image": {"required": False, "allow_null": True},
            "mobile_image": {"required": False, "allow_null": True},
            "slug": {"required": False, "allow_blank": True},
        }

    def get_poster_url(self, obj):
        if not obj.poster:
            return None
        return obj.poster.url

    def get_hero_image_url(self, obj):
        if not obj.hero_image:
            return None
        return obj.hero_image.url

    def get_mobile_image_url(self, obj):
        if not obj.mobile_image:
            return None
        return obj.mobile_image.url

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


class AdminProductMediaSerializer(serializers.ModelSerializer):
    image_url = serializers.SerializerMethodField(read_only=True)
    video_file_url = serializers.SerializerMethodField(read_only=True)
    thumbnail_url = serializers.SerializerMethodField(read_only=True)

    class Meta:
        model = ProductMedia
        fields = [
            "id",
            "media_type",
            "image_category",
            "title",
            "caption",
            "image",
            "image_url",
            "video_source",
            "video_file",
            "video_file_url",
            "video_url",
            "thumbnail",
            "thumbnail_url",
            "alt_text",
            "is_featured",
            "display_order",
            "is_active",
            "mime_type",
            "file_size",
            "duration_seconds",
        ]
        read_only_fields = ["id", "image_url", "video_file_url", "thumbnail_url", "mime_type", "file_size"]
        extra_kwargs = {
            "image": {"required": False, "allow_null": True},
            "video_file": {"required": False, "allow_null": True},
            "thumbnail": {"required": False, "allow_null": True},
            "is_active": {"required": False, "default": True},
            "is_featured": {"required": False, "default": False},
            "display_order": {"required": False, "default": 0},
        }

    def get_image_url(self, obj):
        return obj.image.url if obj.image else None

    def get_video_file_url(self, obj):
        return obj.video_file.url if obj.video_file else None

    def get_thumbnail_url(self, obj):
        return obj.thumbnail.url if obj.thumbnail else None

    def validate(self, attrs):
        instance = getattr(self, "instance", None)
        media_type = attrs.get("media_type", getattr(instance, "media_type", ProductMedia.TYPE_IMAGE))
        merged = {
            "media_type": media_type,
            "image": attrs.get("image", getattr(instance, "image", None)),
            "video_source": attrs.get("video_source", getattr(instance, "video_source", None)),
            "video_file": attrs.get("video_file", getattr(instance, "video_file", None)),
            "video_url": attrs.get("video_url", getattr(instance, "video_url", None)),
        }
        product = getattr(instance, "product", None) or self.context.get("product")
        if not product:
            return attrs
        temp = ProductMedia(
            product=product,
            media_type=merged["media_type"],
            image=merged["image"],
            video_source=merged["video_source"],
            video_file=merged["video_file"],
            video_url=merged["video_url"],
        )
        try:
            temp.clean()
        except Exception as exc:
            if hasattr(exc, "message_dict"):
                raise serializers.ValidationError(exc.message_dict)
            raise serializers.ValidationError(str(exc))
        return attrs


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
        if self.action in (
            "create",
            "update",
            "partial_update",
            "destroy",
            "toggle_status",
            "upload_poster",
            "media",
            "media_detail",
        ):
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

    @action(detail=True, methods=["get", "post"], url_path="media")
    def media(self, request, pk=None):
        product = self.get_object()
        if not can_access_product(request.user, product.pk):
            return Response({"detail": "Forbidden"}, status=403)
        if request.method == "GET":
            qs = product.media_items.all().order_by("display_order", "id")
            return Response(AdminProductMediaSerializer(qs, many=True, context={"request": request}).data)

        serializer = AdminProductMediaSerializer(
            data=request.data, context={"request": request, "product": product}
        )
        serializer.is_valid(raise_exception=True)
        item = serializer.save(product=product)
        return Response(
            AdminProductMediaSerializer(item, context={"request": request}).data,
            status=201,
        )

    @action(detail=True, methods=["patch", "delete"], url_path=r"media/(?P<media_id>[^/.]+)")
    def media_detail(self, request, pk=None, media_id=None):
        product = self.get_object()
        if not can_access_product(request.user, product.pk):
            return Response({"detail": "Forbidden"}, status=403)
        item = product.media_items.filter(pk=media_id).first()
        if not item:
            return Response({"detail": "Not found."}, status=404)

        if request.method == "DELETE":
            for field_name in ("image", "video_file", "thumbnail"):
                f = getattr(item, field_name, None)
                if f:
                    f.delete(save=False)
            item.delete()
            return Response(status=204)

        serializer = AdminProductMediaSerializer(
            item, data=request.data, partial=True, context={"request": request}
        )
        serializer.is_valid(raise_exception=True)
        item = serializer.save()
        return Response(AdminProductMediaSerializer(item, context={"request": request}).data)


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
