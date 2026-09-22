from pathlib import Path

from django.conf import settings
from django.shortcuts import render
from django.views.decorators.csrf import ensure_csrf_cookie


@ensure_csrf_cookie
def spa_index(request):
    """Serve the React SPA shell. Assets come from Vite build via /static/."""
    dist_js = Path(settings.BASE_DIR) / "react" / "dist" / "assets" / "index.js"
    build_id = str(int(dist_js.stat().st_mtime)) if dist_js.exists() else "dev"
    return render(
        request,
        "spa.html",
        {
            "debug": settings.DEBUG,
            "build_id": build_id,
        },
    )
