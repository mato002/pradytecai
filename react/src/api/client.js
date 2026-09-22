function getCookie(name) {
  const match = document.cookie.match(new RegExp("(^| )" + name + "=([^;]+)"));
  return match ? decodeURIComponent(match[2]) : null;
}

/** Match Django CSRF_COOKIE_NAME (default csrftoken or pradytecai_django_csrftoken). */
function getCsrfToken() {
  return (
    getCookie("pradytecai_django_csrftoken") ||
    getCookie("csrftoken") ||
    getCookie("CSRF-TOKEN")
  );
}

export async function api(path, options = {}) {
  const headers = {
    Accept: "application/json",
    ...(options.body && !(options.body instanceof FormData)
      ? { "Content-Type": "application/json" }
      : {}),
    ...options.headers,
  };
  const method = (options.method || "GET").toUpperCase();
  if (method !== "GET" && method !== "HEAD") {
    const csrf = getCsrfToken();
    if (csrf) {
      headers["X-CSRFToken"] = csrf;
    }
  }
  const res = await fetch(`/api/v1${path}`, {
    credentials: "same-origin",
    ...options,
    headers,
    body:
      options.body && !(options.body instanceof FormData) && typeof options.body !== "string"
        ? JSON.stringify(options.body)
        : options.body,
  });
  const text = await res.text();
  let data = null;
  try {
    data = text ? JSON.parse(text) : null;
  } catch {
    data = { detail: text };
  }
  if (!res.ok) {
    const detail =
      (typeof data?.detail === "string" && data.detail) ||
      (Array.isArray(data?.non_field_errors) && data.non_field_errors[0]) ||
      res.statusText;
    const err = new Error(detail);
    err.status = res.status;
    err.data = data;
    throw err;
  }
  return data;
}

export async function ensureCsrf() {
  await api("/auth/csrf/");
}
