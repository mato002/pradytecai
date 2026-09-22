from django.db import models

from apps.core.encryption import decrypt_laravel_string


class Integration(models.Model):
    id = models.BigAutoField(primary_key=True)
    provider = models.CharField(max_length=255)
    status = models.CharField(max_length=255, default="disconnected")
    external_account_name = models.CharField(max_length=255, null=True, blank=True)
    scopes = models.JSONField(null=True, blank=True)
    access_token = models.TextField(null=True, blank=True)
    refresh_token = models.TextField(null=True, blank=True)
    token_expires_at = models.DateTimeField(null=True, blank=True)
    last_sync_at = models.DateTimeField(null=True, blank=True)
    last_error = models.TextField(null=True, blank=True)
    meta = models.JSONField(null=True, blank=True)
    created_at = models.DateTimeField(null=True, blank=True, auto_now_add=True)
    updated_at = models.DateTimeField(null=True, blank=True, auto_now=True)

    class Meta:
        db_table = "integrations"

    def get_access_token_plain(self) -> str | None:
        """Decrypt Laravel-encrypted access_token when APP_KEY is available."""
        if not self.access_token:
            return None
        # Already plaintext (e.g. freshly written by Django) if not JSON/base64 payload
        if not self.access_token.startswith("eyJ") and '"iv"' not in self.access_token[:80]:
            # Laravel payloads are base64(json); try decrypt first, fall back to raw
            decrypted = decrypt_laravel_string(self.access_token)
            return decrypted if decrypted is not None else self.access_token
        return decrypt_laravel_string(self.access_token)

    def has_token_ciphertext(self) -> bool:
        return bool(self.access_token)


class SocialAccount(models.Model):
    id = models.BigAutoField(primary_key=True)
    integration = models.ForeignKey(
        Integration, null=True, blank=True, on_delete=models.SET_NULL, related_name="social_accounts"
    )
    product = models.ForeignKey(
        "products.Product", null=True, blank=True, on_delete=models.SET_NULL, related_name="social_accounts"
    )
    platform = models.CharField(max_length=255)
    name = models.CharField(max_length=255)
    external_id = models.CharField(max_length=255, null=True, blank=True)
    status = models.CharField(max_length=255, default="active")
    token_status = models.CharField(max_length=255, default="unknown")
    token_expires_at = models.DateTimeField(null=True, blank=True)
    last_sync_at = models.DateTimeField(null=True, blank=True)
    last_posted_at = models.DateTimeField(null=True, blank=True)
    follower_count = models.PositiveBigIntegerField(null=True, blank=True)
    can_publish = models.BooleanField(default=True)
    can_analytics = models.BooleanField(default=True)
    can_inbox = models.BooleanField(default=False)
    is_active = models.BooleanField(default=True)
    meta = models.JSONField(null=True, blank=True)
    created_at = models.DateTimeField(null=True, blank=True, auto_now_add=True)
    updated_at = models.DateTimeField(null=True, blank=True, auto_now=True)

    class Meta:
        db_table = "social_accounts"

    def __str__(self):
        return f"{self.platform}:{self.name}"
