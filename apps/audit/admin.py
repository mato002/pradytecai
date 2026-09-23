from django.contrib import admin

from apps.audit.models import ActivityLog


@admin.register(ActivityLog)
class ActivityLogAdmin(admin.ModelAdmin):
    list_display = ("action", "user", "model_type", "model_id", "description", "created_at")
    list_filter = ("action", "model_type")
    search_fields = ("action", "description", "ip_address", "user_agent")
    autocomplete_fields = ("user",)
    readonly_fields = (
        "user",
        "action",
        "model_type",
        "model_id",
        "description",
        "changes",
        "ip_address",
        "user_agent",
        "created_at",
        "updated_at",
    )
    date_hierarchy = "created_at"

    def has_add_permission(self, request):
        return False

    def has_change_permission(self, request, obj=None):
        return False
