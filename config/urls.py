from pathlib import Path

from django.conf import settings
from django.conf.urls.static import static
from django.contrib import admin
from django.urls import include, path, re_path
from django.views.static import serve

from apps.analytics.api.views import tracked_link_redirect
from apps.core.public_forms import careers_apply_dispatch, contact_dispatch, newsletter_dispatch
from apps.core.spa import spa_index
from apps.core.views import health

urlpatterns = [
    path("admin-django/", admin.site.urls),
    path("up", health),
    path("health", health),
    path("api/v1/", include("config.api_urls")),
    path("t/<str:code>", tracked_link_redirect, name="tracked.show"),
    path("contact", contact_dispatch),
    path("newsletter/subscribe", newsletter_dispatch),
    path("careers/apply", careers_apply_dispatch),
    re_path(
        r"^(?!api/|media/|static/|assets/|t/|up$|health$|admin-django/).*$",
        spa_index,
        name="spa",
    ),
]

# Media is also served by Apache from public_html/media (symlink to ./media).
# Keep Django serving as a fallback for local/dev and misconfigured proxies.
urlpatterns += static(settings.MEDIA_URL, document_root=settings.MEDIA_ROOT)

if settings.DEBUG:
    _react_assets = Path(settings.BASE_DIR) / "react" / "dist" / "assets"
    if _react_assets.exists():
        urlpatterns += [
            re_path(
                r"^assets/(?P<path>.*)$",
                serve,
                {"document_root": str(_react_assets)},
            ),
        ]
