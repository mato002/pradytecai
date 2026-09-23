from django.contrib import admin

from apps.analytics.models import MarketingAlert, MetricSnapshot, TrackedLink, WebsiteEvent


@admin.register(MetricSnapshot)
class MetricSnapshotAdmin(admin.ModelAdmin):
    list_display = (
        "metric_key",
        "source",
        "measurable_type",
        "measurable_id",
        "value",
        "captured_at",
    )
    list_filter = ("source", "metric_key", "measurable_type")
    search_fields = ("metric_key", "raw_key", "measurable_type")
    date_hierarchy = "captured_at"


@admin.register(MarketingAlert)
class MarketingAlertAdmin(admin.ModelAdmin):
    list_display = ("title", "rule_key", "severity", "status", "product", "triggered_at")
    list_filter = ("severity", "status", "rule_key")
    search_fields = ("title", "message", "rule_key")
    autocomplete_fields = ("product", "social_account", "campaign")
    date_hierarchy = "triggered_at"


@admin.register(TrackedLink)
class TrackedLinkAdmin(admin.ModelAdmin):
    list_display = ("code", "destination_url", "product", "campaign", "click_count", "created_at")
    search_fields = ("code", "destination_url", "utm_campaign")
    autocomplete_fields = ("product", "campaign", "content_item")


@admin.register(WebsiteEvent)
class WebsiteEventAdmin(admin.ModelAdmin):
    list_display = ("event_name", "product", "campaign", "session_id", "occurred_at")
    list_filter = ("event_name",)
    search_fields = ("event_name", "session_id")
    autocomplete_fields = ("product", "campaign", "tracked_link")
    date_hierarchy = "occurred_at"
