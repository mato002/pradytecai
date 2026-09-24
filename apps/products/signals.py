"""Signals to ensure Cloudflare R2 / S3 storage is kept in sync with database CRUD operations."""

import logging
from django.db.models.signals import post_delete, pre_save
from django.dispatch import receiver

from apps.products.models import (
    Product,
    ProductCustomSection,
    ProductIntegration,
    ProductMedia,
)

logger = logging.getLogger(__name__)


def _safe_delete_file(field_file):
    """Safely delete a file from storage if present."""
    if not field_file or not getattr(field_file, "name", None):
        return
    try:
        storage = field_file.storage
        if storage.exists(field_file.name):
            storage.delete(field_file.name)
            logger.info("Deleted media file from storage: %s", field_file.name)
    except Exception as exc:
        logger.warning("Could not delete '%s' from storage: %s", field_file.name, exc)


def _cleanup_old_files(sender, instance, file_fields):
    """Delete old files from storage when an instance is updated with new files."""
    if not instance.pk:
        return
    try:
        old_instance = sender.objects.filter(pk=instance.pk).first()
        if not old_instance:
            return
        for field_name in file_fields:
            old_file = getattr(old_instance, field_name, None)
            new_file = getattr(instance, field_name, None)
            old_name = getattr(old_file, "name", None)
            new_name = getattr(new_file, "name", None)
            if old_name and old_name != new_name:
                _safe_delete_file(old_file)
    except Exception as exc:
        logger.warning("Error during file cleanup for %s #%s: %s", sender.__name__, instance.pk, exc)


# ---------------------------------------------------------------------------
# Product signals
# ---------------------------------------------------------------------------
@receiver(pre_save, sender=Product)
def product_pre_save(sender, instance, **kwargs):
    _cleanup_old_files(sender, instance, ["poster", "hero_image", "mobile_image"])


@receiver(post_delete, sender=Product)
def product_post_delete(sender, instance, **kwargs):
    for field_name in ("poster", "hero_image", "mobile_image"):
        _safe_delete_file(getattr(instance, field_name, None))


# ---------------------------------------------------------------------------
# ProductMedia signals
# ---------------------------------------------------------------------------
@receiver(pre_save, sender=ProductMedia)
def product_media_pre_save(sender, instance, **kwargs):
    _cleanup_old_files(sender, instance, ["image", "video_file", "thumbnail"])


@receiver(post_delete, sender=ProductMedia)
def product_media_post_delete(sender, instance, **kwargs):
    for field_name in ("image", "video_file", "thumbnail"):
        _safe_delete_file(getattr(instance, field_name, None))


# ---------------------------------------------------------------------------
# ProductIntegration signals
# ---------------------------------------------------------------------------
@receiver(pre_save, sender=ProductIntegration)
def product_integration_pre_save(sender, instance, **kwargs):
    _cleanup_old_files(sender, instance, ["logo"])


@receiver(post_delete, sender=ProductIntegration)
def product_integration_post_delete(sender, instance, **kwargs):
    _safe_delete_file(getattr(instance, "logo", None))


# ---------------------------------------------------------------------------
# ProductCustomSection signals
# ---------------------------------------------------------------------------
@receiver(pre_save, sender=ProductCustomSection)
def product_custom_section_pre_save(sender, instance, **kwargs):
    _cleanup_old_files(sender, instance, ["image"])


@receiver(post_delete, sender=ProductCustomSection)
def product_custom_section_post_delete(sender, instance, **kwargs):
    _safe_delete_file(getattr(instance, "image", None))
