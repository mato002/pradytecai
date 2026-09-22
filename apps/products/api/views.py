from rest_framework import serializers, viewsets
from rest_framework.decorators import action, api_view, permission_classes
from rest_framework.parsers import FormParser, JSONParser, MultiPartParser
from rest_framework.permissions import AllowAny
from rest_framework.response import Response

from apps.accounts.permissions import require_perm
from apps.core.visibility import can_access_product, filter_products_visible
from apps.products.models import Product, validate_product_poster


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
            "icon",
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
        if instance.poster:
            instance.poster.delete(save=False)
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


@api_view(["GET"])
@permission_classes([AllowAny])
def public_product_detail(request, slug):
    product = Product.objects.filter(slug=slug, is_active=True).first()
    if not product:
        return Response({"detail": "Not found."}, status=404)
    return Response(PublicProductSerializer(product, context={"request": request}).data)
