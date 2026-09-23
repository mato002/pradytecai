"""Default product-page content for products whose scope is already known.

Only fills a collection when that product has no rows in it.
Never overwrites an existing title, description, or admin-edited child row.
Does not invent statistics, certifications, or integrations that are not
already part of the product description.
"""

from pathlib import Path

from django.conf import settings
from django.core.files import File

from apps.products.models import (
    Product,
    ProductAudience,
    ProductCapability,
    ProductCapabilityGroup,
    ProductControl,
    ProductCustomSection,
    ProductFAQ,
    ProductHighlight,
    ProductImplementationStep,
    ProductIntegration,
    ProductMedia,
    ProductOutcome,
    ProductPageSection,
    ProductProblem,
    ProductWorkflowStep,
)

SCALAR_FIELDS = (
    "tagline",
    "secondary_cta_label",
    "secondary_cta_type",
    "secondary_cta_url",
    "seo_title",
    "seo_description",
)

FLAT_RELATIONS = (
    ("highlights", ProductHighlight),
    ("audiences", ProductAudience),
    ("problems", ProductProblem),
    ("workflow_steps", ProductWorkflowStep),
    ("integrations", ProductIntegration),
    ("controls", ProductControl),
    ("outcomes", ProductOutcome),
    ("implementation_steps", ProductImplementationStep),
    ("faqs", ProductFAQ),
    ("custom_sections", ProductCustomSection),
)


def _rows(*items, step=10):
    prepared = []
    for index, item in enumerate(items):
        row = dict(item)
        row.setdefault("display_order", (index + 1) * step)
        row.setdefault("is_active", True)
        prepared.append(row)
    return prepared


