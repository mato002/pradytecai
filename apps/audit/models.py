from django.conf import settings
from django.db import models


class ActivityLog(models.Model):
    id = models.BigAutoField(primary_key=True)
    user = models.ForeignKey(
        settings.AUTH_USER_MODEL, null=True, blank=True, on_delete=models.SET_NULL, related_name="activity_logs"
    )
    action = models.CharField(max_length=255)
    model_type = models.CharField(max_length=255, null=True, blank=True)
    model_id = models.PositiveBigIntegerField(null=True, blank=True)
    description = models.CharField(max_length=255, null=True, blank=True)
    changes = models.JSONField(null=True, blank=True)
    ip_address = models.CharField(max_length=45, null=True, blank=True)
    user_agent = models.CharField(max_length=255, null=True, blank=True)
    created_at = models.DateTimeField(null=True, blank=True, auto_now_add=True)
    updated_at = models.DateTimeField(null=True, blank=True, auto_now=True)

    class Meta:
        db_table = "activity_logs"
        ordering = ["-created_at"]
