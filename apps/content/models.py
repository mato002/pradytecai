from django.conf import settings
from django.db import models


class MediaAsset(models.Model):
    id = models.BigAutoField(primary_key=True)
    path = models.CharField(max_length=255)
    disk = models.CharField(max_length=255, default="public")
    mime = models.CharField(max_length=255, null=True, blank=True)
    original_name = models.CharField(max_length=255, null=True, blank=True)
    size = models.PositiveBigIntegerField(null=True, blank=True)
    uploaded_by = models.ForeignKey(
        settings.AUTH_USER_MODEL,
        null=True,
        blank=True,
        on_delete=models.SET_NULL,
        db_column="uploaded_by",
        related_name="uploaded_media",
    )
    created_at = models.DateTimeField(null=True, blank=True, auto_now_add=True)
    updated_at = models.DateTimeField(null=True, blank=True, auto_now=True)

    class Meta:
        db_table = "media_assets"


class ContentItem(models.Model):
    id = models.BigAutoField(primary_key=True)
    product = models.ForeignKey(
        "products.Product", null=True, blank=True, on_delete=models.SET_NULL, related_name="content_items"
    )
    campaign = models.ForeignKey(
        "campaigns.Campaign", null=True, blank=True, on_delete=models.SET_NULL, related_name="content_items"
    )
    author = models.ForeignKey(
        settings.AUTH_USER_MODEL,
        null=True,
        blank=True,
        on_delete=models.SET_NULL,
        related_name="authored_content",
        db_column="author_id",
    )
    approver = models.ForeignKey(
        settings.AUTH_USER_MODEL,
        null=True,
        blank=True,
        on_delete=models.SET_NULL,
        related_name="approved_content",
        db_column="approver_id",
    )
    status = models.CharField(max_length=255, default="draft")
    title = models.CharField(max_length=255, null=True, blank=True)
    caption = models.TextField(null=True, blank=True)
    body = models.TextField(null=True, blank=True)
    cta_label = models.CharField(max_length=255, null=True, blank=True)
    destination_url = models.CharField(max_length=255, null=True, blank=True)
    utm = models.JSONField(null=True, blank=True)
    scheduled_at = models.DateTimeField(null=True, blank=True)
    published_at = models.DateTimeField(null=True, blank=True)
    created_at = models.DateTimeField(null=True, blank=True, auto_now_add=True)
    updated_at = models.DateTimeField(null=True, blank=True, auto_now=True)
    media = models.ManyToManyField(MediaAsset, through="ContentItemMedia", related_name="content_items")

    class Meta:
        db_table = "content_items"
        ordering = ["-created_at"]


class ContentDestination(models.Model):
    id = models.BigAutoField(primary_key=True)
    content_item = models.ForeignKey(ContentItem, on_delete=models.CASCADE, related_name="destinations")
    social_account = models.ForeignKey(
        "social.SocialAccount", on_delete=models.CASCADE, related_name="destinations"
    )
    platform_override = models.JSONField(null=True, blank=True)
    status = models.CharField(max_length=255, default="pending")
    external_post_id = models.CharField(max_length=255, null=True, blank=True)
    published_at = models.DateTimeField(null=True, blank=True)
    failed_at = models.DateTimeField(null=True, blank=True)
    failure_message = models.TextField(null=True, blank=True)
    created_at = models.DateTimeField(null=True, blank=True, auto_now_add=True)
    updated_at = models.DateTimeField(null=True, blank=True, auto_now=True)

    class Meta:
        db_table = "content_destinations"


class ContentItemMedia(models.Model):
    id = models.BigAutoField(primary_key=True)
    content_item = models.ForeignKey(ContentItem, on_delete=models.CASCADE)
    media_asset = models.ForeignKey(MediaAsset, on_delete=models.CASCADE)
    sort_order = models.PositiveIntegerField(default=0)
    created_at = models.DateTimeField(null=True, blank=True, auto_now_add=True)
    updated_at = models.DateTimeField(null=True, blank=True, auto_now=True)

    class Meta:
        db_table = "content_item_media"
        ordering = ["sort_order"]
