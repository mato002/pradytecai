from rest_framework import serializers, viewsets
from rest_framework.decorators import action
from rest_framework.response import Response

from apps.accounts.permissions import require_perm
from apps.core.visibility import can_access_product, filter_products_visible
from apps.products.models import Product


class ProductSerializer(serializers.ModelSerializer):
    class Meta:
        model = Product
        fields = "__all__"


class ProductViewSet(viewsets.ModelViewSet):
    serializer_class = ProductSerializer
    permission_classes = [require_perm("products.view|products.manage")]

    def get_queryset(self):
        qs = Product.objects.all()
        return filter_products_visible(qs, self.request.user)

    def get_permissions(self):
        if self.action in ("create", "update", "partial_update", "destroy", "toggle_status"):
            return [require_perm("products.manage")()]
        return super().get_permissions()

    def retrieve(self, request, *args, **kwargs):
        obj = self.get_object()
        if not can_access_product(request.user, obj.pk):
            return Response({"detail": "Forbidden"}, status=403)
        return super().retrieve(request, *args, **kwargs)

    @action(detail=True, methods=["post"])
    def toggle_status(self, request, pk=None):
        product = self.get_object()
        if not can_access_product(request.user, product.pk):
            return Response({"detail": "Forbidden"}, status=403)
        product.is_active = not product.is_active
        product.save(update_fields=["is_active", "updated_at"])
        return Response(ProductSerializer(product).data)
