from django.db import migrations, models
import apps.products.models


class Migration(migrations.Migration):

    dependencies = [
        ("products", "0001_initial"),
    ]

    operations = [
        migrations.AlterField(
            model_name="product",
            name="slug",
            field=models.SlugField(blank=True, max_length=255, null=True, unique=True),
        ),
        migrations.AlterField(
            model_name="product",
            name="short",
            field=models.CharField(
                blank=True,
                help_text="Short description shown on product cards.",
                max_length=500,
                null=True,
            ),
        ),
        migrations.AlterField(
            model_name="product",
            name="url",
            field=models.CharField(
                blank=True,
                help_text="Optional CTA destination URL (external or internal path).",
                max_length=500,
                null=True,
            ),
        ),
        migrations.AlterField(
            model_name="product",
            name="order",
            field=models.IntegerField(default=0, help_text="Display order (lower first)."),
        ),
        migrations.AddField(
            model_name="product",
            name="poster",
            field=models.ImageField(
                blank=True,
                null=True,
                upload_to="products/posters/",
                validators=[apps.products.models.validate_product_poster],
            ),
        ),
        migrations.AddField(
            model_name="product",
            name="icon",
            field=models.CharField(
                blank=True,
                help_text="Optional icon key for marketing UI (e.g. finance, users).",
                max_length=64,
                null=True,
            ),
        ),
        migrations.AddField(
            model_name="product",
            name="cta_label",
            field=models.CharField(blank=True, default="Request demo", max_length=100, null=True),
        ),
        migrations.AddField(
            model_name="product",
            name="cta_type",
            field=models.CharField(
                blank=True,
                choices=[
                    ("contact", "Contact form"),
                    ("demo", "Request demo"),
                    ("external", "External URL"),
                ],
                default="demo",
                max_length=32,
            ),
        ),
        migrations.AddField(
            model_name="product",
            name="is_featured",
            field=models.BooleanField(default=False),
        ),
        migrations.AddField(
            model_name="product",
            name="group_key",
            field=models.CharField(
                blank=True,
                help_text="Optional portfolio group key: finance, assets, commerce.",
                max_length=64,
                null=True,
            ),
        ),
    ]
