from django.contrib import admin

from apps.products.models import (
    Product,
    ProductAudience,
    ProductCapability,
    ProductCapabilityGroup,
    ProductControl,
    ProductCustomSection,
    ProductFAQ,
    ProductHighlight,
    ProductImplementationStep,
    ProductIntegration,
    ProductMedia,
    ProductOutcome,
    ProductPageSection,
    ProductProblem,
    ProductWorkflowStep,
)


class OrderedTabularInline(admin.TabularInline):
    extra = 0
    ordering = ("display_order", "id")
    show_change_link = False


class ProductHighlightInline(OrderedTabularInline):
    model = ProductHighlight
    fields = ("title", "short_description", "icon", "display_order", "is_active")


class ProductAudienceInline(OrderedTabularInline):
    model = ProductAudience
    fields = ("title", "description", "icon", "display_order", "is_active")
    classes = ("collapse",)


class ProductProblemInline(OrderedTabularInline):
    model = ProductProblem
    fields = ("title", "description", "icon", "display_order", "is_active")
    classes = ("collapse",)


class ProductCapabilityGroupInline(OrderedTabularInline):
    model = ProductCapabilityGroup
    fields = ("title", "description", "icon", "display_order", "is_active")
    show_change_link = True
    verbose_name_plural = "capability groups (open a group to edit its capabilities)"


class ProductWorkflowStepInline(OrderedTabularInline):
    model = ProductWorkflowStep
    fields = ("step_number", "title", "description", "icon", "display_order", "is_active")
    classes = ("collapse",)


class ProductMediaInline(OrderedTabularInline):
    model = ProductMedia
    fields = ("title", "caption", "image", "media_type", "alt_text", "display_order", "is_active")
    classes = ("collapse",)


class ProductIntegrationInline(OrderedTabularInline):
    model = ProductIntegration
    fields = ("name", "description", "logo", "display_order", "is_active")
    classes = ("collapse",)


class ProductControlInline(OrderedTabularInline):
    model = ProductControl
    fields = ("title", "description", "icon", "display_order", "is_active")
    classes = ("collapse",)
    verbose_name_plural = "controls and security features"


class ProductOutcomeInline(OrderedTabularInline):
    model = ProductOutcome
    fields = ("title", "description", "display_order", "is_active")
    classes = ("collapse",)


class ProductImplementationStepInline(OrderedTabularInline):
    model = ProductImplementationStep
    fields = ("step_number", "title", "description", "display_order", "is_active")
    classes = ("collapse",)


class ProductFAQInline(OrderedTabularInline):
    model = ProductFAQ
    fields = ("question", "answer", "display_order", "is_active")
    classes = ("collapse",)


class ProductPageSectionInline(OrderedTabularInline):
    model = ProductPageSection
    fields = ("section_type", "title_override", "subtitle", "display_order", "is_enabled")
    ordering = ("display_order", "id")
    verbose_name_plural = "page sections (order and headings — content stays in the sections above)"


class ProductCustomSectionInline(OrderedTabularInline):
    model = ProductCustomSection
    fields = ("title", "subtitle", "body", "image", "layout_type", "display_order", "is_active")
    classes = ("collapse",)


class ProductCapabilityInline(OrderedTabularInline):
    model = ProductCapability
    fields = ("title", "description", "display_order", "is_active")


@admin.register(Product)
class ProductAdmin(admin.ModelAdmin):
    list_display = (
        "name",
        "slug",
        "group_key",
        "cta_type",
        "is_active",
        "is_featured",
        "order",
        "updated_at",
    )
    list_filter = ("is_active", "is_featured", "cta_type", "group_key", "market")
    search_fields = ("name", "slug", "code", "short", "description", "tagline")
    prepopulated_fields = {"slug": ("name",)}
    list_editable = ("order", "is_active", "is_featured")
    ordering = ("order", "name")
    fieldsets = (
        (
            "Identity",
            {
                "fields": (
                    "name",
                    "slug",
                    "tagline",
                    "short",
                    "description",
                    "market",
                    "type",
                    "code",
                    "icon",
                    "group_key",
                )
            },
        ),
        (
            "Images",
            {
                "fields": ("poster", "hero_image", "mobile_image"),
                "description": "Poster remains the card image. Hero and mobile images are optional detail-page art.",
            },
        ),
        (
            "Calls to action",
            {
                "fields": (
                    "cta_label",
                    "cta_type",
                    "url",
                    "secondary_cta_label",
                    "secondary_cta_type",
                    "secondary_cta_url",
                )
            },
        ),
        (
            "Publishing",
            {"fields": ("is_active", "is_featured", "order", "last_marketed_at")},
        ),
        (
            "SEO",
            {
                "fields": ("seo_title", "seo_description"),
                "classes": ("collapse",),
            },
        ),
    )
    inlines = [
        ProductHighlightInline,
        ProductAudienceInline,
        ProductProblemInline,
        ProductCapabilityGroupInline,
        ProductWorkflowStepInline,
        ProductMediaInline,
        ProductIntegrationInline,
        ProductControlInline,
        ProductOutcomeInline,
        ProductImplementationStepInline,
        ProductFAQInline,
        ProductCustomSectionInline,
        ProductPageSectionInline,
    ]


@admin.register(ProductCapabilityGroup)
class ProductCapabilityGroupAdmin(admin.ModelAdmin):
    list_display = ("title", "product", "display_order", "is_active")
    list_editable = ("display_order", "is_active")
    list_filter = ("is_active", "product")
    search_fields = ("title", "description", "product__name", "product__slug")
    autocomplete_fields = ("product",)
    ordering = ("product", "display_order", "id")
    inlines = [ProductCapabilityInline]
