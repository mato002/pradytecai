from django.db import migrations, models


class Migration(migrations.Migration):

    dependencies = [
        ("marketing", "0001_initial"),
    ]

    operations = [
        migrations.CreateModel(
            name="ContactChannel",
            fields=[
                ("id", models.BigAutoField(primary_key=True, serialize=False)),
                (
                    "channel_type",
                    models.CharField(
                        choices=[
                            ("email", "Email"),
                            ("phone", "Phone"),
                            ("whatsapp", "WhatsApp"),
                            ("facebook", "Facebook"),
                            ("linkedin", "LinkedIn"),
                            ("twitter", "X / Twitter"),
                            ("instagram", "Instagram"),
                            ("youtube", "YouTube"),
                            ("telegram", "Telegram"),
                            ("location", "Location"),
                            ("hours", "Business hours"),
                            ("other", "Other / custom link"),
                        ],
                        max_length=32,
                    ),
                ),
                (
                    "label",
                    models.CharField(
                        help_text="Short label shown on the contact page, e.g. Sales, Support, LinkedIn.",
                        max_length=120,
                    ),
                ),
                (
                    "value",
                    models.CharField(
                        help_text="Display value: email, phone number, handle, address, or URL text.",
                        max_length=500,
                    ),
                ),
                (
                    "href",
                    models.CharField(
                        blank=True,
                        help_text="Optional link override. Leave blank to auto-build tel:/mailto:/wa.me/ from the value.",
                        max_length=500,
                        null=True,
                    ),
                ),
                (
                    "description",
                    models.CharField(
                        blank=True,
                        help_text="Optional supporting line under the value.",
                        max_length=255,
                        null=True,
                    ),
                ),
                ("is_active", models.BooleanField(default=True)),
                (
                    "is_primary",
                    models.BooleanField(
                        default=False,
                        help_text="Highlight as a primary way to reach the team.",
                    ),
                ),
                ("display_order", models.PositiveIntegerField(default=0)),
                ("created_at", models.DateTimeField(auto_now_add=True)),
                ("updated_at", models.DateTimeField(auto_now=True)),
            ],
            options={
                "verbose_name": "contact channel",
                "verbose_name_plural": "contact channels",
                "db_table": "contact_channels",
                "ordering": ["display_order", "id"],
            },
        ),
    ]
