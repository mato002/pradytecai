from django.contrib import admin

from apps.social.models import Integration, SocialAccount


@admin.register(Integration)
class IntegrationAdmin(admin.ModelAdmin):
    list_display = (
        "provider",
        "status",
        "external_account_name",
        "token_expires_at",
        "last_sync_at",
        "updated_at",
    )
    list_filter = ("provider", "status")
    search_fields = ("provider", "external_account_name")
    readonly_fields = ("created_at", "updated_at")
    fieldsets = (
        (None, {"fields": ("provider", "status", "external_account_name", "scopes")}),
        (
            "Tokens",
            {
                "classes": ("collapse",),
                "fields": ("access_token", "refresh_token", "token_expires_at"),
            },
        ),
        ("Sync", {"fields": ("last_sync_at", "last_error", "meta")}),
        ("Timestamps", {"fields": ("created_at", "updated_at")}),
    )


@admin.register(SocialAccount)
class SocialAccountAdmin(admin.ModelAdmin):
    list_display = (
        "name",
        "platform",
        "status",
        "product",
        "is_active",
        "follower_count",
        "last_posted_at",
    )
    list_filter = ("platform", "status", "is_active", "token_status")
    search_fields = ("name", "external_id", "platform")
    autocomplete_fields = ("integration", "product")
