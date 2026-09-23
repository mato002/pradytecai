from django.contrib import admin

from apps.content.models import ContentDestination, ContentItem, ContentItemMedia, MediaAsset


class ContentItemMediaInline(admin.TabularInline):
    model = ContentItemMedia
    extra = 0
    autocomplete_fields = ("media_asset",)


class ContentDestinationInline(admin.TabularInline):
    model = ContentDestination
    extra = 0
    autocomplete_fields = ("social_account",)
    show_change_link = True


@admin.register(MediaAsset)
class MediaAssetAdmin(admin.ModelAdmin):
    list_display = ("id", "original_name", "path", "mime", "size", "uploaded_by", "created_at")
    search_fields = ("path", "original_name", "mime")
    autocomplete_fields = ("uploaded_by",)


@admin.register(ContentItem)
class ContentItemAdmin(admin.ModelAdmin):
    list_display = ("title", "status", "product", "campaign", "author", "scheduled_at", "published_at")
    list_filter = ("status",)
    search_fields = ("title", "caption", "body")
    autocomplete_fields = ("product", "campaign", "author", "approver")
    inlines = [ContentItemMediaInline, ContentDestinationInline]
    date_hierarchy = "created_at"


@admin.register(ContentDestination)
class ContentDestinationAdmin(admin.ModelAdmin):
    list_display = (
        "content_item",
        "social_account",
        "status",
        "external_post_id",
        "published_at",
        "failed_at",
    )
    list_filter = ("status",)
    search_fields = ("external_post_id", "failure_message", "status")
    autocomplete_fields = ("content_item", "social_account")


@admin.register(ContentItemMedia)
class ContentItemMediaAdmin(admin.ModelAdmin):
    list_display = ("content_item", "media_asset", "sort_order")
    autocomplete_fields = ("content_item", "media_asset")
