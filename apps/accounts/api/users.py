from rest_framework import serializers, viewsets
from rest_framework.decorators import action
from rest_framework.response import Response

from apps.accounts.models import Permission, Role, User, UserAccessScope
from apps.accounts.permissions import require_perm
from apps.core.visibility import filter_products_visible
from apps.products.models import Product


class UserSerializer(serializers.ModelSerializer):
    roles = serializers.SerializerMethodField()
    permissions = serializers.SerializerMethodField()
    product_scopes = serializers.SerializerMethodField()
    social_account_scopes = serializers.SerializerMethodField()

    class Meta:
        model = User
        fields = [
            "id",
            "name",
            "email",
            "role",
            "is_super_admin",
            "roles",
            "permissions",
            "product_scopes",
            "social_account_scopes",
            "created_at",
        ]
        read_only_fields = ["id", "created_at"]

    def get_roles(self, obj):
        return obj.get_role_names()

    def get_permissions(self, obj):
        return obj.permission_names()

    def get_product_scopes(self, obj):
        return list(
            obj.access_scopes.filter(scope_type="product").values_list("scope_id", flat=True)
        )

    def get_social_account_scopes(self, obj):
        return list(
            obj.access_scopes.filter(scope_type="social_account").values_list(
                "scope_id", flat=True
            )
        )


class UserWriteSerializer(serializers.Serializer):
    name = serializers.CharField()
    email = serializers.EmailField()
    password = serializers.CharField(required=False, allow_blank=True)
    role = serializers.CharField(required=False)
    is_super_admin = serializers.BooleanField(required=False, default=False)
    roles = serializers.ListField(child=serializers.CharField(), required=False)
    product_scopes = serializers.ListField(child=serializers.IntegerField(), required=False)
    social_account_scopes = serializers.ListField(
        child=serializers.IntegerField(), required=False
    )


class UserViewSet(viewsets.ModelViewSet):
    queryset = User.objects.all().order_by("-id")
    permission_classes = [require_perm("users.view|users.manage")]

    def get_serializer_class(self):
        if self.action in ("create", "update", "partial_update"):
            return UserWriteSerializer
        return UserSerializer

    def get_permissions(self):
        if self.action in ("create", "update", "partial_update", "destroy", "sync_scopes"):
            return [require_perm("users.manage")()]
        return super().get_permissions()

    def create(self, request, *args, **kwargs):
        ser = UserWriteSerializer(data=request.data)
        ser.is_valid(raise_exception=True)
        data = ser.validated_data
        user = User(
            name=data["name"],
            email=data["email"],
            role=data.get("role") or "marketing_analyst",
            is_super_admin=bool(data.get("is_super_admin")),
        )
        if data.get("password"):
            user.set_password(data["password"])
        else:
            user.set_unusable_password()
        user.save()
        self._sync_meta(user, data)
        return Response(UserSerializer(user).data, status=201)

    def update(self, request, *args, **kwargs):
        user = self.get_object()
        ser = UserWriteSerializer(data=request.data, partial=True)
        ser.is_valid(raise_exception=True)
        data = ser.validated_data
        for field in ("name", "email", "role", "is_super_admin"):
            if field in data:
                setattr(user, field, data[field])
        if data.get("password"):
            user.set_password(data["password"])
        user.save()
        self._sync_meta(user, data)
        return Response(UserSerializer(user).data)

    def _sync_meta(self, user: User, data: dict):
        if "roles" in data:
            user.sync_roles(data["roles"])
        if user.is_super_admin:
            user.access_scopes.all().delete()
            return
        if "product_scopes" in data or "social_account_scopes" in data:
            user.access_scopes.all().delete()
            for pid in data.get("product_scopes") or []:
                UserAccessScope.objects.create(
                    user=user, scope_type=UserAccessScope.TYPE_PRODUCT, scope_id=pid
                )
            for sid in data.get("social_account_scopes") or []:
                UserAccessScope.objects.create(
                    user=user, scope_type=UserAccessScope.TYPE_SOCIAL_ACCOUNT, scope_id=sid
                )


class RoleSerializer(serializers.ModelSerializer):
    permissions = serializers.SerializerMethodField()

    class Meta:
        model = Role
        fields = ["id", "name", "guard_name", "permissions"]

    def get_permissions(self, obj):
        return obj.permission_names()


class RoleViewSet(viewsets.ReadOnlyModelViewSet):
    queryset = Role.objects.all().order_by("name")
    serializer_class = RoleSerializer
    permission_classes = [require_perm("roles.view|roles.manage")]

    @action(detail=True, methods=["put", "patch"], permission_classes=[require_perm("roles.manage")])
    def permissions(self, request, pk=None):
        role = self.get_object()
        names = request.data.get("permissions") or []
        from apps.accounts.models import RoleHasPermission

        RoleHasPermission.objects.filter(role=role).delete()
        for name in names:
            perm = Permission.objects.filter(name=name, guard_name="web").first()
            if perm:
                RoleHasPermission.objects.create(role=role, permission=perm)
        return Response(RoleSerializer(role).data)
