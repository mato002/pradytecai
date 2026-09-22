#!/usr/bin/env python
"""Django's command-line utility for administrative tasks."""
import os
import sys
from pathlib import Path


def main():
    # Ensure project root is first on sys.path (ahead of any accidental cwd quirks)
    root = Path(__file__).resolve().parent
    root_str = str(root)
    if root_str not in sys.path:
        sys.path.insert(0, root_str)

    os.environ.setdefault("DJANGO_SETTINGS_MODULE", "config.settings")

    # Soft guard: prefer the project .venv interpreter
    venv_python = root / ".venv" / "Scripts" / "python.exe"
    if not venv_python.exists():
        venv_python = root / ".venv" / "bin" / "python"
    if venv_python.exists() and Path(sys.executable).resolve() != venv_python.resolve():
        # Only warn — do not hard-fail (CI / alternate envs are valid)
        if "runserver" in sys.argv or "migrate" in sys.argv:
            print(
                f"Note: running with {sys.executable}\n"
                f"      Recommended: {venv_python}",
                file=sys.stderr,
            )

    try:
        from django.core.management import execute_from_command_line
    except ImportError as exc:
        raise ImportError(
            "Couldn't import Django. Activate .venv and install requirements.txt."
        ) from exc
    execute_from_command_line(sys.argv)


if __name__ == "__main__":
    main()
