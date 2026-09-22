from django.conf import settings
from django.db import models


class MarketingTask(models.Model):
    """
    related_type / related_id are Laravel morph columns — store as strings/ids,
    not Django ContentType.
    """

    id = models.BigAutoField(primary_key=True)
    assignee = models.ForeignKey(
        settings.AUTH_USER_MODEL,
        null=True,
        blank=True,
        on_delete=models.SET_NULL,
        related_name="marketing_tasks",
        db_column="assignee_id",
    )
    product = models.ForeignKey(
        "products.Product", null=True, blank=True, on_delete=models.SET_NULL, related_name="tasks"
    )
    campaign = models.ForeignKey(
        "campaigns.Campaign", null=True, blank=True, on_delete=models.SET_NULL, related_name="tasks"
    )
    type = models.CharField(max_length=255)
    title = models.CharField(max_length=255)
    notes = models.TextField(null=True, blank=True)
    due_at = models.DateTimeField(null=True, blank=True)
    status = models.CharField(max_length=255, default="open")
    priority = models.CharField(max_length=255, default="normal")
    related_type = models.CharField(max_length=255, null=True, blank=True)
    related_id = models.PositiveBigIntegerField(null=True, blank=True)
    created_at = models.DateTimeField(null=True, blank=True, auto_now_add=True)
    updated_at = models.DateTimeField(null=True, blank=True, auto_now=True)

    class Meta:
        db_table = "marketing_tasks"
        ordering = ["-created_at"]
