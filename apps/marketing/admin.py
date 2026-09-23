from django.contrib import admin

from apps.marketing.models import BlogPost, ContactChannel, NewsletterSubscriber, SiteSetting


@admin.register(BlogPost)
class BlogPostAdmin(admin.ModelAdmin):
    list_display = ("title", "slug", "category", "is_published", "published_at", "updated_at")
    list_filter = ("is_published", "category")
    search_fields = ("title", "slug", "excerpt", "body")
    prepopulated_fields = {"slug": ("title",)}
    date_hierarchy = "published_at"


@admin.register(SiteSetting)
class SiteSettingAdmin(admin.ModelAdmin):
    list_display = ("key", "value")
    search_fields = ("key", "value")


@admin.register(ContactChannel)
class ContactChannelAdmin(admin.ModelAdmin):
    list_display = (
        "label",
        "channel_type",
        "value",
        "is_primary",
        "is_active",
        "display_order",
        "updated_at",
    )
    list_filter = ("channel_type", "is_active", "is_primary")
    list_editable = ("is_active", "is_primary", "display_order")
    search_fields = ("label", "value", "href", "description")
    ordering = ("display_order", "id")
    fieldsets = (
        (
            None,
            {
                "fields": (
                    "channel_type",
                    "label",
                    "value",
                    "href",
                    "description",
                    "is_primary",
                    "is_active",
                    "display_order",
                )
            },
        ),
    )


@admin.register(NewsletterSubscriber)
class NewsletterSubscriberAdmin(admin.ModelAdmin):
    list_display = ("email", "status", "product_interest", "subscribed_at", "created_at")
    list_filter = ("status",)
    search_fields = ("email", "product_interest", "provider_id")
    date_hierarchy = "created_at"
