import React, { useCallback, useEffect, useState } from "react";
import { Navigate } from "react-router-dom";
import { api, ensureCsrf } from "../../api/client";
import { useAuth } from "../../auth/AuthContext";

const EMPTY = {
  name: "",
  slug: "",
  tagline: "",
  short: "",
  description: "",
  market: "",
  icon: "",
  cta_label: "Request demo",
  cta_type: "demo",
  url: "",
  secondary_cta_label: "",
  secondary_cta_type: "contact",
  secondary_cta_url: "",
  seo_title: "",
  seo_description: "",
  is_active: true,
  is_featured: false,
  order: 0,
  group_key: "",
};

function Field({ label, children }) {
  return (
    <label className="admin-field">
      <span>{label}</span>
      {children}
    </label>
  );
}

export default function ProductsAdmin() {
  const { can } = useAuth();
  const canManage = can("products.manage");
  const [rows, setRows] = useState([]);
  const [error, setError] = useState("");
  const [loading, setLoading] = useState(true);
  const [editing, setEditing] = useState(null);
  const [form, setForm] = useState(EMPTY);
  const [posterFile, setPosterFile] = useState(null);
  const [saving, setSaving] = useState(false);
  const [notice, setNotice] = useState("");

  const load = useCallback(() => {
    setLoading(true);
    api("/products/?page_size=100")
      .then((data) => {
        if (Array.isArray(data)) setRows(data);
        else if (data?.results) setRows(data.results);
        else setRows([]);
      })
      .catch((err) => setError(err.message || "Failed to load"))
      .finally(() => setLoading(false));
  }, []);

  useEffect(() => {
    if (!can("products.view")) return;
    load();
  }, [can, load]);

  if (!can("products.view")) return <Navigate to="/admin" replace />;

  function openCreate() {
    setEditing("new");
    setForm(EMPTY);
    setPosterFile(null);
    setNotice("");
  }

  function openEdit(row) {
    setEditing(row.id);
    setForm({
      name: row.name || "",
      slug: row.slug || "",
      tagline: row.tagline || "",
      short: row.short || "",
      description: row.description || "",
      market: row.market || "",
      icon: row.icon || "",
      cta_label: row.cta_label || "Request demo",
      cta_type: row.cta_type || "demo",
      url: row.url || "",
      secondary_cta_label: row.secondary_cta_label || "",
      secondary_cta_type: row.secondary_cta_type || "contact",
      secondary_cta_url: row.secondary_cta_url || "",
      seo_title: row.seo_title || "",
      seo_description: row.seo_description || "",
      is_active: row.is_active !== false,
      is_featured: !!row.is_featured,
      order: row.order ?? 0,
      group_key: row.group_key || "",
      poster_url: row.poster_url || null,
    });
    setPosterFile(null);
    setNotice("");
  }

  function set(k, v) {
    setForm((f) => ({ ...f, [k]: v }));
  }

  async function onSave(e) {
    e.preventDefault();
    if (!canManage) return;
    setSaving(true);
    setNotice("");
    try {
      await ensureCsrf();
      const body = new FormData();
      body.append("name", form.name);
      if (form.slug) body.append("slug", form.slug);
      body.append("tagline", form.tagline || "");
      body.append("short", form.short || "");
      body.append("description", form.description || "");
      body.append("secondary_cta_label", form.secondary_cta_label || "");
      body.append("secondary_cta_type", form.secondary_cta_type || "");
      body.append("secondary_cta_url", form.secondary_cta_url || "");
      body.append("seo_title", form.seo_title || "");
      body.append("seo_description", form.seo_description || "");
      body.append("market", form.market || "");
      body.append("icon", form.icon || "");
      body.append("cta_label", form.cta_label || "");
      body.append("cta_type", form.cta_type || "demo");
      body.append("url", form.url || "");
      body.append("is_active", form.is_active ? "true" : "false");
      body.append("is_featured", form.is_featured ? "true" : "false");
      body.append("order", String(form.order ?? 0));
      body.append("group_key", form.group_key || "");
      if (posterFile) body.append("poster", posterFile);

      let saved;
      if (editing === "new") {
        saved = await api("/products/", { method: "POST", body });
      } else {
        saved = await api(`/products/${editing}/`, { method: "PATCH", body });
      }
      setNotice("Saved.");
      setEditing(saved.id);
      setForm((f) => ({ ...f, poster_url: saved.poster_url, slug: saved.slug }));
      setPosterFile(null);
      load();
    } catch (err) {
      setNotice(err.message || "Save failed");
    } finally {
      setSaving(false);
    }
  }

  async function onDelete(row) {
    if (!canManage) return;
    if (!window.confirm(`Delete “${row.name}”? Linked enquiries will keep their history.`)) return;
    try {
      await ensureCsrf();
      await api(`/products/${row.id}/`, { method: "DELETE" });
      if (editing === row.id) {
        setEditing(null);
        setForm(EMPTY);
      }
      load();
    } catch (err) {
      setError(err.message || "Delete failed");
    }
  }

  async function toggleActive(row) {
    if (!canManage) return;
    try {
      await ensureCsrf();
      await api(`/products/${row.id}/`, {
        method: "PATCH",
        body: { is_active: !row.is_active },
      });
      load();
    } catch (err) {
      setError(err.message || "Update failed");
    }
  }

  return (
    <div className="admin-panel">
      <div className="admin-panel__head">
        <h2>Products · {rows.length}</h2>
        {canManage && (
          <button type="button" className="admin-btn admin-btn--primary" onClick={openCreate}>
            New product
          </button>
        )}
      </div>
      {loading && <p className="p-6 text-[var(--admin-muted)]">Loading…</p>}
      {error && <p className="p-6 text-[var(--admin-danger)]">{error}</p>}
      {!loading && (
        <div className="admin-split">
          <div className="overflow-x-auto">
            <table className="admin-table">
              <thead>
                <tr>
                  <th>Order</th>
                  <th>Product</th>
                  <th>Status</th>
                  <th />
                </tr>
              </thead>
              <tbody>
                {rows.map((row) => (
                  <tr key={row.id} className={editing === row.id ? "admin-row--active" : ""}>
                    <td className="text-[var(--admin-muted)]">{row.order}</td>
                    <td>
                      <button type="button" className="admin-linkish" onClick={() => openEdit(row)}>
                        {row.name}
                      </button>
                      <div className="text-xs text-[var(--admin-muted)]">{row.slug}</div>
                    </td>
                    <td>
                      <span
                        className={`admin-status ${
                          row.is_active ? "admin-status--ok" : "admin-status--warn"
                        }`}
                      >
                        {row.is_active ? "active" : "inactive"}
                        {row.is_featured ? " · featured" : ""}
                      </span>
                    </td>
                    <td className="space-x-2 whitespace-nowrap">
                      {canManage && (
                        <>
                          <button type="button" className="admin-linkish" onClick={() => toggleActive(row)}>
                            {row.is_active ? "Deactivate" : "Activate"}
                          </button>
                          <button type="button" className="admin-linkish text-red-700" onClick={() => onDelete(row)}>
                            Delete
                          </button>
                        </>
                      )}
                    </td>
                  </tr>
                ))}
                {!rows.length && (
                  <tr>
                    <td colSpan={4} className="text-[var(--admin-muted)]">
                      No products yet. Run <code>seed_products</code> or create one.
                    </td>
                  </tr>
                )}
              </tbody>
            </table>
          </div>

          {editing && (
            <form className="admin-form" onSubmit={onSave}>
              <h3>{editing === "new" ? "Create product" : "Edit product"}</h3>
              <Field label="Name">
                <input required value={form.name} onChange={(e) => set("name", e.target.value)} disabled={!canManage} />
              </Field>
              <Field label="Slug">
                <input value={form.slug} onChange={(e) => set("slug", e.target.value)} disabled={!canManage} placeholder="auto from name" />
              </Field>
              <Field label="Tagline">
                <input value={form.tagline} onChange={(e) => set("tagline", e.target.value)} disabled={!canManage} />
              </Field>
              <Field label="Short description">
                <input value={form.short} onChange={(e) => set("short", e.target.value)} disabled={!canManage} />
              </Field>
              <Field label="Full description">
                <textarea rows={5} value={form.description} onChange={(e) => set("description", e.target.value)} disabled={!canManage} />
              </Field>
              <Field label="Market">
                <input value={form.market} onChange={(e) => set("market", e.target.value)} disabled={!canManage} />
              </Field>
              <div className="admin-form__row">
                <Field label="Icon key">
                  <input value={form.icon} onChange={(e) => set("icon", e.target.value)} disabled={!canManage} />
                </Field>
                <Field label="Group">
                  <select value={form.group_key} onChange={(e) => set("group_key", e.target.value)} disabled={!canManage}>
                    <option value="">—</option>
                    <option value="finance">finance</option>
                    <option value="assets">assets</option>
                    <option value="commerce">commerce</option>
                  </select>
                </Field>
                <Field label="Display order">
                  <input
                    type="number"
                    value={form.order}
                    onChange={(e) => set("order", Number(e.target.value))}
                    disabled={!canManage}
                  />
                </Field>
              </div>
              <div className="admin-form__row">
                <Field label="CTA label">
                  <input value={form.cta_label} onChange={(e) => set("cta_label", e.target.value)} disabled={!canManage} />
                </Field>
                <Field label="CTA type">
                  <select value={form.cta_type} onChange={(e) => set("cta_type", e.target.value)} disabled={!canManage}>
                    <option value="demo">demo</option>
                    <option value="contact">contact</option>
                    <option value="external">external</option>
                  </select>
                </Field>
                <Field label="CTA URL">
                  <input value={form.url} onChange={(e) => set("url", e.target.value)} disabled={!canManage} />
                </Field>
              </div>
              <div className="admin-form__row">
                <Field label="Secondary CTA">
                  <input
                    value={form.secondary_cta_label}
                    onChange={(e) => set("secondary_cta_label", e.target.value)}
                    disabled={!canManage}
                    placeholder="Talk to Our Team"
                  />
                </Field>
                <Field label="Secondary type">
                  <select
                    value={form.secondary_cta_type}
                    onChange={(e) => set("secondary_cta_type", e.target.value)}
                    disabled={!canManage}
                  >
                    <option value="contact">contact</option>
                    <option value="demo">demo</option>
                    <option value="external">external</option>
                  </select>
                </Field>
                <Field label="Secondary URL">
                  <input
                    value={form.secondary_cta_url}
                    onChange={(e) => set("secondary_cta_url", e.target.value)}
                    disabled={!canManage}
                  />
                </Field>
              </div>
              <Field label="SEO title">
                <input value={form.seo_title} onChange={(e) => set("seo_title", e.target.value)} disabled={!canManage} />
              </Field>
              <Field label="SEO description">
                <textarea
                  rows={2}
                  value={form.seo_description}
                  onChange={(e) => set("seo_description", e.target.value)}
                  disabled={!canManage}
                />
              </Field>
              <div className="admin-form__checks">
                <label>
                  <input
                    type="checkbox"
                    checked={!!form.is_active}
                    onChange={(e) => set("is_active", e.target.checked)}
                    disabled={!canManage}
                  />{" "}
                  Active
                </label>
                <label>
                  <input
                    type="checkbox"
                    checked={!!form.is_featured}
                    onChange={(e) => set("is_featured", e.target.checked)}
                    disabled={!canManage}
                  />{" "}
                  Featured
                </label>
              </div>
              <Field label="Poster image (JPEG/PNG/WebP/GIF, max 5 MB — preferred 4:3)">
                <input
                  type="file"
                  accept="image/jpeg,image/png,image/webp,image/gif"
                  onChange={(e) => setPosterFile(e.target.files?.[0] || null)}
                  disabled={!canManage}
                />
              </Field>
              {form.poster_url && (
                <img src={form.poster_url} alt="" className="admin-poster-preview" />
              )}
              {canManage && (
                <button type="submit" className="admin-btn admin-btn--primary" disabled={saving}>
                  {saving ? "Saving…" : "Save product"}
                </button>
              )}
              <p className="text-sm text-[var(--admin-muted)]">
                Highlights, audiences, capabilities, screenshots and section order are edited in Django admin.
              </p>
              {notice && <p className="text-sm text-[var(--admin-muted)]">{notice}</p>}
            </form>
          )}
        </div>
      )}
    </div>
  );
}
