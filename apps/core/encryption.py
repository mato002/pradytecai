"""Decrypt Laravel encrypted cast values (AES-256-CBC + APP_KEY)."""

from __future__ import annotations

import base64
import hashlib
import json
import logging

from cryptography.hazmat.primitives.ciphers import Cipher, algorithms, modes
from django.conf import settings

logger = logging.getLogger(__name__)


def _parse_laravel_key(app_key: str) -> bytes:
    """Laravel APP_KEY is typically base64:XXXX."""
    key = (app_key or "").strip()
    if key.startswith("base64:"):
        return base64.b64decode(key[len("base64:") :])
    return key.encode("utf-8")


def decrypt_laravel_string(payload: str | None, app_key: str | None = None) -> str | None:
    """
    Decrypt a value produced by Laravel's Encrypter (AES-256-CBC).

    Does not log plaintext. Returns None on failure.
    """
    if not payload:
        return None
    key_material = app_key if app_key is not None else getattr(settings, "LARAVEL_APP_KEY", "")
    if not key_material:
        logger.warning("LARAVEL_APP_KEY / APP_KEY not configured; cannot decrypt")
        return None
    try:
        key = _parse_laravel_key(key_material)
        raw = json.loads(base64.b64decode(payload))
        iv = base64.b64decode(raw["iv"])
        value = base64.b64decode(raw["value"])
        cipher = Cipher(algorithms.AES(key), modes.CBC(iv))
        decryptor = cipher.decryptor()
        decrypted = decryptor.update(value) + decryptor.finalize()
        # PKCS7 unpad
        pad = decrypted[-1]
        decrypted = decrypted[:-pad]
        # Laravel may serialize PHP strings with a prefix; strip common serialize wrapper
        text = decrypted.decode("utf-8")
        if text.startswith("s:") and '"' in text:
            # PHP serialize: s:N:"content";
            start = text.find('"') + 1
            end = text.rfind('"')
            if start > 0 and end > start:
                return text[start:end]
        return text
    except Exception:
        logger.exception("Failed to decrypt Laravel payload")
        return None


def laravel_payload_fingerprint(payload: str | None) -> str | None:
    """Stable non-secret fingerprint for logging/idempotency (not reversible)."""
    if not payload:
        return None
    return hashlib.sha256(payload.encode("utf-8")).hexdigest()[:16]
