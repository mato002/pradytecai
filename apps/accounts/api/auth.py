from django.contrib.auth import authenticate, login, logout
from django.middleware.csrf import get_token
from django.views.decorators.csrf import ensure_csrf_cookie
from rest_framework import serializers, status
from rest_framework.decorators import api_view, authentication_classes, permission_classes
from rest_framework.permissions import AllowAny, IsAuthenticated
from rest_framework.response import Response


class LoginSerializer(serializers.Serializer):
    email = serializers.EmailField()
    password = serializers.CharField(write_only=True)


@api_view(["GET"])
@permission_classes([AllowAny])
@authentication_classes([])
@ensure_csrf_cookie
def csrf(request):
    return Response({"csrfToken": get_token(request)})


@api_view(["POST"])
@permission_classes([AllowAny])
@authentication_classes([])
def login_view(request):
    """
    Session login without DRF SessionAuthentication CSRF gate.

    The SPA is served by Apache (static index.html), so the first visit does not
    go through Django's ensure_csrf_cookie. Behind a reverse proxy, cookie/CSRF
    bootstrap can still fail; authenticating this view with [] avoids that deadlock.
    After login(), the session cookie is set; later mutating API calls still use CSRF.
    """
    ser = LoginSerializer(data=request.data)
    ser.is_valid(raise_exception=True)
    email = ser.validated_data["email"].strip().lower()
    password = ser.validated_data["password"]
    # Pass both email= and username= so ModelBackend-style and custom backends work
    user = authenticate(request, username=email, email=email, password=password)
    if user is None:
        return Response({"detail": "Invalid credentials."}, status=status.HTTP_401_UNAUTHORIZED)
    login(request, user)
    # Persist session so production reverse proxies keep the auth cookie
    request.session.cycle_key()
    request.session.save()
    return Response({"user": _user_payload(user)})


@api_view(["POST"])
@permission_classes([IsAuthenticated])
def logout_view(request):
    logout(request)
    return Response({"detail": "Logged out."})


@api_view(["GET"])
@permission_classes([IsAuthenticated])
def me(request):
    return Response({"user": _user_payload(request.user)})


def _user_payload(user):
    return {
        "id": user.id,
        "name": user.name,
        "email": user.email,
        "role": user.role,
        "is_super_admin": user.is_super_admin_user(),
        "roles": user.get_role_names(),
        "permissions": user.permission_names(),
        "product_scopes": list(
            user.access_scopes.filter(scope_type="product").values_list("scope_id", flat=True)
        ),
        "social_account_scopes": list(
            user.access_scopes.filter(scope_type="social_account").values_list(
                "scope_id", flat=True
            )
        ),
    }
