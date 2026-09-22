"""Idempotent default product catalog seeding.

Safe to run multiple times: creates missing products by slug only.
Never overwrites admin-edited fields on existing rows.
"""

from django.core.management.base import BaseCommand

from apps.products.models import Product

# Stable portfolio defaults — slug is the idempotency key.
DEFAULT_PRODUCTS = [
    {
        "slug": "prady-microfinance",
        "name": "Prady Microfinance",
        "short": (
            "Complete operating platform for MFIs and lenders covering loans, "
            "collections, payments and institutional operations."
        ),
        "description": (
            "A complete operating platform for microfinance institutions and lenders, "
            "covering customers, loans, collections, payments, M-Pesa, accounting, "
            "reporting, HR, controls and institutional operations."
        ),
        "market": "MFIs, credit companies and lenders",
        "icon": "finance",
        "group_key": "finance",
        "order": 10,
        "is_featured": True,
        "cta_label": "Request demo",
        "cta_type": Product.CTA_DEMO,
    },
    {
        "slug": "rafiki-loan",
        "name": "Rafiki Loan",
        "short": "Digital lending marketplace connecting borrowers with institutions and capital providers.",
        "description": (
            "A digital lending marketplace connecting people seeking financing with "
            "institutions or people offering financing. Supports discovery, matching "
            "and the journey between borrower and finance provider."
        ),
        "market": "Borrowers, lenders and capital providers",
        "icon": "handshake",
        "group_key": "finance",
        "order": 20,
        "cta_label": "Request demo",
        "cta_type": Product.CTA_DEMO,
    },
    {
        "slug": "gps-hosting",
        "name": "GPS Hosting & Tracking Platform",
        "short": "GPS tracking and hosting for vehicle trackers, fleets and GPS resellers.",
        "description": (
            "A GPS tracking and hosting platform for compatible vehicle trackers, fleets "
            "and GPS resellers, including support for Prady-branded tracking devices."
        ),
        "market": "Vehicle owners, fleet operators, logistics businesses and GPS resellers",
        "icon": "location",
        "group_key": "assets",
        "order": 30,
        "cta_label": "Request demo",
        "cta_type": Product.CTA_DEMO,
    },
    {
        "slug": "property-management",
        "name": "Property Management System",
        "short": "Manage properties, tenants, leases, rent, arrears and maintenance in one place.",
        "description": (
            "A platform for managing properties, units, tenants, leases, rent, payments, "
            "arrears, maintenance and property performance."
        ),
        "market": "Landlords, property managers and real-estate businesses",
        "icon": "building",
        "group_key": "assets",
        "order": 40,
        "cta_label": "Request demo",
        "cta_type": Product.CTA_DEMO,
    },
    {
        "slug": "spareme",
        "name": "SpareMe",
        "short": "Vehicle-first automotive commerce matching spare parts to exact vehicles.",
        "description": (
            "A vehicle-first automotive commerce and intelligence ecosystem. Vehicle owners "
            "identify their exact vehicle while dealers manage inventory and compatible spare "
            "parts can be matched to vehicles."
        ),
        "market": "Vehicle owners, spare-parts dealers, mechanics, suppliers and garages",
        "icon": "car",
        "group_key": "assets",
        "order": 50,
        "cta_label": "Request demo",
        "cta_type": Product.CTA_DEMO,
    },
    {
        "slug": "live-commerce",
        "name": "Live Commerce / Social Selling Platform",
        "short": "Social-commerce platform for live shows, audiences and lasting product shelves.",
        "description": (
            "A social-commerce platform where sellers and creators can announce live shows, "
            "attract audiences and showcase products during and after live broadcasts."
        ),
        "market": "Social sellers, creators, SMEs and online merchants",
        "icon": "live",
        "group_key": "commerce",
        "order": 60,
        "cta_label": "Request demo",
        "cta_type": Product.CTA_DEMO,
    },
    {
        "slug": "sacco-system",
        "name": "SACCO System",
        "short": "Digital operating system for SACCOs covering membership, savings and loans.",
        "description": (
            "A digital operating system for SACCO and member-based financial institutions "
            "covering membership, savings, contributions, loans, accounts, governance and "
            "institutional administration."
        ),
        "market": "SACCOs and member-based financial institutions",
        "icon": "users",
        "group_key": "finance",
        "order": 70,
        "cta_label": "Request demo",
        "cta_type": Product.CTA_DEMO,
    },
    {
        "slug": "chama-system",
        "name": "Chama System",
        "short": "Simple digital platform for chamas, investment clubs and welfare groups.",
        "description": (
            "A simplified digital platform for investment groups and chamas covering members, "
            "contributions, welfare, loans, projects/investments, commitments, documents and "
            "transparent group accounts."
        ),
        "market": "Chamas, investment clubs and welfare groups",
        "icon": "group",
        "group_key": "finance",
        "order": 80,
        "cta_label": "Request demo",
        "cta_type": Product.CTA_DEMO,
    },
    {
        "slug": "mtalii-travel-wallet",
        "name": "Mtalii Travel Wallet",
        "short": "Tourism-focused wallet for payments, FX and local merchant acceptance.",
        "description": (
            "A tourism-focused wallet and travel platform designed around tourists and local "
            "merchants, including payments, FX, merchant acceptance and tourism-oriented "
            "financial services."
        ),
        "market": "Tourists, tour operators, guides and local merchants",
        "icon": "wallet",
        "group_key": "commerce",
        "order": 90,
        "cta_label": "Request demo",
        "cta_type": Product.CTA_DEMO,
    },
]


def seed_default_products():
    """Create missing default products. Returns (created_count, skipped_count)."""
    created = 0
    skipped = 0
    for row in DEFAULT_PRODUCTS:
        slug = row["slug"]
        if Product.objects.filter(slug=slug).exists():
            skipped += 1
            continue
        Product.objects.create(
            slug=slug,
            name=row["name"],
            short=row.get("short"),
            description=row.get("description"),
            market=row.get("market"),
            icon=row.get("icon"),
            group_key=row.get("group_key"),
            order=row.get("order", 0),
            is_featured=row.get("is_featured", False),
            is_active=True,
            cta_label=row.get("cta_label") or "Request demo",
            cta_type=row.get("cta_type") or Product.CTA_DEMO,
        )
        created += 1
    return created, skipped


class Command(BaseCommand):
    help = (
        "Idempotently seed the 9 default Pradytec products. "
        "Creates missing slugs only; never overwrites existing products."
    )

    def handle(self, *args, **options):
        created, skipped = seed_default_products()
        self.stdout.write(
            self.style.SUCCESS(
                f"Default products: created={created}, already_present={skipped}, "
                f"total_defaults={len(DEFAULT_PRODUCTS)}"
            )
        )
