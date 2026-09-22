from rest_framework import serializers, viewsets

from apps.accounts.permissions import require_perm
from apps.audit.models import ActivityLog
from apps.core.visibility import filter_by_product_scope
from apps.tasks.models import MarketingTask


class MarketingTaskSerializer(serializers.ModelSerializer):
    class Meta:
        model = MarketingTask
        fields = "__all__"


class MarketingTaskViewSet(viewsets.ModelViewSet):
    serializer_class = MarketingTaskSerializer
    permission_classes = [require_perm("tasks.view|tasks.manage")]

    def get_queryset(self):
        return filter_by_product_scope(MarketingTask.objects.all(), self.request.user)

    def get_permissions(self):
        if self.action in ("create", "update", "partial_update", "destroy"):
            return [require_perm("tasks.manage")()]
        return super().get_permissions()


class ActivityLogSerializer(serializers.ModelSerializer):
    class Meta:
        model = ActivityLog
        fields = "__all__"


class ActivityLogViewSet(viewsets.ReadOnlyModelViewSet):
    queryset = ActivityLog.objects.select_related("user").all()
    serializer_class = ActivityLogSerializer
    permission_classes = [require_perm("audit.view")]
