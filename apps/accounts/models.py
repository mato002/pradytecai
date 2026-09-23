"""Accounts models mapped to existing Laravel / Spatie tables."""

from __future__ import annotations

from django.contrib.auth.models import AbstractBaseUser, BaseUserManager
from django.db import models

from apps.accounts.hashers import LaravelBcryptPasswordHasher, is_laravel_bcrypt


class UserManager(BaseUserManager):
    def create_user(self, email, password=None, **extra):
        if not email:
            raise ValueError("Email is required")
        email = self.normalize_email(email)
        user = self.model(email=email, **extra)
        user.set_password(password)
        user.save(using=self._db)
        return user

    def create_superuser(self, email, password=None, **extra):
        extra.setdefault("is_super_admin", True)
        extra.setdefault("role", "super_admin")
        user = self.create_user(email, password, **extra)
        role = Role.objects.filter(name="super_admin", guard_name="web").first()
        if role:
            user.assign_role(role)
        return user


class Permission(models.Model):
    id = models.BigAutoField(primary_key=True)
    name = models.CharField(max_length=255)
    guard_name = models.CharField(max_length=255, default="web")
    created_at = models.DateTimeField(null=True, blank=True)
    updated_at = models.DateTimeField(null=True, blank=True)

    class Meta:
        db_table = "permissions"
        unique_together = (("name", "guard_name"),)

    def __str__(self):
        return self.name


class Role(models.Model):
    id = models.BigAutoField(primary_key=True)
    name = models.CharField(max_length=255)
    guard_name = models.CharField(max_length=255, default="web")
    created_at = models.DateTimeField(null=True, blank=True)
    updated_at = models.DateTimeField(null=True, blank=True)

    class Meta:
        db_table = "roles"
        unique_together = (("name", "guard_name"),)

    def __str__(self):
        return self.name

    def permission_names(self):
        return list(
            Permission.objects.filter(
                id__in=RoleHasPermission.objects.filter(role=self).values_list(
                    "permission_id", flat=True
                )
            ).values_list("name", flat=True)
        )


class RoleHasPermission(models.Model):
    permission = models.ForeignKey(Permission, on_delete=models.CASCADE, db_column="permission_id")
    role = models.ForeignKey(Role, on_delete=models.CASCADE, db_column="role_id")

    class Meta:
        db_table = "role_has_permissions"
        unique_together = (("permission", "role"),)


class ModelHasRole(models.Model):
    role = models.ForeignKey(Role, on_delete=models.CASCADE, db_column="role_id")
    model_type = models.CharField(max_length=255, default=r"App\Models\User")
    model_id = models.PositiveBigIntegerField(db_column="model_id")

    class Meta:
        db_table = "model_has_roles"
        unique_together = (("role", "model_id", "model_type"),)


class ModelHasPermission(models.Model):
    permission = models.ForeignKey(
        Permission, on_delete=models.CASCADE, db_column="permission_id"
    )
    model_type = models.CharField(max_length=255, default=r"App\Models\User")
    model_id = models.PositiveBigIntegerField(db_column="model_id")

    class Meta:
        db_table = "model_has_permissions"
        unique_together = (("permission", "model_id", "model_type"),)


