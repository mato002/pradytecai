"""Management command to migrate media files from local disk to Cloudflare R2 storage."""

from pathlib import Path
from django.conf import settings
from django.core.management.base import BaseCommand
from django.core.files import File

from apps.core.storage import CloudflareR2Storage


class Command(BaseCommand):
    help = "Migrate media files from local MEDIA_ROOT to Cloudflare R2 storage bucket."

    def add_arguments(self, parser):
        parser.add_argument(
            "--dry-run",
            action="store_true",
            help="Show what files would be uploaded without actually uploading them.",
        )
        parser.add_argument(
            "--overwrite",
            action="store_true",
            help="Overwrite files in R2 even if they already exist.",
        )
        parser.add_argument(
            "--verify-db",
            action="store_true",
            default=True,
            help="Scan database models for media references and verify them.",
        )

    def handle(self, *args, **options):
        dry_run = options["dry_run"]
        overwrite = options["overwrite"]

        endpoint = getattr(settings, "R2_ENDPOINT", None)
        access_key = getattr(settings, "R2_ACCESS_KEY_ID", None)
        secret_key = getattr(settings, "R2_SECRET_ACCESS_KEY", None)
        bucket_name = getattr(settings, "R2_BUCKET_NAME", "pradytecai")

        if not (endpoint and access_key and secret_key):
            self.stderr.write(
                self.style.ERROR(
                    "Cloudflare R2 is not fully configured. Please set R2_ENDPOINT, "
                    "R2_ACCESS_KEY_ID, and R2_SECRET_ACCESS_KEY in your .env file."
                )
            )
            return

        self.stdout.write(
            self.style.MIGRATE_HEADING(
                f"Starting media migration to Cloudflare R2 bucket: {bucket_name}"
            )
        )
        if dry_run:
            self.stdout.write(self.style.WARNING("Mode: DRY RUN (no files will be uploaded)"))

        storage = CloudflareR2Storage()

        media_root = Path(settings.MEDIA_ROOT)
        if not media_root.exists():
            self.stdout.write(self.style.WARNING(f"MEDIA_ROOT does not exist: {media_root}"))
            return

        # 1. Collect all local media files
        local_files = [p for p in media_root.rglob("*") if p.is_file()]
        self.stdout.write(f"Found {len(local_files)} files in local MEDIA_ROOT: {media_root}\n")

        uploaded = 0
        skipped = 0
        errors = 0

        for path in local_files:
            rel_path = path.relative_to(media_root).as_posix()
            file_size = path.stat().st_size

            try:
                exists_in_r2 = storage.exists(rel_path)
            except Exception as exc:
                self.stderr.write(
                    self.style.ERROR(f"Error checking R2 for '{rel_path}': {exc}")
                )
                exists_in_r2 = False

            if exists_in_r2 and not overwrite:
                skipped += 1
                self.stdout.write(f"  [SKIPPED] {rel_path} (already in R2)")
                continue

            if dry_run:
                uploaded += 1
                self.stdout.write(
                    self.style.SUCCESS(
                        f"  [WOULD UPLOAD] {rel_path} ({file_size:,} bytes)"
                    )
                )
                continue

            # Upload to R2
            try:
                with path.open("rb") as f:
                    django_file = File(f)
                    # If overwriting, delete existing first
                    if exists_in_r2 and overwrite:
                        try:
                            storage.delete(rel_path)
                        except Exception:
                            pass
                    # Save preserves exact relative name
                    saved_name = storage._save(rel_path, django_file)
                    uploaded += 1
                    file_url = storage.url(saved_name)
                    self.stdout.write(
                        self.style.SUCCESS(
                            f"  [UPLOADED] {saved_name} ({file_size:,} bytes)\n    -> URL: {file_url}"
                        )
                    )
            except Exception as exc:
                errors += 1
                self.stderr.write(
                    self.style.ERROR(f"  [FAILED] {rel_path}: {exc}")
                )

        # 2. Check database model references
        self.stdout.write("\nChecking database model references...")
        from apps.products.models import (
            Product,
            ProductMedia,
            ProductIntegration,
            ProductCustomSection,
        )
        from apps.careers.models import JobApplication

        total_db_refs = 0
        db_missing = 0

        def check_field(instance, field_name, model_name):
            nonlocal total_db_refs, db_missing
            field = getattr(instance, field_name, None)
            if not field or not getattr(field, "name", None):
                return
            total_db_refs += 1
            name = field.name
            try:
                if not storage.exists(name):
                    db_missing += 1
                    self.stdout.write(
                        self.style.WARNING(
                            f"  [DB WARNING] {model_name} #{instance.pk} references '{name}' but not found in R2"
                        )
                    )
            except Exception as exc:
                self.stdout.write(
                    self.style.WARNING(
                        f"  [DB ERROR] Checking {model_name} #{instance.pk} '{name}': {exc}"
                    )
                )

        for p in Product.objects.all():
            check_field(p, "poster", "Product")
            check_field(p, "hero_image", "Product")
            check_field(p, "mobile_image", "Product")

        for m in ProductMedia.objects.all():
            check_field(m, "image", "ProductMedia")
            check_field(m, "video_file", "ProductMedia")
            check_field(m, "thumbnail", "ProductMedia")

        for i in ProductIntegration.objects.all():
            check_field(i, "logo", "ProductIntegration")

        for c in ProductCustomSection.objects.all():
            check_field(c, "image", "ProductCustomSection")

        for app in JobApplication.objects.all():
            if app.resume_path:
                total_db_refs += 1
                try:
                    if not storage.exists(app.resume_path):
                        db_missing += 1
                        self.stdout.write(
                            self.style.WARNING(
                                f"  [DB WARNING] JobApplication #{app.pk} references '{app.resume_path}' but not found in R2"
                            )
                        )
                except Exception as exc:
                    self.stdout.write(
                        self.style.WARNING(
                            f"  [DB ERROR] Checking JobApplication #{app.pk} '{app.resume_path}': {exc}"
                        )
                    )

        self.stdout.write("\n" + "=" * 60)
        self.stdout.write(
            self.style.SUCCESS(
                f"Migration completed!\n"
                f"  - Files processed: {len(local_files)}\n"
                f"  - Uploaded to R2: {uploaded}\n"
                f"  - Skipped (already in R2): {skipped}\n"
                f"  - Upload errors: {errors}\n"
                f"  - DB references scanned: {total_db_refs}\n"
                f"  - Missing in R2: {db_missing}"
            )
        )
