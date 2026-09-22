from django.db import models


class Product(models.Model):
    id = models.BigAutoField(primary_key=True)
    name = models.CharField(max_length=255)
    slug = models.CharField(max_length=255, null=True, blank=True, unique=True)
    description = models.TextField(null=True, blank=True)
    short = models.CharField(max_length=255, null=True, blank=True)
    type = models.CharField(max_length=255, null=True, blank=True)
    market = models.CharField(max_length=255, null=True, blank=True)
    code = models.CharField(max_length=255, null=True, blank=True)
    url = models.CharField(max_length=255, null=True, blank=True)
    is_active = models.BooleanField(default=True)
    order = models.IntegerField(default=0)
    last_marketed_at = models.DateTimeField(null=True, blank=True)
    created_at = models.DateTimeField(null=True, blank=True, auto_now_add=True)
    updated_at = models.DateTimeField(null=True, blank=True, auto_now=True)

    class Meta:
        db_table = "products"
        ordering = ["order", "name"]

    def __str__(self):
        return self.name
