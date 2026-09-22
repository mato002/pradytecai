from django.utils import timezone
from rest_framework import serializers, viewsets
from rest_framework.decorators import api_view, permission_classes
from rest_framework.permissions import AllowAny, IsAuthenticated
from rest_framework.response import Response

from apps.accounts.permissions import require_perm
from apps.marketing.models import BlogPost, NewsletterSubscriber, SiteSetting
from apps.products.models import Product


class BlogPostSerializer(serializers.ModelSerializer):
    class Meta:
        model = BlogPost
        fields = "__all__"


class BlogPostViewSet(viewsets.ModelViewSet):
    queryset = BlogPost.objects.all()
    serializer_class = BlogPostSerializer
    lookup_field = "slug"
    permission_classes = [require_perm("blog.view|blog.manage")]

    def get_permissions(self):
        if self.action in ("create", "update", "partial_update", "destroy"):
            return [require_perm("blog.manage")()]
        return super().get_permissions()


class SubscriberSerializer(serializers.ModelSerializer):
    class Meta:
        model = NewsletterSubscriber
        fields = "__all__"


class SubscriberViewSet(viewsets.ReadOnlyModelViewSet):
    queryset = NewsletterSubscriber.objects.all().order_by("-id")
    serializer_class = SubscriberSerializer
    permission_classes = [require_perm("subscribers.view|subscribers.manage")]


class SiteSettingSerializer(serializers.ModelSerializer):
    class Meta:
        model = SiteSetting
        fields = ["id", "key", "value"]


class SettingsViewSet(viewsets.ModelViewSet):
    queryset = SiteSetting.objects.all()
    serializer_class = SiteSettingSerializer
    lookup_field = "key"
    permission_classes = [require_perm("settings.view|settings.manage")]

    def get_permissions(self):
        if self.action in ("create", "update", "partial_update", "destroy"):
            return [require_perm("settings.manage")()]
        return super().get_permissions()


@api_view(["GET"])
@permission_classes([AllowAny])
def public_home(request):
    from apps.products.api.views import PublicProductSerializer

    products = Product.objects.filter(is_active=True).order_by("order", "name")
    featured = products.filter(is_featured=True).first()
    posts = BlogPost.objects.filter(is_published=True).order_by("-published_at")[:6]
    return Response(
        {
            "products": PublicProductSerializer(
                products[:24], many=True, context={"request": request}
            ).data,
            "featured": PublicProductSerializer(featured, context={"request": request}).data
            if featured
            else None,
            "posts": BlogPostSerializer(posts, many=True).data,
            "site_name": SiteSetting.get("site.name", "PradytecAI"),
            "tagline": SiteSetting.get("site.tagline", ""),
        }
    )


@api_view(["GET"])
@permission_classes([AllowAny])
def public_blog_list(request):
    posts = BlogPost.objects.filter(is_published=True)
    return Response(BlogPostSerializer(posts, many=True).data)


@api_view(["GET"])
@permission_classes([AllowAny])
def public_blog_detail(request, slug):
    post = BlogPost.objects.filter(slug=slug, is_published=True).first()
    if not post:
        return Response({"detail": "Not found."}, status=404)
    return Response(BlogPostSerializer(post).data)


@api_view(["POST"])
@permission_classes([AllowAny])
def public_newsletter(request):
    email = (request.data.get("email") or "").strip().lower()
    if not email:
        return Response({"detail": "Email required."}, status=400)
    sub, created = NewsletterSubscriber.objects.get_or_create(
        email=email,
        defaults={"status": "subscribed", "subscribed_at": timezone.now()},
    )
    return Response({"id": sub.id, "created": created})


@api_view(["GET"])
@permission_classes([AllowAny])
def public_search(request):
    q = (request.GET.get("q") or "").strip()
    products = Product.objects.filter(is_active=True, name__icontains=q)[:20] if q else []
    posts = BlogPost.objects.filter(is_published=True, title__icontains=q)[:20] if q else []
    return Response(
        {
            "q": q,
            "products": [{"id": p.id, "name": p.name, "slug": p.slug} for p in products],
            "posts": [{"id": p.id, "title": p.title, "slug": p.slug} for p in posts],
        }
    )


@api_view(["GET"])
@permission_classes([IsAuthenticated])
def dashboard(request):
    if not request.user.has_perm_name("dashboard.view") and not request.user.is_super_admin_user():
        return Response({"detail": "Forbidden"}, status=403)
    from apps.leads.models import ContactMessage
    from apps.content.models import ContentItem
    from apps.core.visibility import filter_by_product_scope

    return Response(
        {
            "leads_new": filter_by_product_scope(
                ContactMessage.objects.filter(status="new"), request.user
            ).count(),
            "content_draft": filter_by_product_scope(
                ContentItem.objects.filter(status="draft"), request.user
            ).count(),
        }
    )