MICROFINANCE = {
    "tagline": "Run lending, collections, payments and operations from one connected platform.",
    "secondary_cta_label": "Talk to Our Team",
    "secondary_cta_type": Product.CTA_CONTACT,
    "seo_title": "Prady Microfinance | Prady Technologies",
    "seo_description": (
        "Operating platform for microfinance institutions and lenders covering loans, "
        "collections, payments and institutional operations."
    ),
    "highlights": _rows(
        {"title": "Loan Management", "icon": "finance"},
        {"title": "Collections", "icon": "clock"},
        {"title": "Payments & Treasury", "icon": "wallet"},
        {"title": "Portfolio Intelligence", "icon": "shield"},
    ),
    "audiences": _rows(
        {"title": "Microfinance Institutions", "description": "Institutions that book, service and collect loans."},
        {"title": "Credit Companies", "description": "Lenders that need one record for the loan and its payments."},
        {"title": "Digital Lenders", "description": "Teams originating and servicing loans with less manual tracking."},
    ),
    "problems": _rows(
        {
            "title": "Manual loan tracking",
            "description": "Loan servicing and portfolio data sit in separate books and spreadsheets.",
        },
        {
            "title": "Difficult collections",
            "description": "Repayment follow-up is hard to keep with the loan it belongs to.",
        },
        {
            "title": "Payment reconciliation",
            "description": "Payments, including M-Pesa, need to be recorded against the right loan.",
        },
        {
            "title": "Limited management visibility",
            "description": "Managers lack one operating view of the portfolio.",
        },
    ),
    "capability_groups": [
        {
            "title": "Lending",
            "icon": "finance",
            "display_order": 10,
            "capabilities": [
                "Client onboarding",
                "Loan applications",
                "Appraisal",
                "Approval workflows",
                "Loan booking",
                "Repayment schedules",
                "Loan servicing",
            ],
        },
        {
            "title": "Collections",
            "icon": "clock",
            "display_order": 20,
            "capabilities": ["Repayments", "Arrears", "Promise to pay", "Field collections"],
        },
        {
            "title": "Payments",
            "icon": "wallet",
            "display_order": 30,
            "capabilities": ["Payment recording", "M-Pesa payments", "Reconciliation"],
        },
        {
            "title": "Operations",
            "icon": "cog",
            "display_order": 40,
            "capabilities": ["Accounting", "Reporting", "HR", "Institutional controls"],
        },
    ],
    "workflow_steps": _rows(
        {"title": "Application", "step_number": 1},
        {"title": "Appraisal", "step_number": 2},
        {"title": "Approval", "step_number": 3},
        {"title": "Disbursement", "step_number": 4},
        {"title": "Repayment", "step_number": 5},
        {"title": "Collections", "step_number": 6},
        {"title": "Portfolio Monitoring", "step_number": 7},
    ),
    "integrations": _rows(
        {
            "name": "M-Pesa",
            "description": "Record M-Pesa payments against loans as part of payment operations.",
        }
    ),
    "controls": _rows(
        {"title": "Role-based access", "description": "Staff work within the functions their role allows.", "icon": "users"},
        {"title": "Approval workflows", "description": "Loan decisions can follow a configured approval path.", "icon": "check"},
        {"title": "Portfolio visibility", "description": "Managers can review portfolio activity in one place.", "icon": "finance"},
        {"title": "Audit trail", "description": "Important account activity can be traced.", "icon": "shield"},
    ),
    "outcomes": _rows(
        {"title": "Faster loan processing", "description": "Applications move through appraisal, approval and booking on one record."},
        {"title": "Better collections oversight", "description": "Arrears and follow-up stay attached to the loan."},
        {"title": "Reduced manual reconciliation", "description": "Payments are recorded against the loans they settle."},
        {"title": "Improved management visibility", "description": "Portfolio operations are visible without a separate report chase."},
    ),
    "implementation_steps": _rows(
        {"title": "Understand your operation", "step_number": 1, "description": "Map how loans, collections and payments work today."},
        {"title": "Configure the platform", "step_number": 2, "description": "Set products, roles and approval paths for your institution."},
        {"title": "Migrate your data", "step_number": 3, "description": "Bring across the client and loan records you need to go live."},
        {"title": "Train your team", "step_number": 4, "description": "Walk lending, collections and finance staff through their work."},
        {"title": "Go live", "step_number": 5, "description": "Start operating from the platform with support on hand."},
    ),
    "faqs": _rows(
        {
            "question": "Who is Prady Microfinance for?",
            "answer": "Microfinance institutions, credit companies and lenders that need one platform for loans, collections, payments and operations.",
        },
        {
            "question": "Does it record M-Pesa payments?",
            "answer": "Yes. M-Pesa is part of payment recording so collections can be matched to loans.",
        },
        {
            "question": "How do we request a demo?",
            "answer": "Use Request demo on this page. The team follows up about your operation. Nothing is booked automatically.",
        },
    ),
    "page_sections": [
        ("highlights", 10, "", ""),
        ("audience", 20, "Who it is for", ""),
        ("problems", 30, "Problems solved", ""),
        ("capabilities", 40, "Core capabilities", ""),
        ("workflow", 50, "How it works", ""),
        ("media", 55, "See the product", "Screenshots and walkthroughs from the live platform."),
        ("integrations", 60, "Integrations", "Only integrations configured for this product are listed."),
        ("controls", 70, "Security and controls", ""),
        ("outcomes", 80, "Outcomes", ""),
        ("implementation", 90, "Getting started", ""),
        ("faq", 100, "FAQ", ""),
    ],
}

GPS = {
    "tagline": "Host and monitor compatible vehicle trackers for fleets and GPS resellers.",
    "secondary_cta_label": "Talk to Our Team",
    "secondary_cta_type": Product.CTA_CONTACT,
    "seo_title": "GPS Hosting & Tracking | Prady Technologies",
    "seo_description": "GPS tracking and hosting for vehicle trackers, fleets and GPS resellers.",
    "highlights": _rows(
        {"title": "Vehicle tracking", "icon": "location"},
        {"title": "Fleet visibility", "icon": "car"},
        {"title": "Tracker hosting", "icon": "cloud"},
        {"title": "Reseller support", "icon": "users"},
    ),
    "audiences": _rows(
        {"title": "Vehicle owners"},
        {"title": "Fleet operators"},
        {"title": "Logistics businesses"},
        {"title": "GPS resellers"},
    ),
    "capability_groups": [
        {
            "title": "Tracking",
            "icon": "location",
            "display_order": 10,
            "capabilities": ["Live location", "Trip history", "Vehicle status"],
        },
        {
            "title": "Fleet operations",
            "icon": "car",
            "display_order": 20,
            "capabilities": ["Vehicle records", "Fleet overview"],
        },
        {
            "title": "Device hosting",
            "icon": "cloud",
            "display_order": 30,
            "capabilities": ["Compatible trackers", "Prady-branded devices"],
        },
    ],
    "workflow_steps": _rows(
        {"title": "Register devices", "step_number": 1, "description": "Add the compatible trackers you host."},
        {"title": "Assign vehicles", "step_number": 2, "description": "Connect each device to the vehicle it rides on."},
        {"title": "Monitor location", "step_number": 3, "description": "Follow live position and recent movement."},
        {"title": "Review activity", "step_number": 4, "description": "Look back at trips and vehicle status."},
    ),
    "implementation_steps": _rows(
        {"title": "Confirm your devices", "step_number": 1, "description": "Check that the trackers you use are compatible."},
        {"title": "Configure hosting", "step_number": 2, "description": "Set up the account for your fleet or reseller operation."},
        {"title": "Train operators", "step_number": 3, "description": "Show the people who watch vehicles how to use the platform."},
        {"title": "Go live", "step_number": 4, "description": "Start monitoring from the hosted platform."},
    ),
    "page_sections": [
        ("highlights", 10, "", ""),
        ("audience", 20, "Who it is for", ""),
        ("capabilities", 30, "Tracking capabilities", ""),
        ("workflow", 40, "How tracking runs", ""),
        ("implementation", 50, "Getting started", ""),
    ],
}

