from django.contrib import admin

from apps.tasks.models import MarketingTask


@admin.register(MarketingTask)
class MarketingTaskAdmin(admin.ModelAdmin):
    list_display = (
        "title",
        "type",
        "status",
        "priority",
        "assignee",
        "product",
        "due_at",
        "created_at",
    )
    list_filter = ("status", "priority", "type")
    search_fields = ("title", "notes", "type")
    autocomplete_fields = ("assignee", "product", "campaign")
    date_hierarchy = "created_at"
