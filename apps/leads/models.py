from django.conf import settings
from django.db import models


class ContactMessage(models.Model):
    id = models.BigAutoField(primary_key=True)
    product = models.ForeignKey(
        "products.Product", null=True, blank=True, on_delete=models.SET_NULL, related_name="leads"
    )
    campaign = models.ForeignKey(
        "campaigns.Campaign", null=True, blank=True, on_delete=models.SET_NULL, related_name="leads"
    )
    content_destination = models.ForeignKey(
        "content.ContentDestination",
        null=True,
        blank=True,
        on_delete=models.SET_NULL,
        related_name="contact_messages",
    )
    name = models.CharField(max_length=255)
    company = models.CharField(max_length=255, null=True, blank=True)
    email = models.CharField(max_length=255)
    phone = models.CharField(max_length=255, null=True, blank=True)
    topic = models.CharField(max_length=255, null=True, blank=True)
    request_type = models.CharField(max_length=255, null=True, blank=True)
    subject = models.CharField(max_length=255)
    message = models.TextField()
    status = models.CharField(max_length=255, default="new")
    admin_notes = models.TextField(null=True, blank=True)
    read_at = models.DateTimeField(null=True, blank=True)
    responded_at = models.DateTimeField(null=True, blank=True)
    source = models.CharField(max_length=255, null=True, blank=True)
    referrer = models.CharField(max_length=255, null=True, blank=True)
    landing_page = models.CharField(max_length=255, null=True, blank=True)
    utm_source = models.CharField(max_length=255, null=True, blank=True)
    utm_medium = models.CharField(max_length=255, null=True, blank=True)
    utm_campaign = models.CharField(max_length=255, null=True, blank=True)
    utm_content = models.CharField(max_length=255, null=True, blank=True)
    utm_term = models.CharField(max_length=255, null=True, blank=True)
    assigned_to = models.ForeignKey(
        settings.AUTH_USER_MODEL,
        null=True,
        blank=True,
        on_delete=models.SET_NULL,
        related_name="assigned_leads",
        db_column="assigned_to",
    )
    read_by = models.ForeignKey(
        settings.AUTH_USER_MODEL,
        null=True,
        blank=True,
        on_delete=models.SET_NULL,
        related_name="read_leads",
        db_column="read_by",
    )
    responded_by = models.ForeignKey(
        settings.AUTH_USER_MODEL,
        null=True,
        blank=True,
        on_delete=models.SET_NULL,
        related_name="responded_leads",
        db_column="responded_by",
    )
    next_follow_up_at = models.DateTimeField(null=True, blank=True)
    first_responded_at = models.DateTimeField(null=True, blank=True)
    lead_value = models.DecimalField(max_digits=12, decimal_places=2, null=True, blank=True)
    created_at = models.DateTimeField(null=True, blank=True, auto_now_add=True)
    updated_at = models.DateTimeField(null=True, blank=True, auto_now=True)

    class Meta:
        db_table = "contact_messages"
        ordering = ["-created_at"]


class LeadCommunication(models.Model):
    id = models.BigAutoField(primary_key=True)
    contact_message = models.ForeignKey(
        ContactMessage, on_delete=models.CASCADE, related_name="communications"
    )
    user = models.ForeignKey(
        settings.AUTH_USER_MODEL, null=True, blank=True, on_delete=models.SET_NULL
    )
    channel = models.CharField(max_length=255, default="email")
    subject = models.CharField(max_length=255, null=True, blank=True)
    body = models.TextField()
    status = models.CharField(max_length=255, default="sent")
    created_at = models.DateTimeField(null=True, blank=True, auto_now_add=True)
    updated_at = models.DateTimeField(null=True, blank=True, auto_now=True)

    class Meta:
        db_table = "lead_communications"


class DemoRequest(models.Model):
    id = models.BigAutoField(primary_key=True)
    contact_message = models.ForeignKey(
        ContactMessage, on_delete=models.CASCADE, related_name="demo_requests"
    )
    product = models.ForeignKey(
        "products.Product", null=True, blank=True, on_delete=models.SET_NULL, related_name="demos"
    )
    campaign = models.ForeignKey(
        "campaigns.Campaign", null=True, blank=True, on_delete=models.SET_NULL, related_name="demos"
    )
    request_type = models.CharField(max_length=255, default="demo")
    preferred_at = models.DateTimeField(null=True, blank=True)
    scheduled_at = models.DateTimeField(null=True, blank=True)
    assigned_to = models.ForeignKey(
        settings.AUTH_USER_MODEL,
        null=True,
        blank=True,
        on_delete=models.SET_NULL,
        related_name="assigned_demos",
        db_column="assigned_to",
    )
    status = models.CharField(max_length=255, default="requested")
    notes = models.TextField(null=True, blank=True)
    created_at = models.DateTimeField(null=True, blank=True, auto_now_add=True)
    updated_at = models.DateTimeField(null=True, blank=True, auto_now=True)

    class Meta:
        db_table = "demo_requests"
