"""Social publishers — Buffer + null fallback (parity with Laravel SocialPublisher)."""

from __future__ import annotations

import logging
from typing import Any

from django.conf import settings

logger = logging.getLogger(__name__)


class NullPublisher:
    def publish(self, content_item, destination) -> dict[str, Any]:
        raise RuntimeError("No social publisher configured")


class BufferPublisher:
    def publish(self, content_item, destination) -> dict[str, Any]:
        if not settings.BUFFER_ENABLED:
            raise RuntimeError("Buffer disabled")
        integration = destination.social_account.integration if destination.social_account_id else None
        token = None
        if integration:
            token = integration.get_access_token_plain()
        token = token or settings.BUFFER_ACCESS_TOKEN
        if not token:
            raise RuntimeError("Buffer credentials missing")
        # Stub-compatible behaviour: mark as published with a deterministic id
        # Real Buffer API wiring stays behind credentials (same as Laravel stub).
        external_id = f"buffer-stub-{content_item.id}-{destination.id}"
        logger.info(
            "Buffer publish stub content=%s destination=%s",
            content_item.id,
            destination.id,
        )
        return {"external_post_id": external_id, "provider": "buffer"}


def get_publisher():
    if settings.BUFFER_ENABLED:
        return BufferPublisher()
    return NullPublisher()
