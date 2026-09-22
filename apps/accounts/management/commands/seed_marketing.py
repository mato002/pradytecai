from django.core.management.base import BaseCommand
from django_celery_beat.models import CrontabSchedule, PeriodicTask

from apps.accounts.models import Permission, Role, RoleHasPermission
from apps.accounts.permission_data import MARKETING_PERMISSIONS, ROLE_PERMISSIONS


class Command(BaseCommand):
    help = "Seed Spatie-compatible permissions/roles and Celery Beat schedules (UTC)."

    def handle(self, *args, **options):
        for name in MARKETING_PERMISSIONS:
            Permission.objects.get_or_create(name=name, guard_name="web")
        self.stdout.write(self.style.SUCCESS(f"Permissions: {len(MARKETING_PERMISSIONS)}"))

        for role_name, perms in ROLE_PERMISSIONS.items():
            role, _ = Role.objects.get_or_create(name=role_name, guard_name="web")
            if perms == "*":
                wanted = list(Permission.objects.filter(guard_name="web"))
            else:
                wanted = list(Permission.objects.filter(name__in=perms, guard_name="web"))
            RoleHasPermission.objects.filter(role=role).delete()
            for p in wanted:
                RoleHasPermission.objects.get_or_create(role=role, permission=p)
            self.stdout.write(f"Role {role_name}: {len(wanted)} permissions")

        # Celery Beat — UTC wall times matching Laravel routes/console.php
        hourly, _ = CrontabSchedule.objects.get_or_create(
            minute="0", hour="*", day_of_week="*", day_of_month="*", month_of_year="*", timezone="UTC"
        )
        PeriodicTask.objects.update_or_create(
            name="marketing-pulse-hourly",
            defaults={
                "crontab": hourly,
                "task": "analytics.tasks.evaluate_marketing_pulse",
                "enabled": True,
            },
        )
        daily_2, _ = CrontabSchedule.objects.get_or_create(
            minute="0", hour="2", day_of_week="*", day_of_month="*", month_of_year="*", timezone="UTC"
        )
        PeriodicTask.objects.update_or_create(
            name="sync-social-metrics-0200-utc",
            defaults={
                "crontab": daily_2,
                "task": "analytics.tasks.sync_social_metrics",
                "enabled": True,
            },
        )
        daily_230, _ = CrontabSchedule.objects.get_or_create(
            minute="30", hour="2", day_of_week="*", day_of_month="*", month_of_year="*", timezone="UTC"
        )
        PeriodicTask.objects.update_or_create(
            name="sync-ga4-0230-utc",
            defaults={
                "crontab": daily_230,
                "task": "analytics.tasks.sync_ga4",
                "enabled": True,
            },
        )
        every_5, _ = CrontabSchedule.objects.get_or_create(
            minute="*/5", hour="*", day_of_week="*", day_of_month="*", month_of_year="*", timezone="UTC"
        )
        PeriodicTask.objects.update_or_create(
            name="publish-due-content",
            defaults={
                "crontab": every_5,
                "task": "content.tasks.publish_due_content",
                "enabled": True,
            },
        )
        self.stdout.write(self.style.SUCCESS("Celery Beat schedules seeded (UTC)."))
