"""Tests for public contact/demo requests and admin enquiry status."""

from django.test import Client, TestCase

from apps.accounts.models import User
from apps.leads.models import ContactMessage, DemoRequest
from apps.products.models import Product


class ContactFlowTests(TestCase):
    def setUp(self):
        self.client = Client(enforce_csrf_checks=False)
        self.product = Product.objects.create(
            name="Prady Microfinance",
            slug="prady-microfinance",
            is_active=True,
            short="Short",
        )
        self.admin = User.objects.create(
            name="Admin", email="admin@example.com", role="super_admin", is_super_admin=True
        )
        self.admin.set_password("secret123")
        self.admin.save()

    def test_general_contact(self):
        r = self.client.post(
            "/api/v1/public/contact/",
            data={
                "name": "Jane Doe",
                "email": "jane@example.com",
                "phone": "+254722295194",
                "subject": "General question",
                "message": "Hello team",
                "request_type": "contact",
                "source": "contact_page",
            },
            content_type="application/json",
        )
        self.assertEqual(r.status_code, 201, r.content)
        msg = ContactMessage.objects.get(pk=r.json()["id"])
        self.assertEqual(msg.status, "new")
        self.assertIsNone(msg.product_id)
        self.assertEqual(msg.source, "contact_page")

    def test_product_demo_request(self):
        r = self.client.post(
            "/api/v1/public/contact/",
            data={
                "name": "John Demo",
                "email": "john@example.com",
                "phone": "0722295194",
                "company": "Acme Ltd",
                "message": "Want a demo",
                "request_type": "demo",
                "product_slug": "prady-microfinance",
                "preferred_at": "2030-06-01T10:00:00+03:00",
                "source": "product_page",
                "landing_page": "/products/prady-microfinance",
            },
            content_type="application/json",
        )
        self.assertEqual(r.status_code, 201, r.content)
        msg = ContactMessage.objects.get(pk=r.json()["id"])
        self.assertEqual(msg.product_id, self.product.id)
        self.assertEqual(msg.request_type, "demo")
        demo = DemoRequest.objects.get(contact_message=msg)
        self.assertEqual(demo.product_id, self.product.id)
        self.assertIsNotNone(demo.preferred_at)

    def test_invalid_email_rejected(self):
        r = self.client.post(
            "/api/v1/public/contact/",
            data={
                "name": "Bad",
                "email": "not-an-email",
                "subject": "X",
                "message": "Y",
            },
            content_type="application/json",
        )
        self.assertEqual(r.status_code, 400)

    def test_admin_status_update(self):
        msg = ContactMessage.objects.create(
            name="Lead",
            email="lead@example.com",
            subject="Demo",
            message="Please call",
            product=self.product,
            status="new",
            request_type="demo",
        )
        admin_client = Client(enforce_csrf_checks=False)
        admin_client.force_login(self.admin)

        r = admin_client.get("/api/v1/enquiries/")
        self.assertEqual(r.status_code, 200)
        ids = [row["id"] for row in r.json()["results"]]
        self.assertIn(msg.id, ids)

        r = admin_client.post(
            f"/api/v1/enquiries/{msg.id}/set_status/",
            data={"status": "in_progress"},
            content_type="application/json",
        )
        self.assertEqual(r.status_code, 200, r.content)
        msg.refresh_from_db()
        self.assertEqual(msg.status, "in_progress")

        # Unauthorized cannot update
        anon = Client(enforce_csrf_checks=False)
        r = anon.post(
            f"/api/v1/enquiries/{msg.id}/set_status/",
            data={"status": "closed"},
            content_type="application/json",
        )
        self.assertIn(r.status_code, (401, 403))
