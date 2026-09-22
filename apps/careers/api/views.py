from django.core.files.storage import default_storage
from django.utils import timezone
from rest_framework import serializers, viewsets
from rest_framework.decorators import api_view, permission_classes
from rest_framework.permissions import AllowAny
from rest_framework.response import Response

from apps.accounts.permissions import require_perm
from apps.careers.models import ApplicationComment, JobApplication, Position


class PositionSerializer(serializers.ModelSerializer):
    class Meta:
        model = Position
        fields = "__all__"


class JobApplicationSerializer(serializers.ModelSerializer):
    class Meta:
        model = JobApplication
        fields = "__all__"


class PositionViewSet(viewsets.ModelViewSet):
    queryset = Position.objects.all()
    serializer_class = PositionSerializer
    permission_classes = [require_perm("careers.view|careers.manage")]

    def get_permissions(self):
        if self.action in ("create", "update", "partial_update", "destroy"):
            return [require_perm("careers.manage")()]
        return super().get_permissions()


class JobApplicationViewSet(viewsets.ModelViewSet):
    queryset = JobApplication.objects.select_related("position").all()
    serializer_class = JobApplicationSerializer
    permission_classes = [require_perm("careers.view|careers.manage")]

    def get_permissions(self):
        if self.action in ("create", "update", "partial_update", "destroy"):
            return [require_perm("careers.manage")()]
        return super().get_permissions()


@api_view(["GET"])
@permission_classes([AllowAny])
def public_positions(request):
    qs = Position.objects.filter(is_active=True)
    return Response(PositionSerializer(qs, many=True).data)


@api_view(["POST"])
@permission_classes([AllowAny])
def public_apply(request):
    position_id = request.data.get("position_id")
    position = Position.objects.filter(pk=position_id, is_active=True).first()
    if not position:
        return Response({"detail": "Position not found."}, status=404)
    resume = request.FILES.get("resume")
    resume_path = None
    if resume:
        resume_path = default_storage.save(f"resumes/{resume.name}", resume)
    app = JobApplication.objects.create(
        position=position,
        name=request.data.get("name", ""),
        email=request.data.get("email", ""),
        phone=request.data.get("phone") or None,
        cover_letter=request.data.get("cover_letter", ""),
        resume_path=resume_path,
        status="pending",
    )
    return Response({"id": app.id, "detail": "Application received."}, status=201)
