# Generated manually for ProductMedia image/video support

import apps.products.models
from django.db import migrations, models


LEGACY_TYPES = {
    "screenshot",
    "mobile",
    "dashboard",
    "poster",
    "feature",
    "workflow",
    "other",
}


def forwards_media_types(apps, schema_editor):
    ProductMedia = apps.get_model("products", "ProductMedia")
    for row in ProductMedia.objects.all().iterator():
        legacy = (row.media_type or "").strip()
        if legacy in LEGACY_TYPES:
            row.image_category = legacy
            row.media_type = "image"
            row.save(update_fields=["image_category", "media_type"])
        elif legacy not in ("image", "video"):
            row.media_type = "image"
            row.save(update_fields=["media_type"])


def backwards_media_types(apps, schema_editor):
    ProductMedia = apps.get_model("products", "ProductMedia")
    for row in ProductMedia.objects.all().iterator():
        if row.media_type == "image" and row.image_category:
            row.media_type = row.image_category
            row.save(update_fields=["media_type"])
        elif row.media_type == "video":
            row.media_type = "other"
            row.save(update_fields=["media_type"])


class Migration(migrations.Migration):

    dependencies = [
        ("products", "0003_product_detail_content"),
    ]

    operations = [
        migrations.AddField(
            model_name="productmedia",
            name="image_category",
            field=models.CharField(
                blank=True,
                choices=[
                    ("screenshot", "Screenshot"),
                    ("mobile", "Mobile"),
                    ("dashboard", "Dashboard"),
                    ("poster", "Poster"),
                    ("feature", "Feature"),
                    ("workflow", "Workflow"),
                    ("other", "Other"),
                ],
                help_text="Optional label for image media (screenshot, dashboard, mobile, …).",
                max_length=32,
                null=True,
            ),
        ),
        migrations.AddField(
            model_name="productmedia",
            name="video_source",
            field=models.CharField(
                blank=True,
                choices=[("upload", "Uploaded file"), ("external", "External URL")],
                help_text="Required when media type is video.",
                max_length=16,
                null=True,
            ),
        ),
        migrations.AddField(
            model_name="productmedia",
            name="video_file",
            field=models.FileField(
                blank=True,
                null=True,
                upload_to="products/media/videos/",
                validators=[apps.products.models.validate_product_video],
            ),
        ),
        migrations.AddField(
            model_name="productmedia",
            name="video_url",
            field=models.URLField(
                blank=True,
                help_text="HTTPS URL when video source is external (YouTube/Vimeo preferred).",
                max_length=500,
                null=True,
            ),
        ),
        migrations.AddField(
            model_name="productmedia",
            name="thumbnail",
            field=models.ImageField(
                blank=True,
                help_text="Optional poster frame shown before video playback.",
                null=True,
                upload_to="products/media/thumbnails/",
                validators=[apps.products.models.validate_product_poster],
            ),
        ),
        migrations.AddField(
            model_name="productmedia",
            name="is_featured",
            field=models.BooleanField(
                default=False,
                help_text="At most one featured media item per product (used as the primary gallery viewer).",
            ),
        ),
        migrations.AddField(
            model_name="productmedia",
            name="mime_type",
            field=models.CharField(blank=True, max_length=100, null=True),
        ),
        migrations.AddField(
            model_name="productmedia",
            name="file_size",
            field=models.PositiveIntegerField(
                blank=True,
                help_text="Bytes for the primary uploaded file (image or video).",
                null=True,
            ),
        ),
        migrations.AddField(
            model_name="productmedia",
            name="duration_seconds",
            field=models.PositiveIntegerField(
                blank=True,
                help_text="Optional; not auto-extracted in this phase.",
                null=True,
            ),
        ),
        migrations.AlterField(
            model_name="productmedia",
            name="image",
            field=models.ImageField(
                blank=True,
                null=True,
                upload_to="products/media/images/",
                validators=[apps.products.models.validate_product_poster],
            ),
        ),
        migrations.RunPython(forwards_media_types, backwards_media_types),
        migrations.AlterField(
            model_name="productmedia",
            name="media_type",
            field=models.CharField(
                choices=[("image", "Image"), ("video", "Video")],
                default="image",
                max_length=32,
            ),
        ),
    ]
