from django.views.decorators.http import require_http_methods

from apps.careers.api.views import public_apply
from apps.core.spa import spa_index
from apps.leads.api.views import public_contact
from apps.marketing.api.views import public_newsletter


def contact_dispatch(request):
    if request.method == "GET":
        return spa_index(request)
    return public_contact(request)


def newsletter_dispatch(request):
    if request.method == "GET":
        return spa_index(request)
    return public_newsletter(request)


def careers_apply_dispatch(request):
    if request.method == "GET":
        return spa_index(request)
    return public_apply(request)
