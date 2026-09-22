"""Laravel bcrypt password compatibility ($2y$ hashes in users.password)."""

from __future__ import annotations

import bcrypt
from django.contrib.auth.hashers import BasePasswordHasher, mask_hash
from django.utils.crypto import constant_time_compare


class LaravelBcryptPasswordHasher(BasePasswordHasher):
    """
    Verify and create Laravel-compatible bcrypt hashes.

    Existing rows store raw bcrypt strings like ``$2y$12$...`` with no Django
    algorithm prefix. New hashes are also stored in that Laravel-native form
    so rollback to Laravel remains possible during the dual-run window.
    """

    algorithm = "bcrypt_laravel"
    rounds = 12

    def salt(self):
        return bcrypt.gensalt(rounds=self.rounds).decode("ascii")

    def encode(self, password, salt):
        # salt from gensalt already includes rounds; bcrypt.hashpw wants bytes salt
        if isinstance(salt, str):
            salt_b = salt.encode("ascii")
        else:
            salt_b = salt
        hashed = bcrypt.hashpw(password.encode("utf-8"), salt_b).decode("ascii")
        # Prefer $2y$ for PHP bcrypt compatibility
        if hashed.startswith("$2b$"):
            hashed = "$2y$" + hashed[4:]
        # Store WITHOUT django algorithm prefix so Laravel can still verify.
        # Django identify() / must_update still work via harden_runtime detection.
        return hashed

    def decode(self, encoded):
        return {"algorithm": self.algorithm, "hash": encoded}

    def verify(self, password, encoded):
        raw = encoded
        if raw.startswith(f"{self.algorithm}$"):
            raw = raw[len(self.algorithm) + 1 :]
        # Normalize $2y$ → $2b$ for the bcrypt library
        check = raw
        if check.startswith("$2y$"):
            check = "$2b$" + check[4:]
        try:
            return bcrypt.checkpw(password.encode("utf-8"), check.encode("ascii"))
        except (ValueError, TypeError):
            return False

    def safe_summary(self, encoded):
        return {
            "algorithm": self.algorithm,
            "hash": mask_hash(encoded, show=3),
        }

    def must_update(self, encoded):
        return False

    def harden_runtime(self, password, encoded):
        pass


def is_laravel_bcrypt(encoded: str) -> bool:
    return bool(encoded) and (
        encoded.startswith("$2y$")
        or encoded.startswith("$2b$")
        or encoded.startswith("$2a$")
    )
