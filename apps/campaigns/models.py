from django.conf import settings
from django.db import models


class Campaign(models.Model):
    id = models.BigAutoField(primary_key=True)
    name = models.CharField(max_length=255)
    objective = models.CharField(max_length=255, null=True, blank=True)
    owner = models.ForeignKey(
        settings.AUTH_USER_MODEL,
        null=True,
        blank=True,
        on_delete=models.SET_NULL,
        related_name="owned_campaigns",
        db_column="owner_id",
    )
    starts_at = models.DateTimeField(null=True, blank=True)
    ends_at = models.DateTimeField(null=True, blank=True)
    status = models.CharField(max_length=255, default="draft")
    audience_notes = models.TextField(null=True, blank=True)
    budget_amount = models.DecimalField(max_digits=12, decimal_places=2, null=True, blank=True)
    utm_campaign = models.CharField(max_length=255, null=True, blank=True)
    created_at = models.DateTimeField(null=True, blank=True, auto_now_add=True)
    updated_at = models.DateTimeField(null=True, blank=True, auto_now=True)
    products = models.ManyToManyField(
        "products.Product", through="CampaignProduct", related_name="campaigns"
    )

    class Meta:
        db_table = "campaigns"
        ordering = ["-created_at"]

    def __str__(self):
        return self.name


class CampaignProduct(models.Model):
    id = models.BigAutoField(primary_key=True)
    campaign = models.ForeignKey(Campaign, on_delete=models.CASCADE)
    product = models.ForeignKey("products.Product", on_delete=models.CASCADE)
    created_at = models.DateTimeField(null=True, blank=True, auto_now_add=True)
    updated_at = models.DateTimeField(null=True, blank=True, auto_now=True)

    class Meta:
        db_table = "campaign_product"
        unique_together = (("campaign", "product"),)
