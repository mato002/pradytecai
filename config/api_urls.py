from django.urls import include, path
from rest_framework.routers import DefaultRouter

from apps.accounts.api import auth as auth_api
from apps.accounts.api.users import RoleViewSet, UserViewSet
from apps.analytics.api.views import PulseViewSet, analytics_overview
from apps.campaigns.api.views import CampaignViewSet
from apps.careers.api.views import JobApplicationViewSet, PositionViewSet, public_apply, public_positions
from apps.content.api.views import ContentItemViewSet
from apps.core.views import health
from apps.leads.api.views import DemoRequestViewSet, EnquiryViewSet, public_contact
from apps.marketing.api.views import (
    BlogPostViewSet,
    SettingsViewSet,
    SubscriberViewSet,
    dashboard,
    public_blog_detail,
    public_blog_list,
    public_home,
    public_newsletter,
    public_search,
)
from apps.products.api.views import ProductViewSet, public_product_detail, public_product_list
from apps.social.api.views import IntegrationViewSet, SocialAccountViewSet
from apps.tasks.api.views import ActivityLogViewSet, MarketingTaskViewSet

router = DefaultRouter()
router.register(r"users", UserViewSet, basename="users")
router.register(r"roles", RoleViewSet, basename="roles")
router.register(r"products", ProductViewSet, basename="products")
router.register(r"enquiries", EnquiryViewSet, basename="enquiries")
router.register(r"demos", DemoRequestViewSet, basename="demos")
router.register(r"campaigns", CampaignViewSet, basename="campaigns")
router.register(r"content", ContentItemViewSet, basename="content")
router.register(r"social-accounts", SocialAccountViewSet, basename="social-accounts")
router.register(r"integrations", IntegrationViewSet, basename="integrations")
router.register(r"pulse", PulseViewSet, basename="pulse")
router.register(r"positions", PositionViewSet, basename="positions")
router.register(r"applications", JobApplicationViewSet, basename="applications")
router.register(r"blog", BlogPostViewSet, basename="blog")
router.register(r"subscribers", SubscriberViewSet, basename="subscribers")
router.register(r"settings", SettingsViewSet, basename="settings")
router.register(r"tasks", MarketingTaskViewSet, basename="tasks")
router.register(r"activity-logs", ActivityLogViewSet, basename="activity-logs")

urlpatterns = [
    path("auth/csrf/", auth_api.csrf),
    path("auth/login/", auth_api.login_view),
    path("auth/logout/", auth_api.logout_view),
    path("auth/me/", auth_api.me),
    path("dashboard/", dashboard),
    path("analytics/overview/", analytics_overview),
    path("public/home/", public_home),
    path("public/blog/", public_blog_list),
    path("public/blog/<slug:slug>/", public_blog_detail),
    path("public/search/", public_search),
    path("public/newsletter/", public_newsletter),
    path("public/contact/", public_contact),
    path("public/products/", public_product_list),
    path("public/products/<slug:slug>/", public_product_detail),
    path("public/positions/", public_positions),
    path("public/careers/apply/", public_apply),
    path("", include(router.urls)),
]
