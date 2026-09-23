from django.contrib import admin

from apps.campaigns.models import Campaign, CampaignProduct


class CampaignProductInline(admin.TabularInline):
    model = CampaignProduct
    extra = 0
    autocomplete_fields = ("product",)


@admin.register(Campaign)
class CampaignAdmin(admin.ModelAdmin):
    list_display = ("name", "status", "owner", "starts_at", "ends_at", "budget_amount", "created_at")
    list_filter = ("status",)
    search_fields = ("name", "objective", "utm_campaign")
    autocomplete_fields = ("owner",)
    inlines = [CampaignProductInline]
    date_hierarchy = "created_at"


@admin.register(CampaignProduct)
class CampaignProductAdmin(admin.ModelAdmin):
    list_display = ("campaign", "product", "created_at")
    autocomplete_fields = ("campaign", "product")
