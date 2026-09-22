from django.db import models
from rest_framework import serializers, viewsets

from apps.accounts.permissions import require_perm
from apps.campaigns.models import Campaign, CampaignProduct
from apps.core.visibility import scoped_product_ids


class CampaignSerializer(serializers.ModelSerializer):
    product_ids = serializers.SerializerMethodField()

    class Meta:
        model = Campaign
        fields = [
            "id",
            "name",
            "objective",
            "owner",
            "starts_at",
            "ends_at",
            "status",
            "audience_notes",
            "budget_amount",
            "utm_campaign",
            "created_at",
            "updated_at",
            "product_ids",
        ]

    def get_product_ids(self, obj):
        return list(obj.products.values_list("id", flat=True))


class CampaignViewSet(viewsets.ModelViewSet):
    serializer_class = CampaignSerializer
    permission_classes = [
        require_perm("campaigns.view|campaigns.create|campaigns.edit|campaigns.manage")
    ]

    def get_queryset(self):
        qs = Campaign.objects.prefetch_related("products").all()
        ids = scoped_product_ids(self.request.user)
        if ids is None:
            return qs
        return qs.filter(
            models.Q(products__isnull=True) | models.Q(products__id__in=ids)
        ).distinct()

    def get_permissions(self):
        if self.action == "create":
            return [require_perm("campaigns.create|campaigns.manage")()]
        if self.action in ("update", "partial_update", "destroy"):
            return [require_perm("campaigns.edit|campaigns.manage")()]
        return super().get_permissions()

    def perform_create(self, serializer):
        campaign = serializer.save(owner=self.request.user)
        for pid in self.request.data.get("product_ids") or []:
            CampaignProduct.objects.get_or_create(campaign=campaign, product_id=pid)
