import logging
import os
from typing import Any

from django.conf import settings
from storages.backends.s3boto3 import S3Boto3Storage

logger = logging.getLogger(__name__)


class CloudflareR2Storage(S3Boto3Storage):
    """
    Storage backend for Cloudflare R2 using django-storages and boto3.

    Cloudflare R2 provides an S3-compatible API with:
    - Path-style or virtual-hosted addressing
    - S3v4 signature version
    - 'auto' region
    - Optional Cloudflare extension 'cf-create-bucket-if-missing' header
    - Support for custom domains / r2.dev public URLs or presigned URLs
    """

    def __init__(self, **kwargs: Any):
        # Default options specifically tuned for Cloudflare R2
        kwargs.setdefault(
            "endpoint_url",
            getattr(settings, "R2_ENDPOINT", os.getenv("R2_ENDPOINT")),
        )
        kwargs.setdefault(
            "access_key",
            getattr(settings, "R2_ACCESS_KEY_ID", os.getenv("R2_ACCESS_KEY_ID")),
        )
        kwargs.setdefault(
            "secret_key",
            getattr(
                settings, "R2_SECRET_ACCESS_KEY", os.getenv("R2_SECRET_ACCESS_KEY")
            ),
        )
        kwargs.setdefault(
            "bucket_name",
            getattr(settings, "R2_BUCKET_NAME", os.getenv("R2_BUCKET_NAME", "pradytecai")),
        )
        kwargs.setdefault(
            "region_name",
            getattr(settings, "R2_REGION", os.getenv("R2_REGION", "auto")),
        )
        kwargs.setdefault("signature_version", "s3v4")
        kwargs.setdefault("addressing_style", "path")
        kwargs.setdefault("file_overwrite", False)

        # Check for custom domain (e.g. cdn.pradytecai.com or pub-*.r2.dev)
        raw_custom_domain = (
            getattr(settings, "R2_CUSTOM_DOMAIN", None)
            or os.getenv("R2_CUSTOM_DOMAIN")
            or os.getenv("R2_PUBLIC_URL")
        )
        if raw_custom_domain:
            custom_domain = raw_custom_domain.strip()
            if custom_domain.startswith("https://"):
                custom_domain = custom_domain[len("https://") :]
            elif custom_domain.startswith("http://"):
                custom_domain = custom_domain[len("http://") :]
            custom_domain = custom_domain.rstrip("/")
            kwargs.setdefault("custom_domain", custom_domain)
            kwargs.setdefault("querystring_auth", False)
        else:
            # When no public custom domain is configured, default to presigned URLs
            # with 24 hours expiry (86400 seconds) so images/videos stay accessible.
            expire = int(
                getattr(settings, "R2_QUERYSTRING_EXPIRE", None)
                or os.getenv("R2_QUERYSTRING_EXPIRE", "86400")
            )
            kwargs.setdefault("querystring_expire", expire)
            kwargs.setdefault("querystring_auth", True)

        super().__init__(**kwargs)

    @property
    def connection(self):
        """
        Return the S3 resource connection, attaching Cloudflare R2 specific hooks
        to auto-create bucket if missing.
        """
        is_new = (
            not hasattr(self._connections, "connection")
            or self._connections.connection is None
        )
        conn = super().connection
        if is_new:
            try:
                def _add_cf_header(params, **event_kwargs):
                    headers = params.setdefault("headers", {})
                    headers["cf-create-bucket-if-missing"] = "true"

                conn.meta.client.meta.events.register(
                    "before-call.s3.PutObject", _add_cf_header
                )
                conn.meta.client.meta.events.register(
                    "before-call.s3.CreateMultipartUpload", _add_cf_header
                )
            except Exception as exc:
                logger.debug("Could not register R2 extension header: %s", exc)
        return conn