PROPERTY = {
    "tagline": "Properties, tenants, rent and maintenance in one place.",
    "seo_title": "Property Management | Prady Technologies",
    "seo_description": "Manage properties, tenants, leases, rent, arrears and maintenance in one place.",
    "highlights": _rows(
        {"title": "Properties and units", "icon": "building"},
        {"title": "Tenants and leases", "icon": "users"},
        {"title": "Rent and arrears", "icon": "wallet"},
        {"title": "Maintenance", "icon": "cog"},
    ),
    "audiences": _rows(
        {"title": "Landlords"},
        {"title": "Property managers"},
        {"title": "Real-estate businesses"},
    ),
    "capability_groups": [
        {
            "title": "Properties",
            "icon": "building",
            "display_order": 10,
            "capabilities": ["Property records", "Units"],
        },
        {
            "title": "Tenants",
            "icon": "users",
            "display_order": 20,
            "capabilities": ["Tenant records", "Leases"],
        },
        {
            "title": "Rent collection",
            "icon": "wallet",
            "display_order": 30,
            "capabilities": ["Rent charges", "Payments", "Arrears"],
        },
        {
            "title": "Maintenance",
            "icon": "cog",
            "display_order": 40,
            "capabilities": ["Maintenance requests", "Property performance"],
        },
    ],
    "outcomes": _rows(
        {"title": "A clearer rent position", "description": "Charges and payments stay with the lease."},
        {"title": "Arrears you can see", "description": "Outstanding rent is visible with the tenancy, not in a side list."},
        {"title": "Maintenance follow-up", "description": "Requests stay with the property they belong to."},
    ),
    "custom_sections": _rows(
        {
            "title": "Maintenance stays with the tenancy",
            "subtitle": "One record for the property, the tenant and the work.",
            "body": (
                "Maintenance requests sit with the property and tenant record, so follow-up "
                "does not live in a separate spreadsheet from the lease and the rent."
            ),
            "layout_type": ProductCustomSection.LAYOUT_CALLOUT,
        }
    ),
    "faqs": _rows(
        {
            "question": "What does the property platform cover?",
            "answer": "Properties, units, tenants, leases, rent, payments, arrears and maintenance.",
        },
        {
            "question": "Is this the same product as Prady Microfinance?",
            "answer": "No. Property Management is for landlords and property managers. Lending is a separate product.",
        },
    ),
    "page_sections": [
        ("audience", 10, "Who it is for", ""),
        ("capabilities", 20, "What you can run", ""),
        ("highlights", 30, "", ""),
        ("outcomes", 40, "What changes operationally", ""),
        ("custom", 50, "", ""),
        ("faq", 60, "FAQ", ""),
    ],
}

DETAIL_BY_SLUG = {
    "prady-microfinance": MICROFINANCE,
    "gps-hosting": GPS,
    "property-management": PROPERTY,
}


