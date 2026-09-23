from rest_framework import serializers

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


def file_url(file_field):
    if not file_field:
        return None
    try:
        return file_field.url
    except ValueError:
        return None


class ProductHighlightSerializer(serializers.ModelSerializer):
    class Meta:
        model = ProductHighlight
        fields = ["id", "title", "short_description", "icon", "display_order"]


class ProductAudienceSerializer(serializers.ModelSerializer):
    class Meta:
        model = ProductAudience
        fields = ["id", "title", "description", "icon", "display_order"]


class ProductProblemSerializer(serializers.ModelSerializer):
    class Meta:
        model = ProductProblem
        fields = ["id", "title", "description", "icon", "display_order"]


class ProductCapabilitySerializer(serializers.ModelSerializer):
    class Meta:
        model = ProductCapability
        fields = ["id", "title", "description", "display_order"]


class ProductCapabilityGroupSerializer(serializers.ModelSerializer):
    capabilities = ProductCapabilitySerializer(many=True, read_only=True)

    class Meta:
        model = ProductCapabilityGroup
        fields = ["id", "title", "description", "icon", "display_order", "capabilities"]


class ProductWorkflowStepSerializer(serializers.ModelSerializer):
    class Meta:
        model = ProductWorkflowStep
        fields = ["id", "title", "description", "step_number", "icon", "display_order"]


class ProductMediaSerializer(serializers.ModelSerializer):
    image_url = serializers.SerializerMethodField()

    class Meta:
        model = ProductMedia
        fields = [
            "id",
            "title",
            "caption",
            "image_url",
            "media_type",
            "alt_text",
            "display_order",
        ]

    def get_image_url(self, obj):
        return file_url(obj.image)


class ProductIntegrationSerializer(serializers.ModelSerializer):
    logo_url = serializers.SerializerMethodField()

    class Meta:
        model = ProductIntegration
        fields = ["id", "name", "description", "logo_url", "display_order"]

    def get_logo_url(self, obj):
        return file_url(obj.logo)


class ProductControlSerializer(serializers.ModelSerializer):
    class Meta:
        model = ProductControl
        fields = ["id", "title", "description", "icon", "display_order"]


class ProductOutcomeSerializer(serializers.ModelSerializer):
    class Meta:
        model = ProductOutcome
        fields = ["id", "title", "description", "display_order"]


class ProductImplementationStepSerializer(serializers.ModelSerializer):
    class Meta:
        model = ProductImplementationStep
        fields = ["id", "title", "description", "step_number", "display_order"]


class ProductFAQSerializer(serializers.ModelSerializer):
    class Meta:
        model = ProductFAQ
        fields = ["id", "question", "answer", "display_order"]


class ProductCustomSectionSerializer(serializers.ModelSerializer):
    image_url = serializers.SerializerMethodField()

    class Meta:
        model = ProductCustomSection
        fields = [
            "id",
            "title",
            "subtitle",
            "body",
            "image_url",
            "layout_type",
            "display_order",
        ]

    def get_image_url(self, obj):
        return file_url(obj.image)


class ProductPageSectionSerializer(serializers.ModelSerializer):
    class Meta:
        model = ProductPageSection
        fields = ["id", "section_type", "title_override", "subtitle", "display_order", "is_enabled"]


class PublicProductDetailSerializer(serializers.ModelSerializer):
    """Rich public payload for one product page. List responses stay on PublicProductSerializer."""

    poster_url = serializers.SerializerMethodField()
    hero_image_url = serializers.SerializerMethodField()
    mobile_image_url = serializers.SerializerMethodField()
    short_description = serializers.CharField(source="short", read_only=True)
    overview = serializers.CharField(source="description", read_only=True)
    display_order = serializers.IntegerField(source="order", read_only=True)
    cta_url = serializers.CharField(source="url", read_only=True)
    highlights = ProductHighlightSerializer(many=True, read_only=True)
    audiences = ProductAudienceSerializer(many=True, read_only=True)
    problems = ProductProblemSerializer(many=True, read_only=True)
    capability_groups = ProductCapabilityGroupSerializer(many=True, read_only=True)
    workflow_steps = ProductWorkflowStepSerializer(many=True, read_only=True)
    media = ProductMediaSerializer(source="media_items", many=True, read_only=True)
    integrations = ProductIntegrationSerializer(many=True, read_only=True)
    controls = ProductControlSerializer(many=True, read_only=True)
    outcomes = ProductOutcomeSerializer(many=True, read_only=True)
    implementation_steps = ProductImplementationStepSerializer(many=True, read_only=True)
    faqs = ProductFAQSerializer(many=True, read_only=True)
    custom_sections = ProductCustomSectionSerializer(many=True, read_only=True)
    page_sections = ProductPageSectionSerializer(many=True, read_only=True)

    class Meta:
        model = Product
        fields = [
            "id",
            "name",
            "slug",
            "tagline",
            "short",
            "short_description",
            "description",
            "overview",
            "market",
            "icon",
            "group_key",
            "poster_url",
            "hero_image_url",
            "mobile_image_url",
            "cta_label",
            "cta_type",
            "cta_url",
            "secondary_cta_label",
            "secondary_cta_type",
            "secondary_cta_url",
            "seo_title",
            "seo_description",
            "is_featured",
            "display_order",
            "highlights",
            "audiences",
            "problems",
            "capability_groups",
            "workflow_steps",
            "media",
            "integrations",
            "controls",
            "outcomes",
            "implementation_steps",
            "faqs",
            "custom_sections",
            "page_sections",
        ]

    def get_poster_url(self, obj):
        return file_url(obj.poster)

    def get_hero_image_url(self, obj):
        return file_url(obj.hero_image)

    def get_mobile_image_url(self, obj):
        return file_url(obj.mobile_image)
