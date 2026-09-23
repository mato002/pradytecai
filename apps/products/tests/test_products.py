"""Tests for product catalog CRUD, public API, seeding, and posters."""

from io import BytesIO

from django.core.files.uploadedfile import SimpleUploadedFile
from django.test import Client, TestCase, override_settings
from PIL import Image

from apps.accounts.models import User
from apps.leads.models import ContactMessage
from apps.products.detail_defaults import seed_product_page_content
from apps.products.management.commands.seed_products import DEFAULT_PRODUCTS, seed_default_products
from apps.products.models import (
    Product,
    ProductCapability,
    ProductCapabilityGroup,
    ProductFAQ,
    ProductHighlight,
    ProductIntegration,
    ProductMedia,
    ProductPageSection,
    ProductWorkflowStep,
    validate_product_video,
)


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


class ProductDetailApiTests(TestCase):
    def setUp(self):
        self.client = Client(enforce_csrf_checks=False)

    def _product(self, **kwargs):
        defaults = {"is_active": True, "cta_label": "Request demo", "cta_type": "demo"}
        defaults.update(kwargs)
        return Product.objects.create(**defaults)

    def test_list_stays_lightweight(self):
        product = self._product(name="Listed", slug="listed", short="Card copy")
        ProductHighlight.objects.create(product=product, title="Should not be on the list", display_order=1)
        r = self.client.get("/api/v1/public/products/")
        self.assertEqual(r.status_code, 200)
        row = r.json()[0]
        self.assertEqual(row["slug"], "listed")
        self.assertEqual(row["short_description"], "Card copy")
        self.assertIn("poster_url", row)
        self.assertIn("is_featured", row)
        for heavy in (
            "highlights",
            "audiences",
            "problems",
            "capability_groups",
            "workflow_steps",
            "media",
            "integrations",
            "controls",
            "outcomes",
            "faqs",
            "page_sections",
            "custom_sections",
        ):
            self.assertNotIn(heavy, row)

    def test_detail_orders_and_hides_inactive(self):
        product = self._product(name="Ordered", slug="ordered", short="Short", description="Overview")
        ProductHighlight.objects.create(product=product, title="Later", display_order=30)
        ProductHighlight.objects.create(product=product, title="First", display_order=10)
        ProductHighlight.objects.create(product=product, title="Hidden", display_order=5, is_active=False)
        ProductCapabilityGroup.objects.create(product=product, title="Later group", display_order=20)
        group_early = ProductCapabilityGroup.objects.create(product=product, title="Early group", display_order=10)
        ProductCapability.objects.create(capability_group=group_early, title="Second cap", display_order=20)
        ProductCapability.objects.create(capability_group=group_early, title="First cap", display_order=10)
        ProductCapability.objects.create(
            capability_group=group_early, title="Hidden cap", display_order=1, is_active=False
        )
        ProductCapabilityGroup.objects.create(
            product=product, title="Hidden group", display_order=1, is_active=False
        )
        ProductPageSection.objects.create(
            product=product, section_type="highlights", display_order=20, is_enabled=True
        )
        ProductPageSection.objects.create(
            product=product, section_type="capabilities", display_order=10, is_enabled=True
        )
        ProductPageSection.objects.create(
            product=product, section_type="faq", display_order=5, is_enabled=False, title_override="Nope"
        )
        ProductFAQ.objects.create(product=product, question="Hidden?", answer="No", is_active=False)

        r = self.client.get("/api/v1/public/products/ordered/")
        self.assertEqual(r.status_code, 200)
        body = r.json()
        self.assertEqual(body["overview"], "Overview")
        self.assertEqual(body["short_description"], "Short")
        self.assertEqual([h["title"] for h in body["highlights"]], ["First", "Later"])
        self.assertEqual([g["title"] for g in body["capability_groups"]], ["Early group", "Later group"])
        self.assertEqual(
            [c["title"] for c in body["capability_groups"][0]["capabilities"]],
            ["First cap", "Second cap"],
        )
        enabled = [s["section_type"] for s in body["page_sections"] if s["is_enabled"]]
        self.assertEqual(enabled, ["capabilities", "highlights"])
        self.assertIn(
            "faq",
            [s["section_type"] for s in body["page_sections"] if not s["is_enabled"]],
        )
        self.assertEqual(body["faqs"], [])

    def test_missing_slug_404(self):
        r = self.client.get("/api/v1/public/products/does-not-exist/")
        self.assertEqual(r.status_code, 404)

    def test_three_different_structures(self):
        rich = self._product(name="Rich", slug="rich-product", tagline="Rich tagline")
        ProductHighlight.objects.create(product=rich, title="Highlight", display_order=10)
        ProductWorkflowStep.objects.create(product=rich, title="Apply", step_number=1, display_order=10)
        ProductIntegration.objects.create(product=rich, name="M-Pesa", display_order=10)

        gps = self._product(name="Tracker", slug="tracker-product")
        group = ProductCapabilityGroup.objects.create(product=gps, title="Tracking", display_order=10)
        ProductCapability.objects.create(capability_group=group, title="Live location", display_order=10)
        ProductWorkflowStep.objects.create(product=gps, title="Register devices", step_number=1, display_order=10)

        simple = self._product(name="Simple", slug="simple-product")
        ProductFAQ.objects.create(
            product=simple, question="What is it?", answer="A smaller page.", display_order=10
        )

        rich_body = self.client.get("/api/v1/public/products/rich-product/").json()
        gps_body = self.client.get("/api/v1/public/products/tracker-product/").json()
        simple_body = self.client.get("/api/v1/public/products/simple-product/").json()

        self.assertTrue(rich_body["highlights"])
        self.assertTrue(rich_body["workflow_steps"])
        self.assertTrue(rich_body["integrations"])
        self.assertFalse(rich_body["faqs"])

        self.assertFalse(gps_body["highlights"])
        self.assertEqual(gps_body["capability_groups"][0]["title"], "Tracking")
        self.assertTrue(gps_body["workflow_steps"])
        self.assertFalse(gps_body["integrations"])
        self.assertFalse(gps_body["faqs"])

        self.assertFalse(simple_body["highlights"])
        self.assertFalse(simple_body["workflow_steps"])
        self.assertFalse(simple_body["capability_groups"])
        self.assertEqual(simple_body["faqs"][0]["question"], "What is it?")

    def test_detail_query_count_is_bounded(self):
        from django.db import connection
        from django.test.utils import CaptureQueriesContext

        product = self._product(name="Bound", slug="bound-product")
        for index in range(4):
            group = ProductCapabilityGroup.objects.create(
                product=product, title=f"Group {index}", display_order=index
            )
            for cap in range(6):
                ProductCapability.objects.create(
                    capability_group=group, title=f"Cap {index}-{cap}", display_order=cap
                )
            ProductHighlight.objects.create(product=product, title=f"H {index}", display_order=index)

        with CaptureQueriesContext(connection) as captured:
            response = self.client.get("/api/v1/public/products/bound-product/")
        self.assertEqual(response.status_code, 200)
        self.assertEqual(len(response.json()["capability_groups"]), 4)
        self.assertEqual(len(response.json()["capability_groups"][0]["capabilities"]), 6)
        self.assertLessEqual(len(captured), 20)

    def test_seeded_detail_preserves_admin_edits(self):
        import tempfile
        from pathlib import Path

        tmp = tempfile.TemporaryDirectory()
        self.addCleanup(tmp.cleanup)
        media = override_settings(MEDIA_ROOT=Path(tmp.name))
        media.enable()
        self.addCleanup(media.disable)

        seed_default_products()
        first = seed_product_page_content()
        self.assertGreaterEqual(first["filled"], 1)

        product = Product.objects.get(slug="prady-microfinance")
        original_slug = product.slug
        original_short = product.short
        product.name = "Admin Edited Microfinance"
        product.tagline = "Admin tagline"
        product.save(update_fields=["name", "tagline", "updated_at"])
        highlight = product.highlights.order_by("display_order").first()
        self.assertIsNotNone(highlight)
        highlight.title = "Admin highlight"
        highlight.save(update_fields=["title"])

        product.poster.save("kept.png", _png(), save=True)

        second = seed_product_page_content()
        self.assertEqual(second["filled"], 0)
        product.refresh_from_db()
        highlight.refresh_from_db()
        self.assertEqual(product.name, "Admin Edited Microfinance")
        self.assertEqual(product.slug, original_slug)
        self.assertEqual(product.short, original_short)
        self.assertEqual(product.tagline, "Admin tagline")
        self.assertEqual(highlight.title, "Admin highlight")
        self.assertIn("kept", product.poster.name)

        micro = self.client.get("/api/v1/public/products/prady-microfinance/").json()
        gps = self.client.get("/api/v1/public/products/gps-hosting/").json()
        prop = self.client.get("/api/v1/public/products/property-management/").json()

        self.assertTrue(micro["integrations"])
        self.assertTrue(micro["workflow_steps"])
        self.assertTrue(micro["faqs"])
        self.assertNotIn("%", " ".join(item["title"] for item in micro["outcomes"]))

        self.assertTrue(gps["workflow_steps"])
        self.assertFalse(gps["integrations"])
        self.assertFalse(gps["faqs"])
        self.assertFalse(gps["outcomes"])

        self.assertTrue(prop["custom_sections"])
        self.assertTrue(prop["faqs"])
        self.assertFalse(prop["workflow_steps"])
        self.assertFalse(prop["integrations"])
        self.assertNotEqual(
            [s["section_type"] for s in micro["page_sections"]],
            [s["section_type"] for s in prop["page_sections"]],
        )


