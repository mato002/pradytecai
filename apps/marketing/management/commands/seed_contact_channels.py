"""Idempotent defaults for public contact channels."""

from django.core.management.base import BaseCommand

from apps.marketing.models import ContactChannel

DEFAULT_CHANNELS = [
    {
        "channel_type": ContactChannel.TYPE_PHONE,
        "label": "Call us",
        "value": "+254 722 295 194",
        "description": "Speak with the team during business hours.",
        "is_primary": True,
        "display_order": 10,
    },
    {
        "channel_type": ContactChannel.TYPE_WHATSAPP,
        "label": "WhatsApp",
        "value": "+254 722 295 194",
        "description": "Message us for a quick reply.",
        "is_primary": True,
        "display_order": 20,
    },
    {
        "channel_type": ContactChannel.TYPE_EMAIL,
        "label": "Email",
        "value": "marketing@pradytecai.com",
        "description": "Send a detailed request anytime.",
        "is_primary": True,
        "display_order": 30,
    },
    {
        "channel_type": ContactChannel.TYPE_LOCATION,
        "label": "Location",
        "value": "Nairobi, Kenya",
        "display_order": 40,
    },
    {
        "channel_type": ContactChannel.TYPE_HOURS,
        "label": "Business hours",
        "value": "Mon – Fri: 8:00 AM – 6:00 PM EAT",
        "display_order": 50,
    },
]


def seed_contact_channels():
    """Create missing defaults by (channel_type, label). Never overwrite admin edits."""
    created = 0
    skipped = 0
    for spec in DEFAULT_CHANNELS:
        exists = ContactChannel.objects.filter(
            channel_type=spec["channel_type"],
            label=spec["label"],
        ).exists()
        if exists:
            skipped += 1
            continue
        ContactChannel.objects.create(**spec, is_active=True)
        created += 1
    return created, skipped


class Command(BaseCommand):
    help = "Seed default public contact channels (idempotent)."

    def handle(self, *args, **options):
        created, skipped = seed_contact_channels()
        self.stdout.write(
            self.style.SUCCESS(f"Contact channels: created={created} skipped={skipped}")
        )
