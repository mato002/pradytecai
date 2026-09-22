"""Celery tasks for content publishing (replaces PublishContentJob)."""

from __future__ import annotations

import logging

from celery import shared_task
from django.utils import timezone

logger = logging.getLogger(__name__)


@shared_task(
    bind=True,
    name="content.tasks.publish_content",
    max_retries=5,
    default_retry_delay=60,
    autoretry_for=(ConnectionError, TimeoutError),
    retry_backoff=True,
    retry_jitter=True,
)
def publish_content(self, content_item_id: int):
    from apps.content.models import ContentDestination, ContentItem
    from apps.social.publishers import get_publisher

    try:
        item = ContentItem.objects.get(pk=content_item_id)
    except ContentItem.DoesNotExist:
        logger.error("ContentItem %s missing", content_item_id)
        return {"ok": False, "reason": "missing"}

    destinations = item.destinations.filter(status__in=["pending", "scheduled", "failed"])
    any_success = False
    publisher = get_publisher()

    for dest in destinations:
        # Idempotency: skip already published
        if dest.status == "published" and dest.external_post_id:
            any_success = True
            continue
        try:
            result = publisher.publish(item, dest)
            dest.status = "published"
            dest.external_post_id = result.get("external_post_id")
            dest.published_at = timezone.now()
            dest.failure_message = None
            dest.failed_at = None
            dest.save()
            any_success = True
        except Exception as exc:
            logger.exception("Publish failed destination=%s", dest.id)
            dest.status = "failed"
            dest.failed_at = timezone.now()
            dest.failure_message = str(exc)[:2000]
            dest.save()
            # Permanent auth errors should not retry forever
            msg = str(exc).lower()
            if any(x in msg for x in ("unauthorized", "forbidden", "invalid credential")):
                continue
            raise self.retry(exc=exc)

    item.status = "published" if any_success else "failed"
    item.save(update_fields=["status", "updated_at"])
    if any_success and item.product_id:
        from apps.products.models import Product

        Product.objects.filter(pk=item.product_id).update(last_marketed_at=timezone.now())
    return {"ok": any_success, "content_item_id": content_item_id}


@shared_task(name="content.tasks.publish_due_content")
def publish_due_content():
    """Beat: pick up scheduled content that is due (parity fix vs Laravel gap)."""
    from apps.content.models import ContentItem

    now = timezone.now()
    due = ContentItem.objects.filter(status="scheduled", scheduled_at__lte=now)
    ids = []
    for item in due:
        publish_content.delay(item.id)
        ids.append(item.id)
    return {"queued": ids}
