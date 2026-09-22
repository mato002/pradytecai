from django.conf import settings
from django.db import models


class Position(models.Model):
    id = models.BigAutoField(primary_key=True)
    title = models.CharField(max_length=255)
    description = models.TextField()
    type = models.CharField(max_length=255, default="Full-time")
    location = models.CharField(max_length=255, default="Remote")
    tags = models.CharField(max_length=255, null=True, blank=True)
    is_active = models.BooleanField(default=True)
    order = models.IntegerField(default=0)
    created_at = models.DateTimeField(null=True, blank=True, auto_now_add=True)
    updated_at = models.DateTimeField(null=True, blank=True, auto_now=True)

    class Meta:
        db_table = "positions"
        ordering = ["order", "-created_at"]

    def __str__(self):
        return self.title


class JobApplication(models.Model):
    id = models.BigAutoField(primary_key=True)
    position = models.ForeignKey(Position, on_delete=models.CASCADE, related_name="applications")
    name = models.CharField(max_length=255)
    email = models.CharField(max_length=255)
    phone = models.CharField(max_length=255, null=True, blank=True)
    cover_letter = models.TextField()
    resume_path = models.CharField(max_length=255, null=True, blank=True)
    status = models.CharField(max_length=255, default="pending")
    interview_scheduled_at = models.DateTimeField(null=True, blank=True)
    interview_notes = models.TextField(null=True, blank=True)
    admin_notes = models.TextField(null=True, blank=True)
    rating = models.IntegerField(null=True, blank=True)
    created_at = models.DateTimeField(null=True, blank=True, auto_now_add=True)
    updated_at = models.DateTimeField(null=True, blank=True, auto_now=True)

    class Meta:
        db_table = "job_applications"
        ordering = ["-created_at"]


class ApplicationComment(models.Model):
    id = models.BigAutoField(primary_key=True)
    application = models.ForeignKey(
        JobApplication, on_delete=models.CASCADE, related_name="comments"
    )
    user = models.ForeignKey(
        settings.AUTH_USER_MODEL, null=True, blank=True, on_delete=models.SET_NULL
    )
    parent = models.ForeignKey(
        "self", null=True, blank=True, on_delete=models.CASCADE, related_name="replies"
    )
    body = models.TextField()
    created_at = models.DateTimeField(null=True, blank=True, auto_now_add=True)
    updated_at = models.DateTimeField(null=True, blank=True, auto_now=True)

    class Meta:
        db_table = "application_comments"