def _fill_blank_scalars(product, spec):
    changed = []
    for field in SCALAR_FIELDS:
        value = spec.get(field)
        if value and not getattr(product, field):
            setattr(product, field, value)
            changed.append(field)
    if changed:
        product.save(update_fields=changed + ["updated_at"])
    return bool(changed)


def _seed_flat(product, related_name, model, rows):
    if not rows or getattr(product, related_name).exists():
        return False
    for row in rows:
        model.objects.create(product=product, **row)
    return True


def _seed_groups(product, groups):
    if not groups or product.capability_groups.exists():
        return False
    for group in groups:
        created = ProductCapabilityGroup.objects.create(
            product=product,
            title=group["title"],
            description=group.get("description") or "",
            icon=group.get("icon") or "",
            display_order=group.get("display_order") or 0,
            is_active=True,
        )
        for index, title in enumerate(group.get("capabilities") or []):
            ProductCapability.objects.create(
                capability_group=created,
                title=title,
                display_order=(index + 1) * 10,
                is_active=True,
            )
    return True


def _seed_sections(product, sections):
    if not sections or product.page_sections.exists():
        return False
    for section_type, order, title, subtitle in sections:
        ProductPageSection.objects.create(
            product=product,
            section_type=section_type,
            title_override=title or "",
            subtitle=subtitle or "",
            display_order=order,
            is_enabled=True,
        )
    return True


def _attach_known_poster(product):
    """Attach the existing Microfinance poster only when the product has none."""
    if product.slug != "prady-microfinance" or product.poster:
        return False
    path = Path(settings.BASE_DIR) / "react" / "public" / "images" / "mfi.jpg"
    if not path.is_file():
        return False
    with path.open("rb") as handle:
        product.poster.save("mfi.jpg", File(handle), save=True)
    return True


def _ensure_media_section(product):
    """Add a media page section when absent (does not reorder existing sections)."""
    if product.page_sections.filter(section_type=ProductPageSection.MEDIA).exists():
        return False
    max_order = (
        product.page_sections.order_by("-display_order")
        .values_list("display_order", flat=True)
        .first()
    )
    order = 55 if max_order is None else max_order + 5
    ProductPageSection.objects.create(
        product=product,
        section_type=ProductPageSection.MEDIA,
        title_override="See the product",
        subtitle="Screenshots and walkthroughs from the live platform.",
        display_order=order,
        is_enabled=True,
    )
    return True


def _attach_gallery_media(product):
    """Seed one gallery image when the product has no media rows."""
    if product.slug != "prady-microfinance" or product.media_items.exists():
        return False
    path = Path(settings.BASE_DIR) / "react" / "public" / "images" / "mfi.jpg"
    if not path.is_file() and not product.poster:
        return False
    item = ProductMedia(
        product=product,
        media_type=ProductMedia.TYPE_IMAGE,
        image_category=ProductMedia.CATEGORY_DASHBOARD,
        title="Operations dashboard",
        caption="Desktop and mobile views of the Prady Microfinance operations dashboard.",
        alt_text="Prady Microfinance dashboard on laptop and phone",
        is_featured=True,
        display_order=10,
        is_active=True,
    )
    if product.poster:
        product.poster.open("rb")
        try:
            item.image.save("mfi-gallery.jpg", File(product.poster.file), save=False)
        finally:
            product.poster.close()
    else:
        with path.open("rb") as handle:
            item.image.save("mfi-gallery.jpg", File(handle), save=False)
    item.save()
    return True


def seed_product_page_content():
    """Create missing detail content for known products. Returns a small stats dict."""
    filled = 0
    skipped = 0
    for slug, spec in DETAIL_BY_SLUG.items():
        product = Product.objects.filter(slug=slug).first()
        if not product:
            skipped += 1
            continue
        touched = _fill_blank_scalars(product, spec)
        for related_name, model in FLAT_RELATIONS:
            if _seed_flat(product, related_name, model, spec.get(related_name)):
                touched = True
        if _seed_groups(product, spec.get("capability_groups")):
            touched = True
        if _seed_sections(product, spec.get("page_sections")):
            touched = True
        if _ensure_media_section(product):
            touched = True
        if _attach_known_poster(product):
            touched = True
        if _attach_gallery_media(product):
            touched = True
        if touched:
            filled += 1
        else:
            skipped += 1
    return {"filled": filled, "unchanged": skipped, "catalog": len(DETAIL_BY_SLUG)}
