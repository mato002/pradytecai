"""Analytics / pulse / GA4 / social metrics Celery tasks."""

from __future__ import annotations

import logging

from celery import shared_task
from django.utils import timezone

logger = logging.getLogger(__name__)


@shared_task(name="analytics.tasks.evaluate_marketing_pulse")
def evaluate_marketing_pulse():
    """Port of marketing:pulse — create/open alerts for stale products etc."""
    from apps.analytics.models import MarketingAlert
    from apps.products.models import Product

    now = timezone.now()
    created = 0
    for product in Product.objects.filter(is_active=True):
        rule = "product.stale_marketing"
        if product.last_marketed_at and (now - product.last_marketed_at).days < 14:
            continue
        existing = MarketingAlert.objects.filter(
            rule_key=rule, product=product, status="open"
        ).first()
        if existing:
            continue
        MarketingAlert.objects.create(
            rule_key=rule,
            severity="warning",
            product=product,
            status="open",
            title=f"Stale marketing: {product.name}",
            message="No recent marketing activity recorded for this product.",
            triggered_at=now,
        )
        created += 1
    return {"created": created}


@shared_task(name="analytics.tasks.sync_social_metrics")
def sync_social_metrics():
    """Stub parity with SyncSocialMetricsJob — no-op until provider credentials wired."""
    logger.info("sync_social_metrics stub ran at %s", timezone.now())
    return {"ok": True, "stub": True}


@shared_task(name="analytics.tasks.sync_ga4")
def sync_ga4():
    """Stub parity with SyncGa4Job."""
    from django.conf import settings

    if not settings.GA4_PROPERTY_ID:
        logger.info("GA4 not configured; skipping")
        return {"ok": False, "reason": "not_configured"}
    logger.info("sync_ga4 stub ran for property %s", settings.GA4_PROPERTY_ID)
    return {"ok": True, "stub": True}
