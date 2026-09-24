"""Signals to clean up resumes stored in Cloudflare R2 / S3 storage when applications are deleted."""

import logging
from django.core.files.storage import default_storage
from django.db.models.signals import post_delete
from django.dispatch import receiver

from apps.careers.models import JobApplication

logger = logging.getLogger(__name__)


@receiver(post_delete, sender=JobApplication)
def job_application_post_delete(sender, instance, **kwargs):
    if instance.resume_path:
        try:
            if default_storage.exists(instance.resume_path):
                default_storage.delete(instance.resume_path)
                logger.info("Deleted resume from storage: %s", instance.resume_path)
        except Exception as exc:
            logger.warning("Could not delete resume '%s' from storage: %s", instance.resume_path, exc)
