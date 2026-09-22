from rest_framework import serializers, viewsets

from apps.accounts.permissions import require_perm
from apps.core.visibility import filter_social_accounts_visible
from apps.social.models import Integration, SocialAccount


class SocialAccountSerializer(serializers.ModelSerializer):
    class Meta:
        model = SocialAccount
        fields = "__all__"


class SocialAccountViewSet(viewsets.ModelViewSet):
    serializer_class = SocialAccountSerializer
    permission_classes = [require_perm("social_accounts.view|social_accounts.manage")]

    def get_queryset(self):
        return filter_social_accounts_visible(SocialAccount.objects.all(), self.request.user)

    def get_permissions(self):
        if self.action in ("create", "update", "partial_update", "destroy"):
            return [require_perm("social_accounts.manage")()]
        return super().get_permissions()


class IntegrationSerializer(serializers.ModelSerializer):
    has_token = serializers.SerializerMethodField()

    class Meta:
        model = Integration
        exclude = ("access_token", "refresh_token")

    def get_has_token(self, obj):
        return obj.has_token_ciphertext()


class IntegrationViewSet(viewsets.ModelViewSet):
    queryset = Integration.objects.all()
    serializer_class = IntegrationSerializer
    permission_classes = [require_perm("integrations.view|integrations.manage")]

    def get_permissions(self):
        if self.action in ("create", "update", "partial_update", "destroy"):
            return [require_perm("integrations.manage")()]
        return super().get_permissions()