def _tiny_mp4(name="clip.mp4"):
    # Minimal bytes with .mp4 name — validation is extension/MIME/size based.
    return SimpleUploadedFile(name, b"\x00\x00\x00\x18ftypmp42", content_type="video/mp4")


class ProductMediaTests(TestCase):
    def setUp(self):
        import tempfile
        from pathlib import Path

        self._tmpdir = tempfile.TemporaryDirectory()
        self.addCleanup(self._tmpdir.cleanup)
        media = override_settings(MEDIA_ROOT=Path(self._tmpdir.name))
        media.enable()
        self.addCleanup(media.disable)
        self.client = Client(enforce_csrf_checks=False)
        self.product = Product.objects.create(
            name="Media Product",
            slug="media-product",
            short="Has media",
            is_active=True,
            cta_label="Request demo",
            cta_type="demo",
        )

    def test_image_media_creation(self):
        row = ProductMedia(
            product=self.product,
            media_type=ProductMedia.TYPE_IMAGE,
            title="Dashboard",
            image=_png("dash.png"),
            image_category=ProductMedia.CATEGORY_DASHBOARD,
            display_order=10,
        )
        row.full_clean()
        row.save()
        self.assertEqual(row.media_type, "image")
        self.assertTrue(row.image)

    def test_video_upload_creation(self):
        row = ProductMedia(
            product=self.product,
            media_type=ProductMedia.TYPE_VIDEO,
            video_source=ProductMedia.SOURCE_UPLOAD,
            title="Overview",
            video_file=_tiny_mp4(),
            thumbnail=_png("thumb.png"),
            display_order=20,
        )
        row.full_clean()
        row.save()
        self.assertEqual(row.video_source, "upload")
        self.assertTrue(row.video_file)
        self.assertTrue(row.mime_type)

    def test_invalid_video_format_rejected(self):
        bad = SimpleUploadedFile("clip.exe", b"MZ", content_type="application/octet-stream")
        with self.assertRaises(Exception):
            validate_product_video(bad)

    def test_oversized_video_rejected(self):
        with override_settings(PRODUCT_VIDEO_MAX_BYTES=1024):
            huge = SimpleUploadedFile("big.mp4", b"x" * 2048, content_type="video/mp4")
            with self.assertRaises(Exception):
                validate_product_video(huge)

    def test_upload_video_without_file_rejected(self):
        row = ProductMedia(
            product=self.product,
            media_type=ProductMedia.TYPE_VIDEO,
            video_source=ProductMedia.SOURCE_UPLOAD,
            title="Missing file",
        )
        with self.assertRaises(Exception):
            row.full_clean()

    def test_external_video_without_url_rejected(self):
        row = ProductMedia(
            product=self.product,
            media_type=ProductMedia.TYPE_VIDEO,
            video_source=ProductMedia.SOURCE_EXTERNAL,
            title="Missing url",
        )
        with self.assertRaises(Exception):
            row.full_clean()

    def test_image_cannot_carry_video_file(self):
        row = ProductMedia(
            product=self.product,
            media_type=ProductMedia.TYPE_IMAGE,
            image=_png(),
            video_file=_tiny_mp4(),
        )
        with self.assertRaises(Exception):
            row.full_clean()

    def test_active_filtering_and_ordering(self):
        ProductMedia.objects.create(
            product=self.product,
            media_type=ProductMedia.TYPE_IMAGE,
            title="Second",
            image=_png("b.png"),
            display_order=20,
        )
        ProductMedia.objects.create(
            product=self.product,
            media_type=ProductMedia.TYPE_IMAGE,
            title="First",
            image=_png("a.png"),
            display_order=10,
        )
        ProductMedia.objects.create(
            product=self.product,
            media_type=ProductMedia.TYPE_IMAGE,
            title="Hidden",
            image=_png("c.png"),
            display_order=5,
            is_active=False,
        )
        visible = list(ProductMedia.objects.publicly_visible().filter(product=self.product))
        self.assertEqual([m.title for m in visible], ["First", "Second"])

    def test_featured_media_unique_per_product(self):
        first = ProductMedia.objects.create(
            product=self.product,
            media_type=ProductMedia.TYPE_IMAGE,
            title="A",
            image=_png("a.png"),
            is_featured=True,
            display_order=10,
        )
        second = ProductMedia.objects.create(
            product=self.product,
            media_type=ProductMedia.TYPE_VIDEO,
            video_source=ProductMedia.SOURCE_UPLOAD,
            title="B",
            video_file=_tiny_mp4(),
            is_featured=True,
            display_order=20,
        )
        first.refresh_from_db()
        second.refresh_from_db()
        self.assertFalse(first.is_featured)
        self.assertTrue(second.is_featured)

    def test_detail_serializer_mixed_media_list_stays_light(self):
        ProductMedia.objects.create(
            product=self.product,
            media_type=ProductMedia.TYPE_IMAGE,
            title="Shot",
            image=_png("shot.png"),
            is_featured=True,
            display_order=10,
        )
        ProductMedia.objects.create(
            product=self.product,
            media_type=ProductMedia.TYPE_VIDEO,
            video_source=ProductMedia.SOURCE_UPLOAD,
            title="Clip",
            video_file=_tiny_mp4(),
            thumbnail=_png("t.png"),
            display_order=20,
        )
        ProductMedia.objects.create(
            product=self.product,
            media_type=ProductMedia.TYPE_VIDEO,
            video_source=ProductMedia.SOURCE_EXTERNAL,
            title="Ext",
            video_url="https://example.com/watch",
            display_order=30,
        )

        detail = self.client.get("/api/v1/public/products/media-product/").json()
        self.assertEqual(len(detail["media"]), 3)
        types = [m["media_type"] for m in detail["media"]]
        self.assertEqual(types, ["image", "video", "video"])
        self.assertTrue(detail["media"][0]["image_url"])
        self.assertTrue(detail["media"][0]["is_featured"])
        self.assertTrue(detail["media"][1]["video_file_url"])
        self.assertEqual(detail["media"][1]["video_source"], "upload")
        self.assertEqual(detail["media"][2]["video_url"], "https://example.com/watch")

        listing = self.client.get("/api/v1/public/products/").json()
        row = next(item for item in listing if item["slug"] == "media-product")
        self.assertNotIn("media", row)
        self.assertIn("poster_url", row)
        self.assertIn("short_description", row)
