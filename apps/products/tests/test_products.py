"""Tests for product catalog CRUD, public API, seeding, and posters."""

from io import BytesIO

from django.core.files.uploadedfile import SimpleUploadedFile
from django.test import Client, TestCase, override_settings
from PIL import Image

from apps.accounts.models import User
from apps.leads.models import ContactMessage
from apps.products.management.commands.seed_products import DEFAULT_PRODUCTS, seed_default_products
from apps.products.models import Product


def _png(name="poster.png", size=(40, 40), color=(20, 120, 80)):
    buf = BytesIO()
    Image.new("RGB", size, color).save(buf, format="PNG")
    return SimpleUploadedFile(name, buf.getvalue(), content_type="image/png")


class ProductModelTests(TestCase):
    def test_create_and_auto_slug(self):
        p = Product.objects.create(name="Test Widget", short="A widget", is_active=True)
        self.assertEqual(p.slug, "test-widget")
        self.assertTrue(p.is_active)

    def test_slug_uniqueness(self):
        Product.objects.create(name="A", slug="same-slug")
        with self.assertRaises(Exception):
            Product.objects.create(name="B", slug="same-slug")

    def test_update_fields(self):
        p = Product.objects.create(name="Old", slug="old", short="s")
        p.name = "New"
        p.short = "updated"
        p.is_featured = True
        p.save()
        p.refresh_from_db()
        self.assertEqual(p.name, "New")
        self.assertEqual(p.short, "updated")
        self.assertTrue(p.is_featured)


class ProductSeedTests(TestCase):
    def test_seed_idempotent(self):
        c1, s1 = seed_default_products()
        self.assertEqual(c1, len(DEFAULT_PRODUCTS))
        self.assertEqual(s1, 0)
        self.assertEqual(Product.objects.count(), len(DEFAULT_PRODUCTS))

        # Admin edit must not be overwritten
        p = Product.objects.get(slug="prady-microfinance")
        p.name = "Admin Edited Microfinance"
        p.save(update_fields=["name", "updated_at"])

        c2, s2 = seed_default_products()
        self.assertEqual(c2, 0)
        self.assertEqual(s2, len(DEFAULT_PRODUCTS))
        p.refresh_from_db()
        self.assertEqual(p.name, "Admin Edited Microfinance")
        self.assertEqual(Product.objects.count(), len(DEFAULT_PRODUCTS))


class PublicProductApiTests(TestCase):
    def setUp(self):
        self.client = Client(enforce_csrf_checks=False)
        self.active = Product.objects.create(
            name="Active Prod",
            slug="active-prod",
            short="Active short",
            description="Full description",
            is_active=True,
            is_featured=True,
            order=1,
            cta_label="Book demo",
        )
        self.inactive = Product.objects.create(
            name="Hidden",
            slug="hidden-prod",
            is_active=False,
            order=2,
        )

    def test_list_active_only(self):
        r = self.client.get("/api/v1/public/products/")
        self.assertEqual(r.status_code, 200)
        slugs = [row["slug"] for row in r.json()]
        self.assertIn("active-prod", slugs)
        self.assertNotIn("hidden-prod", slugs)

    def test_detail_active(self):
        r = self.client.get("/api/v1/public/products/active-prod/")
        self.assertEqual(r.status_code, 200)
        self.assertEqual(r.json()["name"], "Active Prod")
        self.assertEqual(r.json()["cta_label"], "Book demo")

    def test_detail_inactive_404(self):
        r = self.client.get("/api/v1/public/products/hidden-prod/")
        self.assertEqual(r.status_code, 404)

    def test_featured_filter(self):
        r = self.client.get("/api/v1/public/products/?featured=1")
        self.assertEqual(r.status_code, 200)
        self.assertEqual(len(r.json()), 1)
        self.assertEqual(r.json()[0]["slug"], "active-prod")


class ProductAdminApiTests(TestCase):
    def setUp(self):
        import tempfile
        from pathlib import Path

        from django.conf import settings

        self._tmpdir = tempfile.TemporaryDirectory()
        self._media_override = override_settings(MEDIA_ROOT=Path(self._tmpdir.name))
        self._media_override.enable()

        self.client = Client(enforce_csrf_checks=False)
        self.admin = User.objects.create(
            name="Admin", email="admin@example.com", role="super_admin", is_super_admin=True
        )
        self.admin.set_password("secret123")
        self.admin.save()
        self.client.force_login(self.admin)

        self.anon = Client(enforce_csrf_checks=False)

    def tearDown(self):
        self._media_override.disable()
        self._tmpdir.cleanup()

    def test_admin_crud_and_poster(self):
        # Create
        r = self.client.post(
            "/api/v1/products/",
            data={
                "name": "E2E Product",
                "slug": "e2e-product",
                "short": "Short",
                "description": "Desc",
                "is_active": True,
                "order": 5,
            },
            content_type="application/json",
        )
        self.assertEqual(r.status_code, 201, r.content)
        pid = r.json()["id"]

        # Public sees it
        pub = self.anon.get("/api/v1/public/products/e2e-product/")
        self.assertEqual(pub.status_code, 200)
        self.assertEqual(pub.json()["name"], "E2E Product")

        # Update
        r = self.client.patch(
            f"/api/v1/products/{pid}/",
            data={"name": "E2E Renamed", "description": "Updated desc"},
            content_type="application/json",
        )
        self.assertEqual(r.status_code, 200)
        pub = self.anon.get("/api/v1/public/products/e2e-product/")
        self.assertEqual(pub.json()["name"], "E2E Renamed")

        # Poster upload
        r = self.client.post(
            f"/api/v1/products/{pid}/upload-poster/",
            data={"poster": _png()},
        )
        self.assertEqual(r.status_code, 200, r.content)
        self.assertTrue(r.json().get("poster_url"))
        pub = self.anon.get("/api/v1/public/products/e2e-product/")
        self.assertTrue(pub.json().get("poster_url"))

        # Deactivate
        r = self.client.patch(
            f"/api/v1/products/{pid}/",
            data={"is_active": False},
            content_type="application/json",
        )
        self.assertEqual(r.status_code, 200)
        self.assertEqual(self.anon.get("/api/v1/public/products/e2e-product/").status_code, 404)

        # Reactivate
        self.client.patch(
            f"/api/v1/products/{pid}/",
            data={"is_active": True},
            content_type="application/json",
        )
        self.assertEqual(self.anon.get("/api/v1/public/products/e2e-product/").status_code, 200)

        # Delete with linked enquiry (SET_NULL)
        ContactMessage.objects.create(
            name="Lead",
            email="lead@example.com",
            subject="Hi",
            message="Msg",
            product_id=pid,
            status="new",
        )
        r = self.client.delete(f"/api/v1/products/{pid}/")
        self.assertEqual(r.status_code, 204)
        self.assertFalse(Product.objects.filter(pk=pid).exists())
        lead = ContactMessage.objects.get(email="lead@example.com")
        self.assertIsNone(lead.product_id)

    def test_public_cannot_manage(self):
        r = self.anon.get("/api/v1/products/")
        self.assertIn(r.status_code, (401, 403))
        r = self.anon.post(
            "/api/v1/products/",
            data={"name": "Nope", "slug": "nope"},
            content_type="application/json",
        )
        self.assertIn(r.status_code, (401, 403))
