import React, { useCallback, useEffect, useId, useState } from "react";
import { Link, Navigate, useNavigate, useParams } from "react-router-dom";
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
  poster_url: null,
  hero_image_url: null,
  mobile_image_url: null,
};

const EMPTY_MEDIA = {
  media_type: "image",
  image_category: "screenshot",
  title: "",
  caption: "",
  alt_text: "",
  video_source: "upload",
  video_url: "",
  is_featured: false,
  display_order: 10,
  is_active: true,
};

/** URL-safe slug matching Django slugify behaviour for common product names. */
function slugify(value) {
  return String(value || "")
    .normalize("NFKD")
    .replace(/[\u0300-\u036f]/g, "")
    .toLowerCase()
    .replace(/[^a-z0-9]+/g, "-")
    .replace(/^-+|-+$/g, "")
    .slice(0, 240);
}

function FieldHelp({ text }) {
  const tipId = useId();
  if (!text) return null;
  return (
    <span className="admin-field-help">
      <button
        type="button"
        className="admin-field-help__btn"
        aria-describedby={tipId}
        aria-label="Field help"
        onMouseDown={(e) => e.preventDefault()}
        onClick={(e) => e.preventDefault()}
      >
        ?
      </button>
      <span id={tipId} role="tooltip" className="admin-field-help__tip">
        {text}
      </span>
    </span>
  );
}

function Field({ label, children, help, hint }) {
  return (
    <label className="admin-field">
      <span className="admin-field__label">
        <span>{label}</span>
        <FieldHelp text={help} />
      </span>
      {children}
      {hint ? <small className="admin-field__hint">{hint}</small> : null}
    </label>
  );
}

function CheckField({ label, help, children }) {
  return (
    <label className="admin-check-field">
      {children}
      <span className="admin-check-field__text">
        {label}
        <FieldHelp text={help} />
      </span>
    </label>
  );
}

function mediaPreview(item) {
  if (!item) return null;
  if (item.media_type === "video") {
    return item.thumbnail_url || item.video_file_url || item.video_url;
  }
  return item.image_url || item.thumbnail_url;
}

