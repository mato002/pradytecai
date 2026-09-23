from django.db import models


class BlogPost(models.Model):
    id = models.BigAutoField(primary_key=True)
    title = models.CharField(max_length=255)
    slug = models.CharField(max_length=255, unique=True)
    category = models.CharField(max_length=255, null=True, blank=True)
    excerpt = models.TextField(null=True, blank=True)
    body = models.TextField()
    is_published = models.BooleanField(default=True)
    published_at = models.DateTimeField(null=True, blank=True)
    created_at = models.DateTimeField(null=True, blank=True, auto_now_add=True)
    updated_at = models.DateTimeField(null=True, blank=True, auto_now=True)

    class Meta:
        db_table = "blog_posts"
        ordering = ["-published_at", "-created_at"]

    def __str__(self):
        return self.title


class SiteSetting(models.Model):
    id = models.BigAutoField(primary_key=True)
    key = models.CharField(max_length=255, unique=True)
    value = models.TextField(null=True, blank=True)

    class Meta:
        db_table = "site_settings"

    @classmethod
    def get(cls, key: str, default=None):
        row = cls.objects.filter(key=key).first()
        return row.value if row else default

    @classmethod
    def set(cls, key: str, value: str):
        obj, _ = cls.objects.update_or_create(key=key, defaults={"value": value})
        return obj


class ContactChannel(models.Model):
    """Admin-managed public contact methods (email, phone, WhatsApp, social, location, hours)."""

    TYPE_EMAIL = "email"
    TYPE_PHONE = "phone"
    TYPE_WHATSAPP = "whatsapp"
    TYPE_FACEBOOK = "facebook"
    TYPE_LINKEDIN = "linkedin"
    TYPE_TWITTER = "twitter"
    TYPE_INSTAGRAM = "instagram"
    TYPE_YOUTUBE = "youtube"
    TYPE_TELEGRAM = "telegram"
    TYPE_LOCATION = "location"
    TYPE_HOURS = "hours"
    TYPE_OTHER = "other"

    CHANNEL_TYPE_CHOICES = (
        (TYPE_EMAIL, "Email"),
        (TYPE_PHONE, "Phone"),
        (TYPE_WHATSAPP, "WhatsApp"),
        (TYPE_FACEBOOK, "Facebook"),
        (TYPE_LINKEDIN, "LinkedIn"),
        (TYPE_TWITTER, "X / Twitter"),
        (TYPE_INSTAGRAM, "Instagram"),
        (TYPE_YOUTUBE, "YouTube"),
        (TYPE_TELEGRAM, "Telegram"),
        (TYPE_LOCATION, "Location"),
        (TYPE_HOURS, "Business hours"),
        (TYPE_OTHER, "Other / custom link"),
    )

    id = models.BigAutoField(primary_key=True)
    channel_type = models.CharField(max_length=32, choices=CHANNEL_TYPE_CHOICES)
    label = models.CharField(
        max_length=120,
        help_text="Short label shown on the contact page, e.g. Sales, Support, LinkedIn.",
    )
    value = models.CharField(
        max_length=500,
        help_text="Display value: email, phone number, handle, address, or URL text.",
    )
    href = models.CharField(
        max_length=500,
        null=True,
        blank=True,
        help_text="Optional link override. Leave blank to auto-build tel:/mailto:/wa.me/ from the value.",
    )
    description = models.CharField(
        max_length=255,
        null=True,
        blank=True,
        help_text="Optional supporting line under the value.",
    )
    is_active = models.BooleanField(default=True)
    is_primary = models.BooleanField(
        default=False,
        help_text="Highlight as a primary way to reach the team.",
    )
    display_order = models.PositiveIntegerField(default=0)
    created_at = models.DateTimeField(auto_now_add=True)
    updated_at = models.DateTimeField(auto_now=True)

    class Meta:
        db_table = "contact_channels"
        ordering = ["display_order", "id"]
        verbose_name = "contact channel"
        verbose_name_plural = "contact channels"

    def __str__(self):
        return f"{self.get_channel_type_display()}: {self.label}"

    @staticmethod
    def _digits(value: str) -> str:
        return "".join(ch for ch in (value or "") if ch.isdigit() or ch == "+")

    def build_href(self):
        explicit = (self.href or "").strip()
        if explicit:
            return explicit
        raw = (self.value or "").strip()
        if not raw:
            return None
        kind = self.channel_type
        if kind == self.TYPE_EMAIL:
            return f"mailto:{raw}"
        if kind == self.TYPE_PHONE:
            digits = self._digits(raw)
            return f"tel:{digits}" if digits else None
        if kind == self.TYPE_WHATSAPP:
            digits = self._digits(raw).lstrip("+")
            return f"https://wa.me/{digits}" if digits else None
        if kind == self.TYPE_TELEGRAM:
            handle = raw.lstrip("@")
            if raw.startswith("http"):
                return raw
            return f"https://t.me/{handle}"
        if kind in (
            self.TYPE_FACEBOOK,
            self.TYPE_LINKEDIN,
            self.TYPE_TWITTER,
            self.TYPE_INSTAGRAM,
            self.TYPE_YOUTUBE,
            self.TYPE_OTHER,
        ):
            if raw.startswith("http://") or raw.startswith("https://"):
                return raw
            return f"https://{raw}"
        return None


class NewsletterSubscriber(models.Model):
    id = models.BigAutoField(primary_key=True)
    email = models.CharField(max_length=255, unique=True)
    status = models.CharField(max_length=255, default="subscribed")
    product_interest = models.CharField(max_length=255, null=True, blank=True)
    provider_id = models.CharField(max_length=255, null=True, blank=True)
    subscribed_at = models.DateTimeField(null=True, blank=True)
    unsubscribed_at = models.DateTimeField(null=True, blank=True)
    created_at = models.DateTimeField(null=True, blank=True, auto_now_add=True)
    updated_at = models.DateTimeField(null=True, blank=True, auto_now=True)

    class Meta:
        db_table = "newsletter_subscribers"
