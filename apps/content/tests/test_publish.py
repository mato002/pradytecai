"""Publish content task idempotency tests (eager)."""

from django.test import TestCase, override_settings
from django.utils import timezone

from apps.content.models import ContentDestination, ContentItem
from apps.content.tasks import publish_content
from apps.products.models import Product
from apps.social.models import SocialAccount


@override_settings(CELERY_TASK_ALWAYS_EAGER=True, CELERY_TASK_EAGER_PROPAGATES=True, BUFFER_ENABLED=True, BUFFER_ACCESS_TOKEN="test")
class PublishIdempotencyTests(TestCase):
    def setUp(self):
        self.product = Product.objects.create(name="P", is_active=True)
        self.account = SocialAccount.objects.create(platform="linkedin", name="Page", is_active=True)
        self.item = ContentItem.objects.create(
            product=self.product, status="scheduled", title="T", published_at=timezone.now()
        )
        self.dest = ContentDestination.objects.create(
            content_item=self.item, social_account=self.account, status="pending"
        )

    def test_publish_twice_does_not_duplicate_external_id(self):
        publish_content(self.item.id)
        self.dest.refresh_from_db()
        self.assertEqual(self.dest.status, "published")
        first_id = self.dest.external_post_id
        # Reset to pending would re-publish; already published destinations are skipped on re-run
        publish_content(self.item.id)
        self.dest.refresh_from_db()
        self.assertEqual(self.dest.external_post_id, first_id)