export default function ProductAdminEditor() {
  const { id } = useParams();
  const isNew = !id || id === "new";
  const navigate = useNavigate();
  const { can } = useAuth();
  const canManage = can("products.manage");

  const [form, setForm] = useState(EMPTY);
  const [posterFile, setPosterFile] = useState(null);
  const [heroFile, setHeroFile] = useState(null);
  const [mobileFile, setMobileFile] = useState(null);
  const [mediaItems, setMediaItems] = useState([]);
  const [mediaForm, setMediaForm] = useState(EMPTY_MEDIA);
  const [mediaImage, setMediaImage] = useState(null);
  const [mediaVideo, setMediaVideo] = useState(null);
  const [mediaThumb, setMediaThumb] = useState(null);
  const [loading, setLoading] = useState(!isNew);
  const [saving, setSaving] = useState(false);
  const [mediaSaving, setMediaSaving] = useState(false);
  const [error, setError] = useState("");
  const [notice, setNotice] = useState("");
  const [productId, setProductId] = useState(isNew ? null : id);
  /** When true, slug is locked to the name (auto). Manual slug edits unlock it. */
  const [slugLocked, setSlugLocked] = useState(isNew);

  const loadMedia = useCallback(async (pid) => {
    if (!pid) return;
    try {
      const rows = await api(`/products/${pid}/media/`);
      setMediaItems(Array.isArray(rows) ? rows : []);
    } catch (err) {
      setError(err.message || "Failed to load media");
    }
  }, []);

  useEffect(() => {
    if (!can("products.view")) return;
    if (isNew) {
      setForm(EMPTY);
      setSlugLocked(true);
      setLoading(false);
      return;
    }
    setLoading(true);
    api(`/products/${id}/`)
      .then((row) => {
        setProductId(row.id);
        const name = row.name || "";
        const slug = row.slug || "";
        // Keep auto-sync when the stored slug still matches the name.
        setSlugLocked(!slug || slug === slugify(name));
        setForm({
          name,
          slug,
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
          hero_image_url: row.hero_image_url || null,
          mobile_image_url: row.mobile_image_url || null,
        });
        return loadMedia(row.id);
      })
      .catch((err) => setError(err.message || "Failed to load product"))
      .finally(() => setLoading(false));
  }, [can, id, isNew, loadMedia]);

  if (!can("products.view")) return <Navigate to="/admin" replace />;

  function set(k, v) {
    setForm((f) => ({ ...f, [k]: v }));
  }

  function onNameChange(value) {
    setForm((f) => ({
      ...f,
      name: value,
      slug: slugLocked ? slugify(value) : f.slug,
    }));
  }

  function onSlugChange(value) {
    setSlugLocked(false);
    set("slug", slugify(value));
  }

  function setMedia(k, v) {
    setMediaForm((f) => ({ ...f, [k]: v }));
  }

  function appendProductFields(body) {
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
    if (heroFile) body.append("hero_image", heroFile);
    if (mobileFile) body.append("mobile_image", mobileFile);
  }

  async function onSave(e) {
    e.preventDefault();
    if (!canManage) return;
    setSaving(true);
    setNotice("");
    setError("");
    try {
      await ensureCsrf();
      const body = new FormData();
      appendProductFields(body);
      let saved;
      if (isNew || !productId) {
        saved = await api("/products/", { method: "POST", body });
        setNotice("Product created.");
        navigate(`/admin/products/${saved.id}/edit`, { replace: true });
        return;
      }
      saved = await api(`/products/${productId}/`, { method: "PATCH", body });
      setForm((f) => ({
        ...f,
        slug: saved.slug,
        poster_url: saved.poster_url,
        hero_image_url: saved.hero_image_url,
        mobile_image_url: saved.mobile_image_url,
      }));
      setPosterFile(null);
      setHeroFile(null);
      setMobileFile(null);
      setNotice("Saved.");
    } catch (err) {
      setError(err.message || "Save failed");
    } finally {
      setSaving(false);
    }
  }

  async function onAddMedia(e) {
    e.preventDefault();
    if (!canManage || !productId) return;
    setMediaSaving(true);
    setError("");
    try {
      await ensureCsrf();
      const body = new FormData();
      body.append("media_type", mediaForm.media_type);
      body.append("title", mediaForm.title || "");
      body.append("caption", mediaForm.caption || "");
      body.append("alt_text", mediaForm.alt_text || "");
      body.append("is_featured", mediaForm.is_featured ? "true" : "false");
      body.append("display_order", String(mediaForm.display_order ?? 10));
      body.append("is_active", mediaForm.is_active ? "true" : "false");

      if (mediaForm.media_type === "image") {
        if (mediaForm.image_category) body.append("image_category", mediaForm.image_category);
        if (mediaImage) body.append("image", mediaImage);
      } else {
        body.append("video_source", mediaForm.video_source || "upload");
        if (mediaForm.video_source === "external") {
          body.append("video_url", mediaForm.video_url || "");
        } else if (mediaVideo) {
          body.append("video_file", mediaVideo);
        }
        if (mediaThumb) body.append("thumbnail", mediaThumb);
      }

      await api(`/products/${productId}/media/`, { method: "POST", body });
      setMediaForm(EMPTY_MEDIA);
      setMediaImage(null);
      setMediaVideo(null);
      setMediaThumb(null);
      setNotice("Media added.");
      await loadMedia(productId);
    } catch (err) {
      const detail =
        err.data && typeof err.data === "object"
          ? Object.values(err.data).flat().join(" ") || err.message
          : err.message;
      setError(detail || "Media upload failed");
    } finally {
      setMediaSaving(false);
    }
  }

  async function onDeleteMedia(item) {
    if (!canManage || !productId) return;
    if (!window.confirm("Remove this media item?")) return;
    try {
      await ensureCsrf();
      await api(`/products/${productId}/media/${item.id}/`, { method: "DELETE" });
      await loadMedia(productId);
    } catch (err) {
      setError(err.message || "Delete failed");
    }
  }

  async function onToggleFeatured(item) {
    if (!canManage || !productId) return;
    try {
      await ensureCsrf();
      const body = new FormData();
      body.append("is_featured", item.is_featured ? "false" : "true");
      await api(`/products/${productId}/media/${item.id}/`, { method: "PATCH", body });
      await loadMedia(productId);
    } catch (err) {
      setError(err.message || "Update failed");
    }
  }

  if (loading) {
    return (
      <div className="admin-panel">
        <p className="p-6 text-[var(--admin-muted)]">Loading…</p>
      </div>
    );
  }

  return (
    <div className="admin-panel admin-product-editor">
      <div className="admin-panel__head">
        <div>
          <Link to="/admin/products" className="admin-linkish">
            ← Products
          </Link>
          <h2>{isNew ? "Create product" : `Edit · ${form.name || "Product"}`}</h2>
        </div>
        {!isNew && form.slug ? (
          <Link to={`/products/${form.slug}`} className="admin-btn" target="_blank" rel="noreferrer">
            View public page
          </Link>
        ) : null}
      </div>

      {error && <p className="admin-banner admin-banner--error">{error}</p>}
      {notice && <p className="admin-banner admin-banner--ok">{notice}</p>}

      <form className="admin-form admin-form--wide" onSubmit={onSave}>
        <h3>Basics</h3>
        <Field
          label="Name"
          help="Public product name shown on cards, detail pages, and menus."
        >
          <input required value={form.name} onChange={(e) => onNameChange(e.target.value)} disabled={!canManage} />
        </Field>
        <Field
          label="Slug"
          help="URL path for this product, e.g. /products/prady-microfinance. Generated automatically from the name; edit only if you need a custom URL."
        >
          <div className="admin-slug-row">
            <input
              value={form.slug}
              onChange={(e) => onSlugChange(e.target.value)}
              disabled={!canManage}
              placeholder="generated-from-name"
            />
            {canManage && !slugLocked ? (
              <button
                type="button"
                className="admin-linkish"
                onClick={() => {
                  setSlugLocked(true);
                  set("slug", slugify(form.name));
                }}
              >
                Re-sync
              </button>
            ) : null}
          </div>
        </Field>
        <Field
          label="Tagline"
          help="One short line under the product name on the detail page hero."
        >
          <input value={form.tagline} onChange={(e) => set("tagline", e.target.value)} disabled={!canManage} />
        </Field>
        <Field
          label="Short description"
          help="Brief card copy used on the homepage and products listing (keep to one or two sentences)."
        >
          <input value={form.short} onChange={(e) => set("short", e.target.value)} disabled={!canManage} />
        </Field>
        <Field
          label="Full description / overview"
          help="Longer overview shown on the product detail page. Supports multiple paragraphs."
        >
          <textarea rows={6} value={form.description} onChange={(e) => set("description", e.target.value)} disabled={!canManage} />
        </Field>
        <Field
          label="Market"
          help="Who this product is for (e.g. MFIs, landlords). Shown as supporting context on cards and detail."
        >
          <input value={form.market} onChange={(e) => set("market", e.target.value)} disabled={!canManage} />
        </Field>

        <div className="admin-form__row">
          <Field
            label="Icon key"
            help="Marketing icon name used next to the product (e.g. finance, users, car). Must match a known Prady icon key."
          >
            <input value={form.icon} onChange={(e) => set("icon", e.target.value)} disabled={!canManage} />
          </Field>
          <Field
            label="Group"
            help="Portfolio group for the products page: finance, assets, or commerce."
          >
            <select value={form.group_key} onChange={(e) => set("group_key", e.target.value)} disabled={!canManage}>
              <option value="">—</option>
              <option value="finance">finance</option>
              <option value="assets">assets</option>
              <option value="commerce">commerce</option>
            </select>
          </Field>
          <Field
            label="Display order"
            help="Sort priority on listings. Lower numbers appear first."
          >
            <input type="number" value={form.order} onChange={(e) => set("order", Number(e.target.value))} disabled={!canManage} />
          </Field>
        </div>

        <h3>Calls to action</h3>
        <div className="admin-form__row">
          <Field
            label="CTA label"
            help="Primary button text on the product page (e.g. Request demo)."
          >
            <input value={form.cta_label} onChange={(e) => set("cta_label", e.target.value)} disabled={!canManage} />
          </Field>
          <Field
            label="CTA type"
            help="demo or contact open the site form pre-filled for this product. external opens the CTA URL."
          >
            <select value={form.cta_type} onChange={(e) => set("cta_type", e.target.value)} disabled={!canManage}>
              <option value="demo">demo</option>
              <option value="contact">contact</option>
              <option value="external">external</option>
            </select>
          </Field>
          <Field
            label="CTA URL"
            help="Optional destination when CTA type is external (full URL or internal path)."
          >
            <input value={form.url} onChange={(e) => set("url", e.target.value)} disabled={!canManage} />
          </Field>
        </div>
        <div className="admin-form__row">
          <Field
            label="Secondary CTA"
            help="Optional second button label (e.g. Talk to Our Team). Leave blank to hide."
          >
            <input
              value={form.secondary_cta_label}
              onChange={(e) => set("secondary_cta_label", e.target.value)}
              disabled={!canManage}
              placeholder="Talk to Our Team"
            />
          </Field>
          <Field
            label="Secondary type"
            help="Where the secondary button goes: contact form, demo form, or an external URL."
          >
            <select value={form.secondary_cta_type} onChange={(e) => set("secondary_cta_type", e.target.value)} disabled={!canManage}>
              <option value="contact">contact</option>
              <option value="demo">demo</option>
              <option value="external">external</option>
            </select>
          </Field>
          <Field
            label="Secondary URL"
            help="Destination when secondary type is external."
          >
            <input value={form.secondary_cta_url} onChange={(e) => set("secondary_cta_url", e.target.value)} disabled={!canManage} />
          </Field>
        </div>

        <h3>SEO</h3>
        <Field
          label="SEO title"
          help="Browser tab and search title. Defaults to “Product name | Prady Technologies” if empty."
        >
          <input value={form.seo_title} onChange={(e) => set("seo_title", e.target.value)} disabled={!canManage} />
        </Field>
        <Field
          label="SEO description"
          help="Meta description for search results. Keep under ~160 characters."
        >
          <textarea rows={2} value={form.seo_description} onChange={(e) => set("seo_description", e.target.value)} disabled={!canManage} />
        </Field>

        <div className="admin-form__checks">
          <CheckField
            label="Active"
            help="When off, the product is hidden from the public website but kept in admin."
          >
            <input
              type="checkbox"
              checked={!!form.is_active}
              onChange={(e) => set("is_active", e.target.checked)}
              disabled={!canManage}
            />
          </CheckField>
          <CheckField
            label="Featured"
            help="Featured products are prioritised on the homepage and marketing surfaces."
          >
            <input
              type="checkbox"
              checked={!!form.is_featured}
              onChange={(e) => set("is_featured", e.target.checked)}
              disabled={!canManage}
            />
          </CheckField>
        </div>

        <h3>Cover images</h3>
        <p className="admin-help">JPEG, PNG, WebP, or GIF — max 5 MB each.</p>
        <div className="admin-media-uploads">
          <Field
            label="Poster (cards & listing)"
            help="Main product image on homepage and products grid cards. Prefer a clear 4:3 visual."
          >
            <input
              type="file"
              accept="image/jpeg,image/png,image/webp,image/gif,.gif,.jpg,.jpeg,.png,.webp"
              onChange={(e) => setPosterFile(e.target.files?.[0] || null)}
              disabled={!canManage}
            />
            {form.poster_url ? <img src={form.poster_url} alt="" className="admin-poster-preview" /> : null}
          </Field>
          <Field
            label="Hero image (detail page)"
            help="Optional wide image for the product detail hero. Falls back to the poster if empty."
          >
            <input
              type="file"
              accept="image/jpeg,image/png,image/webp,image/gif,.gif,.jpg,.jpeg,.png,.webp"
              onChange={(e) => setHeroFile(e.target.files?.[0] || null)}
              disabled={!canManage}
            />
            {form.hero_image_url ? <img src={form.hero_image_url} alt="" className="admin-poster-preview" /> : null}
          </Field>
          <Field
            label="Mobile image"
            help="Optional image shown on small screens instead of the hero/poster."
          >
            <input
              type="file"
              accept="image/jpeg,image/png,image/webp,image/gif,.gif,.jpg,.jpeg,.png,.webp"
              onChange={(e) => setMobileFile(e.target.files?.[0] || null)}
              disabled={!canManage}
            />
            {form.mobile_image_url ? <img src={form.mobile_image_url} alt="" className="admin-poster-preview" /> : null}
          </Field>
        </div>

        {canManage && (
          <button type="submit" className="admin-btn admin-btn--primary" disabled={saving}>
            {saving ? "Saving…" : isNew ? "Create product" : "Save product"}
          </button>
        )}
      </form>

      {!isNew && productId ? (
        <section className="admin-media-panel">
          <h3>Gallery media</h3>
          <p className="admin-help">
            Images and GIFs, or MP4/WebM videos (upload or external URL). Featured item is the primary viewer on the
            public product page.
          </p>

          <div className="admin-media-grid">
            {mediaItems.map((item) => {
              const preview = mediaPreview(item);
              return (
                <article key={item.id} className="admin-media-card">
                  <div className="admin-media-card__preview">
                    {item.media_type === "video" && item.video_file_url ? (
                      <video src={item.video_file_url} muted playsInline preload="metadata" />
                    ) : preview ? (
                      <img src={preview} alt={item.alt_text || item.title || ""} />
                    ) : (
                      <span className="admin-media-card__empty">{item.media_type}</span>
                    )}
                  </div>
                  <div className="admin-media-card__meta">
                    <strong>{item.title || item.alt_text || `#${item.id}`}</strong>
                    <span>
                      {item.media_type}
                      {item.is_featured ? " · featured" : ""}
                      {!item.is_active ? " · inactive" : ""}
                    </span>
                  </div>
                  {canManage && (
                    <div className="admin-media-card__actions">
                      <button type="button" className="admin-linkish" onClick={() => onToggleFeatured(item)}>
                        {item.is_featured ? "Unfeature" : "Feature"}
                      </button>
                      <button type="button" className="admin-linkish text-red-700" onClick={() => onDeleteMedia(item)}>
                        Delete
                      </button>
                    </div>
                  )}
                </article>
              );
            })}
            {!mediaItems.length && <p className="text-[var(--admin-muted)]">No gallery media yet.</p>}
          </div>

          {canManage && (
            <form className="admin-form admin-form--wide" onSubmit={onAddMedia}>
              <h4>Add media</h4>
              <div className="admin-form__row">
                <Field
                  label="Type"
                  help="Image/GIF for stills and animated GIFs. Video for MP4/WebM uploads or an external link."
                >
                  <select value={mediaForm.media_type} onChange={(e) => setMedia("media_type", e.target.value)}>
                    <option value="image">Image / GIF</option>
                    <option value="video">Video</option>
                  </select>
                </Field>
                <Field
                  label="Title"
                  help="Short label shown with the media item in the gallery."
                >
                  <input value={mediaForm.title} onChange={(e) => setMedia("title", e.target.value)} />
                </Field>
                <Field
                  label="Display order"
                  help="Gallery sort order. Lower numbers appear first."
                >
                  <input
                    type="number"
                    value={mediaForm.display_order}
                    onChange={(e) => setMedia("display_order", Number(e.target.value))}
                  />
                </Field>
              </div>
              <Field
                label="Caption"
                help="Optional text shown under the selected gallery item on the public page."
              >
                <textarea rows={2} value={mediaForm.caption} onChange={(e) => setMedia("caption", e.target.value)} />
              </Field>
              <Field
                label="Alt text"
                help="Accessibility description for screen readers and SEO. Required for meaningful images."
              >
                <input value={mediaForm.alt_text} onChange={(e) => setMedia("alt_text", e.target.value)} />
              </Field>

              {mediaForm.media_type === "image" ? (
                <>
                  <Field
                    label="Category"
                    help="Optional label for organising screenshots (dashboard, mobile, workflow, etc.)."
                  >
                    <select value={mediaForm.image_category} onChange={(e) => setMedia("image_category", e.target.value)}>
                      <option value="screenshot">screenshot</option>
                      <option value="mobile">mobile</option>
                      <option value="dashboard">dashboard</option>
                      <option value="poster">poster</option>
                      <option value="feature">feature</option>
                      <option value="workflow">workflow</option>
                      <option value="other">other</option>
                    </select>
                  </Field>
                  <Field
                    label="Image / GIF file"
                    help="JPEG, PNG, WebP, or GIF up to 5 MB. GIFs play as animated images in the gallery."
                  >
                    <input
                      type="file"
                      accept="image/jpeg,image/png,image/webp,image/gif,.gif,.jpg,.jpeg,.png,.webp"
                      required
                      onChange={(e) => setMediaImage(e.target.files?.[0] || null)}
                    />
                  </Field>
                </>
              ) : (
                <>
                  <Field
                    label="Video source"
                    help="Upload a file to host on this site, or link to an external video URL (YouTube/Vimeo preferred)."
                  >
                    <select value={mediaForm.video_source} onChange={(e) => setMedia("video_source", e.target.value)}>
                      <option value="upload">Uploaded file</option>
                      <option value="external">External URL</option>
                    </select>
                  </Field>
                  {mediaForm.video_source === "upload" ? (
                    <Field
                      label="Video file"
                      help="MP4 or WebM file, typically up to ~80 MB depending on server limits."
                    >
                      <input
                        type="file"
                        accept="video/mp4,video/webm,.mp4,.webm"
                        required
                        onChange={(e) => setMediaVideo(e.target.files?.[0] || null)}
                      />
                    </Field>
                  ) : (
                    <Field
                      label="Video URL"
                      help="Full HTTPS link visitors can open. Shown with an optional thumbnail on the product page."
                    >
                      <input
                        type="url"
                        required
                        value={mediaForm.video_url}
                        onChange={(e) => setMedia("video_url", e.target.value)}
                        placeholder="https://"
                      />
                    </Field>
                  )}
                  <Field
                    label="Thumbnail (optional)"
                    help="Poster frame shown before video playback or for external video cards."
                  >
                    <input
                      type="file"
                      accept="image/jpeg,image/png,image/webp,image/gif,.gif,.jpg,.jpeg,.png,.webp"
                      onChange={(e) => setMediaThumb(e.target.files?.[0] || null)}
                    />
                  </Field>
                </>
              )}

              <div className="admin-form__checks">
                <CheckField
                  label="Featured"
                  help="Featured media is the primary gallery viewer. Only one featured item per product."
                >
                  <input
                    type="checkbox"
                    checked={!!mediaForm.is_featured}
                    onChange={(e) => setMedia("is_featured", e.target.checked)}
                  />
                </CheckField>
                <CheckField
                  label="Active"
                  help="Inactive media stays in admin but is hidden from the public gallery."
                >
                  <input
                    type="checkbox"
                    checked={!!mediaForm.is_active}
                    onChange={(e) => setMedia("is_active", e.target.checked)}
                  />
                </CheckField>
              </div>

              <button type="submit" className="admin-btn admin-btn--primary" disabled={mediaSaving}>
                {mediaSaving ? "Uploading…" : "Add media"}
              </button>
            </form>
          )}

          <p className="admin-help">
            Highlights, audiences, capabilities, FAQs and section order can also be edited in Django admin for richer
            page structure.
          </p>
        </section>
      ) : (
        <p className="admin-help p-6">Save the product first to attach gallery images, GIFs, or videos.</p>
      )}
    </div>
  );
}
