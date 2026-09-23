from django.conf import settings
from django.core.exceptions import ValidationError
from django.db import models
from django.db.models import Q
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


def product_video_max_bytes():
    return int(getattr(settings, "PRODUCT_VIDEO_MAX_BYTES", 80 * 1024 * 1024))


def validate_product_video(file):
    """Restrict product videos to MP4/WebM within the configured size ceiling."""
    max_bytes = product_video_max_bytes()
    allowed_mime = {"video/mp4", "video/webm"}
    content_type = (getattr(file, "content_type", None) or "").lower()
    name = (getattr(file, "name", "") or "").lower()
    ext_ok = name.endswith((".mp4", ".webm"))
    if content_type and content_type not in allowed_mime and not ext_ok:
        raise ValidationError("Video must be MP4 or WebM.")
    if not ext_ok and not content_type:
        raise ValidationError("Video must be MP4 or WebM.")
    if content_type and content_type not in allowed_mime and ext_ok:
        # Extension ok but MIME wrong — still reject when MIME is present and wrong
        if content_type.startswith("video/") and content_type not in allowed_mime:
            raise ValidationError("Video must be MP4 or WebM.")
    size = getattr(file, "size", None)
    if size is not None and size > max_bytes:
        mb = max_bytes / (1024 * 1024)
        raise ValidationError(f"Video must be {mb:g} MB or smaller.")


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


class ProductMediaQuerySet(models.QuerySet):
    def publicly_visible(self):
        """Active rows that have a renderable image or video payload."""
        image_ok = Q(media_type=ProductMedia.TYPE_IMAGE) & ~Q(image="")
        video_upload = (
            Q(media_type=ProductMedia.TYPE_VIDEO)
            & Q(video_source=ProductMedia.SOURCE_UPLOAD)
            & ~Q(video_file="")
        )
        video_external = (
            Q(media_type=ProductMedia.TYPE_VIDEO)
            & Q(video_source=ProductMedia.SOURCE_EXTERNAL)
            & ~Q(video_url="")
            & Q(video_url__isnull=False)
        )
        return (
            self.filter(is_active=True)
            .filter(image_ok | video_upload | video_external)
            .order_by("display_order", "id")
        )


