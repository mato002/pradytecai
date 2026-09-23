from django.contrib import admin

from apps.products.models import Product


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
    search_fields = ("name", "slug", "code", "short", "description")
    prepopulated_fields = {"slug": ("name",)}
    list_editable = ("order", "is_active", "is_featured")
    ordering = ("order", "name")
