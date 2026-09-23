from django import forms
from django.contrib import admin
from django.contrib.auth.forms import ReadOnlyPasswordHashField

from apps.accounts.models import (
    ModelHasPermission,
    ModelHasRole,
    Permission,
    Role,
    RoleHasPermission,
    User,
    UserAccessScope,
)


class UserCreationForm(forms.ModelForm):
    password1 = forms.CharField(label="Password", widget=forms.PasswordInput)
    password2 = forms.CharField(label="Password confirmation", widget=forms.PasswordInput)

    class Meta:
        model = User
        fields = ("email", "name", "role", "is_super_admin")

    def clean_password2(self):
        password1 = self.cleaned_data.get("password1")
        password2 = self.cleaned_data.get("password2")
        if password1 and password2 and password1 != password2:
            raise forms.ValidationError("Passwords do not match.")
        return password2

    def save(self, commit=True):
        user = super().save(commit=False)
        user.set_password(self.cleaned_data["password1"])
        if commit:
            user.save()
        return user


class UserChangeForm(forms.ModelForm):
    password = ReadOnlyPasswordHashField(
        help_text="Raw passwords are not stored. Use “Change password” below to set a new one."
    )

    class Meta:
        model = User
        fields = (
            "email",
            "name",
            "password",
            "role",
            "is_super_admin",
            "email_verified_at",
            "remember_token",
        )


@admin.register(User)
class UserAdmin(admin.ModelAdmin):
    add_form = UserCreationForm
    form = UserChangeForm
    list_display = ("email", "name", "role", "is_super_admin", "created_at")
    list_filter = ("role", "is_super_admin")
    search_fields = ("email", "name")
    ordering = ("email",)
    readonly_fields = ("created_at", "updated_at")

    fieldsets = (
        (None, {"fields": ("email", "password")}),
        ("Profile", {"fields": ("name", "role", "is_super_admin", "email_verified_at")}),
        ("Tokens", {"fields": ("remember_token",), "classes": ("collapse",)}),
        ("Timestamps", {"fields": ("created_at", "updated_at")}),
    )
    add_fieldsets = (
        (
            None,
            {
                "classes": ("wide",),
                "fields": ("email", "name", "role", "is_super_admin", "password1", "password2"),
            },
        ),
    )

    def get_form(self, request, obj=None, **kwargs):
        defaults = {"form": self.add_form if obj is None else self.form}
        defaults.update(kwargs)
        return super().get_form(request, obj, **defaults)

    def get_fieldsets(self, request, obj=None):
        if obj is None:
            return self.add_fieldsets
        return super().get_fieldsets(request, obj)

    def save_model(self, request, obj, form, change):
        super().save_model(request, obj, form, change)
        if obj.is_super_admin or obj.role == "super_admin":
            role = Role.objects.filter(name="super_admin", guard_name="web").first()
            if role:
                obj.assign_role(role)


@admin.register(Permission)
class PermissionAdmin(admin.ModelAdmin):
    list_display = ("name", "guard_name", "created_at")
    search_fields = ("name",)
    list_filter = ("guard_name",)


@admin.register(Role)
class RoleAdmin(admin.ModelAdmin):
    list_display = ("name", "guard_name", "created_at")
    search_fields = ("name",)
    list_filter = ("guard_name",)


@admin.register(RoleHasPermission)
class RoleHasPermissionAdmin(admin.ModelAdmin):
    list_display = ("role", "permission")
    list_filter = ("role",)
    autocomplete_fields = ("role", "permission")


@admin.register(ModelHasRole)
class ModelHasRoleAdmin(admin.ModelAdmin):
    list_display = ("role", "model_type", "model_id")
    list_filter = ("role", "model_type")
    autocomplete_fields = ("role",)


@admin.register(ModelHasPermission)
class ModelHasPermissionAdmin(admin.ModelAdmin):
    list_display = ("permission", "model_type", "model_id")
    list_filter = ("model_type",)
    autocomplete_fields = ("permission",)


@admin.register(UserAccessScope)
class UserAccessScopeAdmin(admin.ModelAdmin):
    list_display = ("user", "scope_type", "scope_id", "created_at")
    list_filter = ("scope_type",)
    search_fields = ("user__email",)
    autocomplete_fields = ("user",)