class ProductMedia(OrderedProductItem):
    """Screenshots and videos for the public product detail gallery."""

    TYPE_IMAGE = "image"
    TYPE_VIDEO = "video"
    MEDIA_TYPE_CHOICES = (
        (TYPE_IMAGE, "Image"),
        (TYPE_VIDEO, "Video"),
    )

    # Optional labels for image rows (preserved from pre-video media_type values).
    CATEGORY_SCREENSHOT = "screenshot"
    CATEGORY_MOBILE = "mobile"
    CATEGORY_DASHBOARD = "dashboard"
    CATEGORY_POSTER = "poster"
    CATEGORY_FEATURE = "feature"
    CATEGORY_WORKFLOW = "workflow"
    CATEGORY_OTHER = "other"
    IMAGE_CATEGORY_CHOICES = (
        (CATEGORY_SCREENSHOT, "Screenshot"),
        (CATEGORY_MOBILE, "Mobile"),
        (CATEGORY_DASHBOARD, "Dashboard"),
        (CATEGORY_POSTER, "Poster"),
        (CATEGORY_FEATURE, "Feature"),
        (CATEGORY_WORKFLOW, "Workflow"),
        (CATEGORY_OTHER, "Other"),
    )

    SOURCE_UPLOAD = "upload"
    SOURCE_EXTERNAL = "external"
    VIDEO_SOURCE_CHOICES = (
        (SOURCE_UPLOAD, "Uploaded file"),
        (SOURCE_EXTERNAL, "External URL"),
    )

    product = models.ForeignKey(Product, on_delete=models.CASCADE, related_name="media_items")
    title = models.CharField(max_length=160, null=True, blank=True)
    caption = models.TextField(null=True, blank=True)
    media_type = models.CharField(max_length=32, choices=MEDIA_TYPE_CHOICES, default=TYPE_IMAGE)
    image_category = models.CharField(
        max_length=32,
        choices=IMAGE_CATEGORY_CHOICES,
        null=True,
        blank=True,
        help_text="Optional label for image media (screenshot, dashboard, mobile, …).",
    )
    image = models.ImageField(
        upload_to="products/media/images/",
        null=True,
        blank=True,
        validators=[validate_product_poster],
    )
    video_source = models.CharField(
        max_length=16,
        choices=VIDEO_SOURCE_CHOICES,
        null=True,
        blank=True,
        help_text="Required when media type is video.",
    )
    video_file = models.FileField(
        upload_to="products/media/videos/",
        null=True,
        blank=True,
        validators=[validate_product_video],
    )
    video_url = models.URLField(
        max_length=500,
        null=True,
        blank=True,
        help_text="HTTPS URL when video source is external (YouTube/Vimeo preferred).",
    )
    thumbnail = models.ImageField(
        upload_to="products/media/thumbnails/",
        null=True,
        blank=True,
        validators=[validate_product_poster],
        help_text="Optional poster frame shown before video playback.",
    )
    alt_text = models.CharField(max_length=255, null=True, blank=True)
    is_featured = models.BooleanField(
        default=False,
        help_text="At most one featured media item per product (used as the primary gallery viewer).",
    )
    mime_type = models.CharField(max_length=100, null=True, blank=True)
    file_size = models.PositiveIntegerField(
        null=True,
        blank=True,
        help_text="Bytes for the primary uploaded file (image or video).",
    )
    duration_seconds = models.PositiveIntegerField(
        null=True,
        blank=True,
        help_text="Optional; not auto-extracted in this phase.",
    )

    objects = ProductMediaQuerySet.as_manager()

    class Meta(OrderedProductItem.Meta):
        abstract = False
        db_table = "product_media"
        verbose_name = "product media"
        verbose_name_plural = "product media"

    def __str__(self):
        return self.title or self.alt_text or f"Media {self.pk}"

    def clean(self):
        super().clean()
        errors = {}
        media_type = self.media_type or self.TYPE_IMAGE

        if media_type == self.TYPE_IMAGE:
            if not self.image:
                errors["image"] = "Image media requires an image file."
            if self.video_file:
                errors["video_file"] = "Image media cannot include a video file."
            if self.video_url:
                errors["video_url"] = "Image media cannot include a video URL."
            if self.video_source:
                errors["video_source"] = "Video source applies only to video media."
        elif media_type == self.TYPE_VIDEO:
            source = self.video_source or ""
            if source not in (self.SOURCE_UPLOAD, self.SOURCE_EXTERNAL):
                errors["video_source"] = "Choose upload or external for video media."
            if source == self.SOURCE_UPLOAD:
                if not self.video_file:
                    errors["video_file"] = "Uploaded video requires a video file."
                if self.video_url:
                    errors["video_url"] = "Uploaded video should not set an external URL."
            if source == self.SOURCE_EXTERNAL:
                if not (self.video_url or "").strip():
                    errors["video_url"] = "External video requires a video URL."
                if self.video_file:
                    errors["video_file"] = "External video should not include an uploaded file."
            if self.image:
                errors["image"] = "Video media should use thumbnail, not the image field."
        else:
            errors["media_type"] = "Media type must be image or video."

        if errors:
            raise ValidationError(errors)

    def save(self, *args, **kwargs):
        # Capture metadata from the primary binary when present.
        primary = None
        if self.media_type == self.TYPE_IMAGE and self.image:
            primary = self.image
        elif self.media_type == self.TYPE_VIDEO and self.video_source == self.SOURCE_UPLOAD and self.video_file:
            primary = self.video_file
        if primary is not None:
            content_type = getattr(primary, "content_type", None)
            if not content_type and hasattr(primary, "file"):
                content_type = getattr(primary.file, "content_type", None)
            if content_type:
                self.mime_type = str(content_type)[:100]
            elif not self.mime_type:
                name = (getattr(primary, "name", "") or "").lower()
                if name.endswith(".mp4"):
                    self.mime_type = "video/mp4"
                elif name.endswith(".webm"):
                    self.mime_type = "video/webm"
                elif name.endswith((".jpg", ".jpeg")):
                    self.mime_type = "image/jpeg"
                elif name.endswith(".png"):
                    self.mime_type = "image/png"
                elif name.endswith(".webp"):
                    self.mime_type = "image/webp"
                elif name.endswith(".gif"):
                    self.mime_type = "image/gif"
            size = getattr(primary, "size", None)
            if size is None and hasattr(primary, "file"):
                size = getattr(primary.file, "size", None)
            if size is not None:
                self.file_size = size

        super().save(*args, **kwargs)

        if self.is_featured and self.product_id:
            (
                ProductMedia.objects.filter(product_id=self.product_id, is_featured=True)
                .exclude(pk=self.pk)
                .update(is_featured=False)
            )


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
