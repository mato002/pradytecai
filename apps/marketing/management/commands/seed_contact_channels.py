from django.core.management.base import BaseCommand
from apps.marketing.models import ContactChannel


class Command(BaseCommand):
    help = "Seed initial contact channels and social media handles if none exist."

    def handle(self, *args, **options):
        initial = [
            {
                "channel_type": ContactChannel.TYPE_PHONE,
                "label": "Call Office",
                "value": "+254 722 295 194",
                "description": "Speak with our team during business hours.",
                "is_primary": True,
                "display_order": 10,
            },
            {
                "channel_type": ContactChannel.TYPE_WHATSAPP,
                "label": "WhatsApp Chat",
                "value": "+254 722 295 194",
                "description": "Quick inquiries, support, and demos.",
                "is_primary": True,
                "display_order": 20,
            },
            {
                "channel_type": ContactChannel.TYPE_EMAIL,
                "label": "Contact Email",
                "value": "marketing@pradytecai.com",
                "description": "Send a detailed request anytime.",
                "is_primary": True,
                "display_order": 30,
            },
            {
                "channel_type": ContactChannel.TYPE_LINKEDIN,
                "label": "LinkedIn",
                "value": "https://www.linkedin.com/company/prady-technologies-ltd",
                "description": "Follow Prady Technologies on LinkedIn.",
                "is_primary": False,
                "display_order": 40,
            },
            {
                "channel_type": ContactChannel.TYPE_TWITTER,
                "label": "X / Twitter",
                "value": "https://x.com/pradytecai",
                "description": "Follow updates and tech announcements.",
                "is_primary": False,
                "display_order": 50,
            },
            {
                "channel_type": ContactChannel.TYPE_LOCATION,
                "label": "Headquarters",
                "value": "Nairobi, Kenya",
                "description": "Prady Technologies Ltd",
                "is_primary": False,
                "display_order": 60,
            },
            {
                "channel_type": ContactChannel.TYPE_HOURS,
                "label": "Business Hours",
                "value": "Mon – Fri: 8:00 AM – 6:00 PM EAT",
                "description": "Customer support & engineering schedule.",
                "is_primary": False,
                "display_order": 70,
            },
        ]

        created_count = 0
        for item in initial:
            obj, created = ContactChannel.objects.get_or_create(
                channel_type=item["channel_type"],
                value=item["value"],
                defaults=item,
            )
            if created:
                created_count += 1
                self.stdout.write(self.style.SUCCESS(f"Created: {obj}"))
            else:
                self.stdout.write(f"Exists: {obj}")

        self.stdout.write(self.style.SUCCESS(f"Done. {created_count} channels created."))
