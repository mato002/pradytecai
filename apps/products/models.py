from django.core.exceptions import ValidationError
from django.db import models
from django.utils.text import slugify


def validate_product_poster(file):
    """Restrict poster uploads to common image types and a 5 MB size cap."""
    max_bytes = 5 * 1024 * 1024
    allowed = {
        "image/jpeg",
        "image/png",
        "image/webp",
        "image/gif",
    }
    content_type = getattr(file, "content_type", None) or ""
    name = (getattr(file, "name", "") or "").lower()
    ext_ok = name.endswith((".jpg", ".jpeg", ".png", ".webp", ".gif"))
    if content_type and content_type not in allowed and not ext_ok:
        raise ValidationError("Poster must be a JPEG, PNG, WebP, or GIF image.")
    if not ext_ok and not content_type:
        raise ValidationError("Poster must be a JPEG, PNG, WebP, or GIF image.")
    size = getattr(file, "size", None)
    if size is not None and size > max_bytes:
        raise ValidationError("Poster must be 5 MB or smaller.")


class Product(models.Model):
    """Authoritative product catalog — admin + public website share this table."""

    CTA_CONTACT = "contact"
    CTA_DEMO = "demo"
    CTA_EXTERNAL = "external"
    CTA_TYPE_CHOICES = (
        (CTA_CONTACT, "Contact form"),
        (CTA_DEMO, "Request demo"),
        (CTA_EXTERNAL, "External URL"),
    )

    id = models.BigAutoField(primary_key=True)
    name = models.CharField(max_length=255)
    slug = models.SlugField(max_length=255, null=True, blank=True, unique=True)
    description = models.TextField(null=True, blank=True)
    short = models.CharField(
        max_length=500,
        null=True,
        blank=True,
        help_text="Short description shown on product cards.",
    )
    type = models.CharField(max_length=255, null=True, blank=True)
    market = models.CharField(max_length=255, null=True, blank=True)
    code = models.CharField(max_length=255, null=True, blank=True)
    url = models.CharField(
        max_length=500,
        null=True,
        blank=True,
        help_text="Optional CTA destination URL (external or internal path).",
    )
    poster = models.ImageField(
        upload_to="products/posters/",
        null=True,
        blank=True,
        validators=[validate_product_poster],
    )
    icon = models.CharField(
        max_length=64,
        null=True,
        blank=True,
        help_text="Optional icon key for marketing UI (e.g. finance, users).",
    )
    cta_label = models.CharField(max_length=100, null=True, blank=True, default="Request demo")
    cta_type = models.CharField(
        max_length=32,
        choices=CTA_TYPE_CHOICES,
        default=CTA_DEMO,
        blank=True,
    )
    is_active = models.BooleanField(default=True)
    is_featured = models.BooleanField(default=False)
    order = models.IntegerField(default=0, help_text="Display order (lower first).")
    group_key = models.CharField(
        max_length=64,
        null=True,
        blank=True,
        help_text="Optional portfolio group key: finance, assets, commerce.",
    )
    last_marketed_at = models.DateTimeField(null=True, blank=True)
    created_at = models.DateTimeField(null=True, blank=True, auto_now_add=True)
    updated_at = models.DateTimeField(null=True, blank=True, auto_now=True)

    class Meta:
        db_table = "products"
        ordering = ["order", "name"]

    def __str__(self):
        return self.name

    def save(self, *args, **kwargs):
        if not self.slug and self.name:
            base = slugify(self.name)[:240] or "product"
            candidate = base
            n = 2
            while Product.objects.filter(slug=candidate).exclude(pk=self.pk).exists():
                candidate = f"{base}-{n}"
                n += 1
            self.slug = candidate
        super().save(*args, **kwargs)

    @property
    def short_description(self):
        return self.short

    @property
    def display_order(self):
        return self.order

    @property
    def cta_url(self):
        return self.url
