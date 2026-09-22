"""Create or update a super-admin for local/prod bootstrap."""

from django.core.management.base import BaseCommand

from apps.accounts.models import Role, User


class Command(BaseCommand):
    help = "Ensure an admin user exists (Laravel-compatible bcrypt password)."

    def add_arguments(self, parser):
        parser.add_argument("--email", default="admin@pradytecai.com")
        parser.add_argument("--password", required=True, help="Plain password to set")
        parser.add_argument("--name", default="Pradytec Admin")

    def handle(self, *args, **options):
        email = options["email"].strip().lower()
        password = options["password"]
        name = options["name"]

        user = User.objects.filter(email__iexact=email).first()
        created = user is None
        if created:
            user = User(email=email, name=name)
        user.name = name
        user.role = "super_admin"
        user.is_super_admin = True
        user.set_password(password)
        user.save()

        role = Role.objects.filter(name="super_admin", guard_name="web").first()
        if role:
            user.assign_role(role)

        ok = user.check_password(password)
        auth_ok = False
        from django.contrib.auth import authenticate

        auth_user = authenticate(username=email, email=email, password=password)
        auth_ok = auth_user is not None

        self.stdout.write(
            self.style.SUCCESS(
                f"{'Created' if created else 'Updated'} {user.email} | "
                f"check_password={ok} | authenticate={auth_ok}"
            )
        )