class User(AbstractBaseUser):
    """Maps to Laravel ``users`` — only columns that already exist."""

    SPATIE_MORPH_TYPE = r"App\Models\User"

    id = models.BigAutoField(primary_key=True)
    name = models.CharField(max_length=255)
    email = models.EmailField(unique=True)
    email_verified_at = models.DateTimeField(null=True, blank=True)
    remember_token = models.CharField(max_length=100, null=True, blank=True)
    role = models.CharField(max_length=255, default="marketing_analyst")
    is_super_admin = models.BooleanField(default=False)
    created_at = models.DateTimeField(null=True, blank=True, auto_now_add=True)
    updated_at = models.DateTimeField(null=True, blank=True, auto_now=True)

    objects = UserManager()

    USERNAME_FIELD = "email"
    REQUIRED_FIELDS = ["name"]

    class Meta:
        db_table = "users"

    def __str__(self):
        return self.email

    def set_password(self, raw_password):
        if raw_password is None:
            self.set_unusable_password()
            return
        hasher = LaravelBcryptPasswordHasher()
        self.password = hasher.encode(raw_password, hasher.salt())

    def check_password(self, raw_password):
        if is_laravel_bcrypt(self.password):
            return LaravelBcryptPasswordHasher().verify(raw_password, self.password)
        from django.contrib.auth.hashers import check_password

        return check_password(raw_password, self.password)

    @property
    def is_staff(self):
        return self.is_super_admin_user()

    @property
    def is_active(self):
        return True

    @property
    def is_superuser(self):
        return self.is_super_admin_user()

    def has_perm(self, perm, obj=None):
        return self.is_super_admin_user() or self.has_perm_name(perm)

    def has_module_perms(self, app_label):
        return self.is_super_admin_user()

    def is_super_admin_user(self) -> bool:
        if self.is_super_admin or self.role == "super_admin":
            return True
        return self.get_roles().filter(name="super_admin").exists()

    def get_roles(self):
        role_ids = ModelHasRole.objects.filter(
            model_type=self.SPATIE_MORPH_TYPE, model_id=self.pk
        ).values_list("role_id", flat=True)
        return Role.objects.filter(id__in=role_ids)

    def assign_role(self, role: Role | str):
        if isinstance(role, str):
            role = Role.objects.get(name=role, guard_name="web")
        ModelHasRole.objects.get_or_create(
            role=role,
            model_type=self.SPATIE_MORPH_TYPE,
            model_id=self.pk,
        )

    def sync_roles(self, role_names: list[str]):
        ModelHasRole.objects.filter(
            model_type=self.SPATIE_MORPH_TYPE, model_id=self.pk
        ).delete()
        for name in role_names:
            role = Role.objects.filter(name=name, guard_name="web").first()
            if role:
                self.assign_role(role)

    def has_perm_name(self, perm: str) -> bool:
        if self.is_super_admin_user():
            return True
        if ModelHasPermission.objects.filter(
            model_type=self.SPATIE_MORPH_TYPE,
            model_id=self.pk,
            permission__name=perm,
            permission__guard_name="web",
        ).exists():
            return True
        role_ids = ModelHasRole.objects.filter(
            model_type=self.SPATIE_MORPH_TYPE, model_id=self.pk
        ).values_list("role_id", flat=True)
        return RoleHasPermission.objects.filter(
            role_id__in=role_ids,
            permission__name=perm,
            permission__guard_name="web",
        ).exists()

    def permission_names(self) -> list[str]:
        if self.is_super_admin_user():
            return list(
                Permission.objects.filter(guard_name="web").values_list("name", flat=True)
            )
        direct = set(
            Permission.objects.filter(
                id__in=ModelHasPermission.objects.filter(
                    model_type=self.SPATIE_MORPH_TYPE, model_id=self.pk
                ).values_list("permission_id", flat=True),
                guard_name="web",
            ).values_list("name", flat=True)
        )
        role_ids = ModelHasRole.objects.filter(
            model_type=self.SPATIE_MORPH_TYPE, model_id=self.pk
        ).values_list("role_id", flat=True)
        via_roles = set(
            Permission.objects.filter(
                id__in=RoleHasPermission.objects.filter(role_id__in=role_ids).values_list(
                    "permission_id", flat=True
                ),
                guard_name="web",
            ).values_list("name", flat=True)
        )
        return sorted(direct | via_roles)

    def get_role_names(self) -> list[str]:
        return list(self.get_roles().values_list("name", flat=True))


class UserAccessScope(models.Model):
    TYPE_PRODUCT = "product"
    TYPE_SOCIAL_ACCOUNT = "social_account"

    id = models.BigAutoField(primary_key=True)
    user = models.ForeignKey(User, on_delete=models.CASCADE, related_name="access_scopes")
    scope_type = models.CharField(max_length=255)
    scope_id = models.PositiveBigIntegerField()
    created_at = models.DateTimeField(null=True, blank=True, auto_now_add=True)
    updated_at = models.DateTimeField(null=True, blank=True, auto_now=True)

    class Meta:
        db_table = "user_access_scopes"
        indexes = [
            models.Index(fields=["user", "scope_type"]),
        ]
