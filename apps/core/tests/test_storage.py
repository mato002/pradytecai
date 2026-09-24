"""Tests for Cloudflare R2 storage backend and migration command."""

from io import StringIO
from pathlib import Path
from unittest.mock import MagicMock, patch

from django.core.management import call_command
from django.test import SimpleTestCase, TestCase, override_settings

from apps.core.storage import CloudflareR2Storage
from apps.products.models import Product


class CloudflareR2StorageUnitTests(SimpleTestCase):
    def test_default_initialization(self):
        storage = CloudflareR2Storage(
            endpoint_url="https://example.r2.cloudflarestorage.com",
            access_key="fake-access",
            secret_key="fake-secret",
            bucket_name="test-bucket",
        )
        self.assertEqual(storage.endpoint_url, "https://example.r2.cloudflarestorage.com")
        self.assertEqual(storage.bucket_name, "test-bucket")
        self.assertEqual(storage.signature_version, "s3v4")
        self.assertEqual(storage.addressing_style, "path")
        self.assertFalse(storage.file_overwrite)
        self.assertTrue(storage.querystring_auth)
        self.assertEqual(storage.querystring_expire, 86400)

    def test_custom_domain_configuration(self):
        with patch.dict(
            "os.environ",
            {
                "R2_ENDPOINT": "https://example.r2.cloudflarestorage.com",
                "R2_ACCESS_KEY_ID": "fake-access",
                "R2_SECRET_ACCESS_KEY": "fake-secret",
                "R2_BUCKET_NAME": "test-bucket",
                "R2_CUSTOM_DOMAIN": "https://cdn.example.com/",
            },
        ):
            storage = CloudflareR2Storage()
            self.assertEqual(storage.custom_domain, "cdn.example.com")
            self.assertFalse(storage.querystring_auth)


class MigrateMediaToR2CommandTests(TestCase):
    @patch("apps.core.management.commands.migrate_media_to_r2.CloudflareR2Storage")
    def test_dry_run_command(self, mock_storage_cls):
        mock_storage = MagicMock()
        mock_storage.exists.return_value = False
        mock_storage_cls.return_value = mock_storage

        out = StringIO()
        call_command("migrate_media_to_r2", "--dry-run", stdout=out)
        output = out.getvalue()

        self.assertIn("Mode: DRY RUN", output)
        self.assertIn("Migration completed!", output)
        mock_storage._save.assert_not_called()

    @patch("apps.core.management.commands.migrate_media_to_r2.CloudflareR2Storage")
    def test_unconfigured_r2_warning(self, mock_storage_cls):
        with override_settings(R2_ENDPOINT=None, R2_ACCESS_KEY_ID=None, R2_SECRET_ACCESS_KEY=None):
            with patch.dict(
                "os.environ",
                {"R2_ENDPOINT": "", "R2_ACCESS_KEY_ID": "", "R2_SECRET_ACCESS_KEY": ""},
                clear=False,
            ):
                err = StringIO()
                call_command("migrate_media_to_r2", stderr=err)
                self.assertIn("Cloudflare R2 is not fully configured", err.getvalue())


class MediaLifecycleSignalTests(TestCase):
    def test_product_media_deletion_cleans_storage(self):
        from django.core.files.uploadedfile import SimpleUploadedFile
        from apps.products.models import Product, ProductMedia

        product = Product.objects.create(name="Test Product", slug="test-product-crud")
        media = ProductMedia.objects.create(
            product=product,
            media_type="image",
            image=SimpleUploadedFile("dummy.png", b"fake-png-content", content_type="image/png"),
        )
        file_name = media.image.name
        self.assertTrue(file_name)
        storage = media.image.storage
        self.assertTrue(storage.exists(file_name))

        # Delete media item
        media.delete()
        self.assertFalse(storage.exists(file_name))

    def test_product_update_replaces_old_poster(self):
        from django.core.files.uploadedfile import SimpleUploadedFile
        from apps.products.models import Product

        product = Product.objects.create(
            name="Test Poster Lifecycle",
            slug="test-poster-lifecycle",
            poster=SimpleUploadedFile("poster1.png", b"fake-poster-1", content_type="image/png"),
        )
        old_poster_name = product.poster.name
        storage = product.poster.storage
        self.assertTrue(storage.exists(old_poster_name))

        # Replace poster with a new one
        product.poster = SimpleUploadedFile("poster2.png", b"fake-poster-2", content_type="image/png")
        product.save()

        self.assertFalse(storage.exists(old_poster_name))
        self.assertTrue(storage.exists(product.poster.name))
