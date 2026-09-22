"""Lead / communication Celery tasks (email, BulkSMS, UltraMsg)."""

from __future__ import annotations

import logging

import urllib.request
import json
from celery import shared_task
from django.conf import settings
from django.core.mail import send_mail

logger = logging.getLogger(__name__)


def _load_comm(comm_id: int):
    from apps.leads.models import LeadCommunication

    return LeadCommunication.objects.select_related("contact_message").get(pk=comm_id)


@shared_task(name="leads.tasks.send_lead_email", max_retries=5, bind=True)
def send_lead_email(self, communication_id: int):
    comm = _load_comm(communication_id)
    if comm.status == "sent":
        return {"ok": True, "idempotent": True}
    try:
        send_mail(
            subject=comm.subject or "Message from Pradytec",
            message=comm.body,
            from_email=settings.DEFAULT_FROM_EMAIL,
            recipient_list=[comm.contact_message.email],
            fail_silently=False,
        )
        comm.status = "sent"
        comm.save(update_fields=["status", "updated_at"])
        return {"ok": True}
    except Exception as exc:
        comm.status = "failed"
        comm.save(update_fields=["status", "updated_at"])
        raise self.retry(exc=exc, countdown=60 * (2 ** self.request.retries))


@shared_task(name="leads.tasks.send_lead_sms", max_retries=5, bind=True)
def send_lead_sms(self, communication_id: int):
    comm = _load_comm(communication_id)
    if comm.status == "sent":
        return {"ok": True, "idempotent": True}
    if not settings.BULKSMS_CRM_ENABLED or not settings.BULKSMS_API_KEY:
        logger.warning("BulkSMS not configured; marking failed")
        comm.status = "failed"
        comm.save(update_fields=["status", "updated_at"])
        return {"ok": False, "reason": "not_configured"}
    phone = comm.contact_message.phone
    if not phone:
        comm.status = "failed"
        comm.save(update_fields=["status", "updated_at"])
        return {"ok": False, "reason": "no_phone"}
    try:
        payload = json.dumps(
            {
                "client_id": settings.BULKSMS_CLIENT_ID,
                "sender_id": settings.BULKSMS_SENDER_ID,
                "message": comm.body,
                "phone": phone,
            }
        ).encode()
        req = urllib.request.Request(
            f"{settings.BULKSMS_API_URL.rstrip('/')}/sms/send",
            data=payload,
            headers={
                "Content-Type": "application/json",
                "Authorization": f"Bearer {settings.BULKSMS_API_KEY}",
            },
            method="POST",
        )
        with urllib.request.urlopen(req, timeout=30) as resp:
            resp.read()
        comm.status = "sent"
        comm.save(update_fields=["status", "updated_at"])
        return {"ok": True}
    except Exception as exc:
        logger.exception("BulkSMS send failed")
        msg = str(exc).lower()
        if any(x in msg for x in ("unauthorized", "forbidden", "401", "403")):
            comm.status = "failed"
            comm.save(update_fields=["status", "updated_at"])
            return {"ok": False, "reason": "permanent"}
        raise self.retry(exc=exc, countdown=60 * (2 ** self.request.retries))


@shared_task(name="leads.tasks.send_lead_whatsapp", max_retries=5, bind=True)
def send_lead_whatsapp(self, communication_id: int):
    comm = _load_comm(communication_id)
    if comm.status == "sent":
        return {"ok": True, "idempotent": True}
    if not settings.ULTRAMSG_INSTANCE_ID or not settings.ULTRAMSG_TOKEN:
        comm.status = "failed"
        comm.save(update_fields=["status", "updated_at"])
        return {"ok": False, "reason": "not_configured"}
    phone = comm.contact_message.phone
    if not phone:
        comm.status = "failed"
        comm.save(update_fields=["status", "updated_at"])
        return {"ok": False, "reason": "no_phone"}
    try:
        url = (
            f"{settings.ULTRAMSG_API_URL.rstrip('/')}/{settings.ULTRAMSG_INSTANCE_ID}/messages/chat"
        )
        payload = json.dumps({"token": settings.ULTRAMSG_TOKEN, "to": phone, "body": comm.body}).encode()
        req = urllib.request.Request(
            url, data=payload, headers={"Content-Type": "application/json"}, method="POST"
        )
        with urllib.request.urlopen(req, timeout=30) as resp:
            resp.read()
        comm.status = "sent"
        comm.save(update_fields=["status", "updated_at"])
        return {"ok": True}
    except Exception as exc:
        msg = str(exc).lower()
        if any(x in msg for x in ("unauthorized", "forbidden", "401", "403")):
            comm.status = "failed"
            comm.save(update_fields=["status", "updated_at"])
            return {"ok": False, "reason": "permanent"}
        raise self.retry(exc=exc, countdown=60 * (2 ** self.request.retries))
