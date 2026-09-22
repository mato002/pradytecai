"""Shared middleware ported from Laravel SetProductFilter / CaptureUtmParameters."""

from __future__ import annotations


class ProductFilterMiddleware:
    """Store selected marketing product id on the session (admin product switcher)."""

    SESSION_KEY = "marketing.product_id"

    def __init__(self, get_response):
        self.get_response = get_response

    def __call__(self, request):
        product_id = request.GET.get("product_id") or request.headers.get("X-Product-Id")
        if product_id is not None:
            if product_id in ("", "0", "all"):
                request.session.pop(self.SESSION_KEY, None)
            else:
                try:
                    request.session[self.SESSION_KEY] = int(product_id)
                except (TypeError, ValueError):
                    pass
        request.product_filter_id = request.session.get(self.SESSION_KEY)
        return self.get_response(request)


class CaptureUtmMiddleware:
    """Capture UTM query params into the session for lead attribution."""

    UTM_KEYS = (
        "utm_source",
        "utm_medium",
        "utm_campaign",
        "utm_content",
        "utm_term",
    )

    def __init__(self, get_response):
        self.get_response = get_response

    def __call__(self, request):
        for key in self.UTM_KEYS:
            value = request.GET.get(key)
            if value:
                request.session[f"utm.{key}"] = value
        return self.get_response(request)
