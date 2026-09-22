from django.utils import timezone
from rest_framework import serializers, viewsets
from rest_framework.decorators import action
from rest_framework.response import Response

from apps.accounts.permissions import require_perm
from apps.content.models import ContentDestination, ContentItem
from apps.content.tasks import publish_content
from apps.core.visibility import filter_by_product_scope
from apps.marketing.models import SiteSetting


class ContentItemSerializer(serializers.ModelSerializer):
    class Meta:
        model = ContentItem
        fields = "__all__"


class ContentItemViewSet(viewsets.ModelViewSet):
    serializer_class = ContentItemSerializer
    permission_classes = [require_perm("content.view|content.create|content.edit")]

    def get_queryset(self):
        qs = ContentItem.objects.prefetch_related("destinations").all()
        return filter_by_product_scope(qs, self.request.user)

    def get_permissions(self):
        if self.action == "create":
            return [require_perm("content.create")()]
        if self.action in ("update", "partial_update"):
            return [require_perm("content.edit")()]
        if self.action == "destroy":
            return [require_perm("content.delete")()]
        if self.action == "approve":
            return [require_perm("content.approve")()]
        if self.action in ("publish", "schedule"):
            return [require_perm("content.publish")()]
        return super().get_permissions()

    def perform_create(self, serializer):
        serializer.save(author=self.request.user, status="draft")

    @action(detail=True, methods=["post"])
    def submit_for_review(self, request, pk=None):
        item = self.get_object()
        item.status = "in_review"
        item.save(update_fields=["status", "updated_at"])
        return Response(ContentItemSerializer(item).data)

    @action(detail=True, methods=["post"])
    def approve(self, request, pk=None):
        item = self.get_object()
        item.status = "approved"
        item.approver = request.user
        item.save(update_fields=["status", "approver", "updated_at"])
        return Response(ContentItemSerializer(item).data)

    @action(detail=True, methods=["post"])
    def schedule(self, request, pk=None):
        item = self.get_object()
        item.status = "scheduled"
        item.scheduled_at = request.data.get("scheduled_at")
        item.save(update_fields=["status", "scheduled_at", "updated_at"])
        ContentDestination.objects.filter(
            content_item=item, status__in=["pending", "failed"]
        ).update(status="scheduled")
        return Response(ContentItemSerializer(item).data)

    @action(detail=True, methods=["post"])
    def publish(self, request, pk=None):
        item = self.get_object()
        required = SiteSetting.get("content.approval_required", "1") == "1"
        if required and item.status not in ("approved", "scheduled"):
            return Response({"detail": "Approval required."}, status=400)
        item.status = "scheduled"
        if not item.published_at:
            item.published_at = timezone.now()
        item.save(update_fields=["status", "published_at", "updated_at"])
        publish_content.delay(item.id)
        return Response({"detail": "Publish queued.", "id": item.id})
