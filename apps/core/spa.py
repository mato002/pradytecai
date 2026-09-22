from pathlib import Path

from django.conf import settings
from django.http import HttpResponse
from django.shortcuts import render
from django.views.decorators.csrf import ensure_csrf_cookie


@ensure_csrf_cookie
def spa_index(request):
    """Serve the React SPA shell.

    Production: Apache serves public_html/index.html directly.
    Local/fallback: prefer the Vite build output (hashed /assets/*).
    """
    built = Path(settings.BASE_DIR) / "react" / "dist" / "index.html"
    if built.exists():
        return HttpResponse(built.read_text(encoding="utf-8"), content_type="text/html")
    return render(
        request,
        "spa.html",
        {
            "debug": settings.DEBUG,
            "build_id": "dev",
        },
    )
