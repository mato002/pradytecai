"""DRF permission classes mirroring Spatie permission middleware."""

from rest_framework.permissions import BasePermission


class HasPerm(BasePermission):
    """Require a named Spatie-style permission (or super-admin)."""

    permission_name: str = ""

    def has_permission(self, request, view):
        user = request.user
        if not user or not user.is_authenticated:
            return False
        name = getattr(view, "required_permission", None) or self.permission_name
        if not name:
            return True
        # Support "a|b" like Laravel permission:a|b
        for part in name.split("|"):
            if user.has_perm_name(part.strip()):
                return True
        return False


def require_perm(name: str):
    class _P(HasPerm):
        permission_name = name

    _P.__name__ = f"Require_{name.replace('.', '_').replace('|', '_')}"
    return _P
