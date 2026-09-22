from django.db import models


class MetricSnapshot(models.Model):
    """
    Laravel morph columns store FQCNs such as ``App\\Models\\SocialAccount``.
    Do NOT use Django ContentType — preserve measurable_type strings as-is.
    """

    LARAVEL_SOCIAL_ACCOUNT = r"App\Models\SocialAccount"
    LARAVEL_CAMPAIGN = r"App\Models\Campaign"

    id = models.BigAutoField(primary_key=True)
    measurable_type = models.CharField(max_length=255)
    measurable_id = models.PositiveBigIntegerField()
    source = models.CharField(max_length=255)
    metric_key = models.CharField(max_length=255)
    raw_key = models.CharField(max_length=255, null=True, blank=True)
    value = models.DecimalField(max_digits=16, decimal_places=4, default=0)
    captured_at = models.DateTimeField()
    created_at = models.DateTimeField(null=True, blank=True, auto_now_add=True)
    updated_at = models.DateTimeField(null=True, blank=True, auto_now=True)

    class Meta:
        db_table = "metric_snapshots"
        unique_together = (
            ("measurable_type", "measurable_id", "source", "metric_key", "captured_at"),
        )


class MarketingAlert(models.Model):
    id = models.BigAutoField(primary_key=True)
    rule_key = models.CharField(max_length=255)
    severity = models.CharField(max_length=255)
    product = models.ForeignKey(
        "products.Product", null=True, blank=True, on_delete=models.SET_NULL, related_name="alerts"
    )
    social_account = models.ForeignKey(
        "social.SocialAccount", null=True, blank=True, on_delete=models.SET_NULL, related_name="alerts"
    )
    campaign = models.ForeignKey(
        "campaigns.Campaign", null=True, blank=True, on_delete=models.SET_NULL, related_name="alerts"
    )
    status = models.CharField(max_length=255, default="open")
    title = models.CharField(max_length=255)
    message = models.TextField(null=True, blank=True)
    triggered_at = models.DateTimeField()
    resolved_at = models.DateTimeField(null=True, blank=True)
    payload = models.JSONField(null=True, blank=True)
    created_at = models.DateTimeField(null=True, blank=True, auto_now_add=True)
    updated_at = models.DateTimeField(null=True, blank=True, auto_now=True)

    class Meta:
        db_table = "marketing_alerts"


class TrackedLink(models.Model):
    id = models.BigAutoField(primary_key=True)
    code = models.CharField(max_length=255, unique=True)
    destination_url = models.CharField(max_length=255)
    product = models.ForeignKey(
        "products.Product", null=True, blank=True, on_delete=models.SET_NULL, related_name="tracked_links"
    )
    campaign = models.ForeignKey(
        "campaigns.Campaign", null=True, blank=True, on_delete=models.SET_NULL, related_name="tracked_links"
    )
    content_item = models.ForeignKey(
        "content.ContentItem", null=True, blank=True, on_delete=models.SET_NULL, related_name="tracked_links"
    )
    utm_source = models.CharField(max_length=255, null=True, blank=True)
    utm_medium = models.CharField(max_length=255, null=True, blank=True)
    utm_campaign = models.CharField(max_length=255, null=True, blank=True)
    utm_content = models.CharField(max_length=255, null=True, blank=True)
    utm_term = models.CharField(max_length=255, null=True, blank=True)
    click_count = models.PositiveBigIntegerField(default=0)
    created_at = models.DateTimeField(null=True, blank=True, auto_now_add=True)
    updated_at = models.DateTimeField(null=True, blank=True, auto_now=True)

    class Meta:
        db_table = "tracked_links"


class WebsiteEvent(models.Model):
    id = models.BigAutoField(primary_key=True)
    event_name = models.CharField(max_length=255)
    product = models.ForeignKey(
        "products.Product", null=True, blank=True, on_delete=models.SET_NULL, related_name="website_events"
    )
    campaign = models.ForeignKey(
        "campaigns.Campaign", null=True, blank=True, on_delete=models.SET_NULL, related_name="website_events"
    )
    tracked_link = models.ForeignKey(
        TrackedLink, null=True, blank=True, on_delete=models.SET_NULL, related_name="website_events"
    )
    session_id = models.CharField(max_length=255, null=True, blank=True)
    occurred_at = models.DateTimeField()
    meta = models.JSONField(null=True, blank=True)
    created_at = models.DateTimeField(null=True, blank=True, auto_now_add=True)
    updated_at = models.DateTimeField(null=True, blank=True, auto_now=True)

    class Meta:
        db_table = "website_events"
