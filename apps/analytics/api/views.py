from django.db.models import F
from django.shortcuts import get_object_or_404, redirect
from django.utils import timezone
from django.views.decorators.http import require_GET
from rest_framework import serializers, viewsets
from rest_framework.decorators import api_view, permission_classes
from rest_framework.response import Response

from apps.accounts.permissions import require_perm
from apps.analytics.models import MarketingAlert, TrackedLink, WebsiteEvent
from apps.core.visibility import filter_by_product_scope


class MarketingAlertSerializer(serializers.ModelSerializer):
    class Meta:
        model = MarketingAlert
        fields = "__all__"


class PulseViewSet(viewsets.ReadOnlyModelViewSet):
    serializer_class = MarketingAlertSerializer
    permission_classes = [require_perm("pulse.view")]

    def get_queryset(self):
        qs = MarketingAlert.objects.filter(status="open").order_by("-triggered_at")
        return filter_by_product_scope(qs, self.request.user)


@api_view(["GET"])
@permission_classes([require_perm("analytics.view")])
def analytics_overview(request):
    from apps.campaigns.models import Campaign
    from apps.content.models import ContentItem
    from apps.leads.models import ContactMessage

    return Response(
        {
            "leads": filter_by_product_scope(ContactMessage.objects.all(), request.user).count(),
            "campaigns": Campaign.objects.count(),
            "content": filter_by_product_scope(ContentItem.objects.all(), request.user).count(),
            "open_alerts": filter_by_product_scope(
                MarketingAlert.objects.filter(status="open"), request.user
            ).count(),
        }
    )


@require_GET
def tracked_link_redirect(request, code: str):
    link = get_object_or_404(TrackedLink, code=code)
    TrackedLink.objects.filter(pk=link.pk).update(click_count=F("click_count") + 1)
    if not request.session.session_key:
        request.session.create()
    WebsiteEvent.objects.create(
        event_name="tracked_link_click",
        product_id=link.product_id,
        campaign_id=link.campaign_id,
        tracked_link=link,
        session_id=request.session.session_key,
        occurred_at=timezone.now(),
        meta={
            "code": code,
            "referrer": request.META.get("HTTP_REFERER"),
            "ip": request.META.get("REMOTE_ADDR"),
        },
    )
    return redirect(link.destination_url)
