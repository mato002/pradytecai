"""Product / social-account visibility — port of User.visibleTo / scopedProductIds."""

from __future__ import annotations

from typing import TYPE_CHECKING

from django.db.models import Q, QuerySet

if TYPE_CHECKING:
    from apps.accounts.models import User


def scoped_product_ids(user: User) -> list[int] | None:
    """Return product id list, or None meaning unrestricted."""
    if user.is_super_admin_user():
        return None
    ids = list(
        user.access_scopes.filter(scope_type="product").values_list("scope_id", flat=True)
    )
    return None if not ids else [int(i) for i in ids]


def scoped_social_account_ids(user: User) -> list[int] | None:
    if user.is_super_admin_user():
        return None
    ids = list(
        user.access_scopes.filter(scope_type="social_account").values_list(
            "scope_id", flat=True
        )
    )
    return None if not ids else [int(i) for i in ids]


def can_access_product(user: User, product_id: int | None) -> bool:
    if product_id is None:
        return True
    if user.is_super_admin_user():
        return True
    ids = scoped_product_ids(user)
    if ids is None:
        return True
    return int(product_id) in ids


def can_access_social_account(user: User, social_account_id: int | None) -> bool:
    if social_account_id is None:
        return True
    if user.is_super_admin_user():
        return True
    ids = scoped_social_account_ids(user)
    if ids is None:
        return True
    return int(social_account_id) in ids


def filter_products_visible(qs: QuerySet, user: User) -> QuerySet:
    ids = scoped_product_ids(user)
    if ids is None:
        return qs
    return qs.filter(pk__in=ids)


def filter_by_product_scope(
    qs: QuerySet, user: User, product_field: str = "product_id"
) -> QuerySet:
    """Restrict queryset by product scopes; null product_id rows remain visible."""
    ids = scoped_product_ids(user)
    if ids is None:
        return qs
    return qs.filter(Q(**{f"{product_field}__isnull": True}) | Q(**{f"{product_field}__in": ids}))


def filter_social_accounts_visible(qs: QuerySet, user: User) -> QuerySet:
    product_ids = scoped_product_ids(user)
    account_ids = scoped_social_account_ids(user)
    if product_ids is not None:
        qs = qs.filter(Q(product_id__isnull=True) | Q(product_id__in=product_ids))
    if account_ids is not None:
        qs = qs.filter(pk__in=account_ids)
    return qs
