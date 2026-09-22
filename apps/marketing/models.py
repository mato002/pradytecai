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
