from django.contrib import admin

from apps.careers.models import ApplicationComment, JobApplication, Position


class ApplicationCommentInline(admin.TabularInline):
    model = ApplicationComment
    extra = 0
    autocomplete_fields = ("user", "parent")
    readonly_fields = ("created_at", "updated_at")


@admin.register(Position)
class PositionAdmin(admin.ModelAdmin):
    list_display = ("title", "type", "location", "is_active", "order", "created_at")
    list_filter = ("is_active", "type", "location")
    search_fields = ("title", "description", "tags")
    list_editable = ("order", "is_active")


@admin.register(JobApplication)
class JobApplicationAdmin(admin.ModelAdmin):
    list_display = ("name", "email", "position", "status", "rating", "created_at")
    list_filter = ("status", "position")
    search_fields = ("name", "email", "phone", "cover_letter")
    autocomplete_fields = ("position",)
    inlines = [ApplicationCommentInline]
    date_hierarchy = "created_at"


@admin.register(ApplicationComment)
class ApplicationCommentAdmin(admin.ModelAdmin):
    list_display = ("application", "user", "created_at")
    search_fields = ("body",)
    autocomplete_fields = ("application", "user", "parent")
