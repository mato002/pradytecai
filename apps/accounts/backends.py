from django.contrib.auth.backends import ModelBackend
from django.contrib.auth.hashers import check_password

from apps.accounts.hashers import LaravelBcryptPasswordHasher, is_laravel_bcrypt


class LaravelCompatibleBackend(ModelBackend):
    """Authenticate against users table including Laravel bcrypt password hashes."""

    def authenticate(self, request, username=None, password=None, **kwargs):
        email = (kwargs.get("email") or username or "").strip()
        if not email or password is None:
            return None
        from apps.accounts.models import User

        try:
            user = User.objects.get(email__iexact=email)
        except User.DoesNotExist:
            # Timing mitigation — do not reveal whether email exists
            LaravelBcryptPasswordHasher().encode(password, LaravelBcryptPasswordHasher().salt())
            return None
        if self._check_password(password, user.password) and self.user_can_authenticate(user):
            return user
        return None

    def user_can_authenticate(self, user):
        # Custom User.is_active is a property (always True); still respect Django API
        return bool(getattr(user, "is_active", True))

    def _check_password(self, raw: str, encoded: str) -> bool:
        if not encoded:
            return False
        if is_laravel_bcrypt(encoded):
            return LaravelBcryptPasswordHasher().verify(raw, encoded)
        return check_password(raw, encoded)
