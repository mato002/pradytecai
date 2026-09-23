from django.contrib import admin

from apps.leads.models import ContactMessage, DemoRequest, LeadCommunication


class LeadCommunicationInline(admin.TabularInline):
    model = LeadCommunication
    extra = 0
    readonly_fields = ("created_at", "updated_at")


class DemoRequestInline(admin.TabularInline):
    model = DemoRequest
    extra = 0
    readonly_fields = ("created_at", "updated_at")
    show_change_link = True


@admin.register(ContactMessage)
class ContactMessageAdmin(admin.ModelAdmin):
    list_display = (
        "name",
        "email",
        "subject",
        "status",
        "product",
        "assigned_to",
        "created_at",
    )
    list_filter = ("status", "request_type", "source")
    search_fields = ("name", "email", "company", "subject", "message")
    autocomplete_fields = (
        "product",
        "campaign",
        "content_destination",
        "assigned_to",
        "read_by",
        "responded_by",
    )
    readonly_fields = ("created_at", "updated_at")
    inlines = [LeadCommunicationInline, DemoRequestInline]
    date_hierarchy = "created_at"


@admin.register(LeadCommunication)
class LeadCommunicationAdmin(admin.ModelAdmin):
    list_display = ("contact_message", "channel", "subject", "status", "user", "created_at")
    list_filter = ("channel", "status")
    search_fields = ("subject", "body")
    autocomplete_fields = ("contact_message", "user")


@admin.register(DemoRequest)
class DemoRequestAdmin(admin.ModelAdmin):
    list_display = (
        "contact_message",
        "product",
        "request_type",
        "status",
        "preferred_at",
        "scheduled_at",
        "assigned_to",
    )
    list_filter = ("status", "request_type")
    autocomplete_fields = ("contact_message", "product", "campaign", "assigned_to")
