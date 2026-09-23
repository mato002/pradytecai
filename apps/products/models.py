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
        raise ValidationError("Image must be a JPEG, PNG, WebP, or GIF.")
    if not ext_ok and not content_type:
        raise ValidationError("Image must be a JPEG, PNG, WebP, or GIF.")
    size = getattr(file, "size", None)
    if size is not None and size > max_bytes:
        raise ValidationError("Image must be 5 MB or smaller.")


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
    hero_image = models.ImageField(
        upload_to="products/heroes/",
        null=True,
        blank=True,
        validators=[validate_product_poster],
        help_text="Optional hero image. The public page falls back to the poster.",
    )
    mobile_image = models.ImageField(
        upload_to="products/mobile/",
        null=True,
        blank=True,
        validators=[validate_product_poster],
        help_text="Optional mobile screenshot shown on small screens.",
    )
    tagline = models.CharField(
        max_length=255,
        null=True,
        blank=True,
        help_text="Short line under the product name on the detail page.",
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
    secondary_cta_label = models.CharField(max_length=100, null=True, blank=True)
    secondary_cta_url = models.CharField(
        max_length=500,
        null=True,
        blank=True,
        help_text="Optional secondary CTA destination (external or internal path).",
    )
    secondary_cta_type = models.CharField(
        max_length=32,
        choices=CTA_TYPE_CHOICES,
        null=True,
        blank=True,
    )
    seo_title = models.CharField(max_length=255, null=True, blank=True)
    seo_description = models.CharField(max_length=320, null=True, blank=True)
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

    @property
    def overview(self):
        return self.description


class OrderedProductItem(models.Model):
    """Shared ordering and visibility for product-detail child rows."""

    display_order = models.PositiveIntegerField(default=0)
    is_active = models.BooleanField(default=True)

    class Meta:
        abstract = True
        ordering = ["display_order", "id"]


class ProductAudience(OrderedProductItem):
    product = models.ForeignKey(Product, on_delete=models.CASCADE, related_name="audiences")
    title = models.CharField(max_length=160)
    description = models.TextField(null=True, blank=True)
    icon = models.CharField(max_length=64, null=True, blank=True)

    class Meta(OrderedProductItem.Meta):
        abstract = False
        db_table = "product_audiences"
        verbose_name_plural = "audiences"

    def __str__(self):
        return self.title


class ProductHighlight(OrderedProductItem):
    product = models.ForeignKey(Product, on_delete=models.CASCADE, related_name="highlights")
    title = models.CharField(max_length=160)
    short_description = models.CharField(max_length=255, null=True, blank=True)
    icon = models.CharField(max_length=64, null=True, blank=True)

    class Meta(OrderedProductItem.Meta):
        abstract = False
        db_table = "product_highlights"

    def __str__(self):
        return self.title


class ProductProblem(OrderedProductItem):
    product = models.ForeignKey(Product, on_delete=models.CASCADE, related_name="problems")
    title = models.CharField(max_length=160)
    description = models.TextField(null=True, blank=True)
    icon = models.CharField(max_length=64, null=True, blank=True)

    class Meta(OrderedProductItem.Meta):
        abstract = False
        db_table = "product_problems"

    def __str__(self):
        return self.title


class ProductCapabilityGroup(OrderedProductItem):
    product = models.ForeignKey(Product, on_delete=models.CASCADE, related_name="capability_groups")
    title = models.CharField(max_length=160)
    description = models.TextField(null=True, blank=True)
    icon = models.CharField(max_length=64, null=True, blank=True)

    class Meta(OrderedProductItem.Meta):
        abstract = False
        db_table = "product_capability_groups"
        verbose_name = "capability group"

    def __str__(self):
        return f"{self.product}: {self.title}"


class ProductCapability(OrderedProductItem):
    capability_group = models.ForeignKey(
        ProductCapabilityGroup,
        on_delete=models.CASCADE,
        related_name="capabilities",
    )
    title = models.CharField(max_length=160)
    description = models.TextField(null=True, blank=True)

    class Meta(OrderedProductItem.Meta):
        abstract = False
        db_table = "product_capabilities"
        verbose_name_plural = "capabilities"

    def __str__(self):
        return self.title


class ProductWorkflowStep(OrderedProductItem):
    product = models.ForeignKey(Product, on_delete=models.CASCADE, related_name="workflow_steps")
    title = models.CharField(max_length=160)
    description = models.TextField(null=True, blank=True)
    step_number = models.PositiveIntegerField(default=1)
    icon = models.CharField(max_length=64, null=True, blank=True)

    class Meta(OrderedProductItem.Meta):
        abstract = False
        db_table = "product_workflow_steps"
        verbose_name = "workflow step"

    def __str__(self):
        return self.title


class ProductMedia(OrderedProductItem):
    TYPE_SCREENSHOT = "screenshot"
    TYPE_MOBILE = "mobile"
    TYPE_DASHBOARD = "dashboard"
    TYPE_POSTER = "poster"
    TYPE_FEATURE = "feature"
    TYPE_WORKFLOW = "workflow"
    TYPE_OTHER = "other"
    MEDIA_TYPE_CHOICES = (
        (TYPE_SCREENSHOT, "Screenshot"),
        (TYPE_MOBILE, "Mobile"),
        (TYPE_DASHBOARD, "Dashboard"),
        (TYPE_POSTER, "Poster"),
        (TYPE_FEATURE, "Feature"),
        (TYPE_WORKFLOW, "Workflow"),
        (TYPE_OTHER, "Other"),
    )

    product = models.ForeignKey(Product, on_delete=models.CASCADE, related_name="media_items")
    title = models.CharField(max_length=160, null=True, blank=True)
    caption = models.TextField(null=True, blank=True)
    image = models.ImageField(
        upload_to="products/media/",
        null=True,
        blank=True,
        validators=[validate_product_poster],
    )
    media_type = models.CharField(max_length=32, choices=MEDIA_TYPE_CHOICES, default=TYPE_SCREENSHOT)
    alt_text = models.CharField(max_length=255, null=True, blank=True)

    class Meta(OrderedProductItem.Meta):
        abstract = False
        db_table = "product_media"
        verbose_name = "product media"
        verbose_name_plural = "product media"

    def __str__(self):
        return self.title or self.alt_text or f"Media {self.pk}"


class ProductIntegration(OrderedProductItem):
    product = models.ForeignKey(Product, on_delete=models.CASCADE, related_name="integrations")
    name = models.CharField(max_length=160)
    description = models.TextField(null=True, blank=True)
    logo = models.ImageField(
        upload_to="products/integrations/",
        null=True,
        blank=True,
        validators=[validate_product_poster],
    )

    class Meta(OrderedProductItem.Meta):
        abstract = False
        db_table = "product_integrations"

    def __str__(self):
        return self.name


class ProductControl(OrderedProductItem):
    product = models.ForeignKey(Product, on_delete=models.CASCADE, related_name="controls")
    title = models.CharField(max_length=160)
    description = models.TextField(null=True, blank=True)
    icon = models.CharField(max_length=64, null=True, blank=True)

    class Meta(OrderedProductItem.Meta):
        abstract = False
        db_table = "product_controls"
        verbose_name = "control / security feature"

    def __str__(self):
        return self.title


class ProductOutcome(OrderedProductItem):
    product = models.ForeignKey(Product, on_delete=models.CASCADE, related_name="outcomes")
    title = models.CharField(max_length=160)
    description = models.TextField(null=True, blank=True)

    class Meta(OrderedProductItem.Meta):
        abstract = False
        db_table = "product_outcomes"

    def __str__(self):
        return self.title


class ProductImplementationStep(OrderedProductItem):
    product = models.ForeignKey(Product, on_delete=models.CASCADE, related_name="implementation_steps")
    title = models.CharField(max_length=160)
    description = models.TextField(null=True, blank=True)
    step_number = models.PositiveIntegerField(default=1)

    class Meta(OrderedProductItem.Meta):
        abstract = False
        db_table = "product_implementation_steps"
        verbose_name = "implementation step"

    def __str__(self):
        return self.title


class ProductFAQ(OrderedProductItem):
    product = models.ForeignKey(Product, on_delete=models.CASCADE, related_name="faqs")
    question = models.CharField(max_length=255)
    answer = models.TextField()

    class Meta(OrderedProductItem.Meta):
        abstract = False
        db_table = "product_faqs"
        verbose_name = "FAQ"
        verbose_name_plural = "FAQs"

    def __str__(self):
        return self.question


class ProductPageSection(models.Model):
    """Presentation order for a product page. Content lives on the related models."""

    HIGHLIGHTS = "highlights"
    AUDIENCE = "audience"
    PROBLEMS = "problems"
    CAPABILITIES = "capabilities"
    WORKFLOW = "workflow"
    MEDIA = "media"
    INTEGRATIONS = "integrations"
    CONTROLS = "controls"
    OUTCOMES = "outcomes"
    IMPLEMENTATION = "implementation"
    FAQ = "faq"
    CUSTOM = "custom"
    SECTION_CHOICES = (
        (HIGHLIGHTS, "Highlights"),
        (AUDIENCE, "Audience"),
        (PROBLEMS, "Problems"),
        (CAPABILITIES, "Capabilities"),
        (WORKFLOW, "Workflow"),
        (MEDIA, "Media"),
        (INTEGRATIONS, "Integrations"),
        (CONTROLS, "Controls"),
        (OUTCOMES, "Outcomes"),
        (IMPLEMENTATION, "Implementation"),
        (FAQ, "FAQ"),
        (CUSTOM, "Custom"),
    )

    product = models.ForeignKey(Product, on_delete=models.CASCADE, related_name="page_sections")
    section_type = models.CharField(max_length=32, choices=SECTION_CHOICES)
    title_override = models.CharField(max_length=160, null=True, blank=True)
    subtitle = models.CharField(max_length=300, null=True, blank=True)
    display_order = models.PositiveIntegerField(default=0)
    is_enabled = models.BooleanField(default=True)

    class Meta:
        db_table = "product_page_sections"
        ordering = ["display_order", "id"]
        verbose_name = "page section"
        constraints = [
            models.UniqueConstraint(
                fields=["product", "section_type"],
                name="uniq_product_page_section_type",
            )
        ]

    def __str__(self):
        return f"{self.product}: {self.section_type}"


class ProductCustomSection(OrderedProductItem):
    LAYOUT_TEXT = "text"
    LAYOUT_TEXT_IMAGE = "text_image"
    LAYOUT_IMAGE_TEXT = "image_text"
    LAYOUT_CALLOUT = "callout"
    LAYOUT_CHOICES = (
        (LAYOUT_TEXT, "Text"),
        (LAYOUT_TEXT_IMAGE, "Text and image"),
        (LAYOUT_IMAGE_TEXT, "Image and text"),
        (LAYOUT_CALLOUT, "Callout"),
    )

    product = models.ForeignKey(Product, on_delete=models.CASCADE, related_name="custom_sections")
    title = models.CharField(max_length=160)
    subtitle = models.CharField(max_length=300, null=True, blank=True)
    body = models.TextField(null=True, blank=True)
    image = models.ImageField(
        upload_to="products/custom/",
        null=True,
        blank=True,
        validators=[validate_product_poster],
    )
    layout_type = models.CharField(max_length=32, choices=LAYOUT_CHOICES, default=LAYOUT_TEXT)

    class Meta(OrderedProductItem.Meta):
        abstract = False
        db_table = "product_custom_sections"
        verbose_name = "custom section"

    def __str__(self):
        return self.title
