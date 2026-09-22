"""Parity smoke tests for auth, visibility, and Celery task registration."""

from django.test import Client, TestCase, override_settings
from django.urls import reverse

from apps.accounts.models import Permission, Role, RoleHasPermission, User, UserAccessScope
from apps.products.models import Product


class AuthParityTests(TestCase):
    def setUp(self):
        self.client = Client(enforce_csrf_checks=False)
        self.user = User.objects.create(name="Admin", email="admin@example.com", role="super_admin", is_super_admin=True)
        self.user.set_password("secret123")
        self.user.save()

    def test_login_and_me(self):
        r = self.client.post(
            "/api/v1/auth/login/",
            data={"email": "admin@example.com", "password": "secret123"},
            content_type="application/json",
        )
        self.assertEqual(r.status_code, 200)
        me = self.client.get("/api/v1/auth/me/")
        self.assertEqual(me.status_code, 200)
        self.assertEqual(me.json()["user"]["email"], "admin@example.com")

    def test_laravel_bcrypt_roundtrip(self):
        raw = "secret123"
        self.assertTrue(self.user.check_password(raw))
        self.assertTrue(self.user.password.startswith("$2y$") or self.user.password.startswith("$2b$"))


class VisibilityTests(TestCase):
    def setUp(self):
        self.p1 = Product.objects.create(name="A", is_active=True)
        self.p2 = Product.objects.create(name="B", is_active=True)
        self.user = User.objects.create(name="Scoped", email="scoped@example.com", role="marketing_analyst")
        self.user.set_password("secret123")
        self.user.save()
        perm, _ = Permission.objects.get_or_create(name="products.view", guard_name="web")
        role, _ = Role.objects.get_or_create(name="marketing_analyst", guard_name="web")
        RoleHasPermission.objects.get_or_create(role=role, permission=perm)
        self.user.assign_role(role)
        UserAccessScope.objects.create(user=self.user, scope_type="product", scope_id=self.p1.id)
        self.client = Client(enforce_csrf_checks=False)
        self.client.force_login(self.user)

    def test_product_list_scoped(self):
        r = self.client.get("/api/v1/products/")
        self.assertEqual(r.status_code, 200)
        ids = [row["id"] for row in r.json()["results"]]
        self.assertIn(self.p1.id, ids)
        self.assertNotIn(self.p2.id, ids)


class HealthTests(TestCase):
    def test_up(self):
        r = self.client.get("/up")
        self.assertEqual(r.status_code, 200)
        self.assertEqual(r.json()["status"], "ok")


class CeleryTaskImportTests(TestCase):
    def test_tasks_importable(self):
        from apps.analytics.tasks import evaluate_marketing_pulse, sync_ga4, sync_social_metrics
        from apps.content.tasks import publish_content, publish_due_content
        from apps.leads.tasks import send_lead_email

        self.assertTrue(callable(publish_content))
        self.assertTrue(callable(publish_due_content))
        self.assertTrue(callable(evaluate_marketing_pulse))
        self.assertTrue(callable(sync_social_metrics))
        self.assertTrue(callable(sync_ga4))
        self.assertTrue(callable(send_lead_email))
