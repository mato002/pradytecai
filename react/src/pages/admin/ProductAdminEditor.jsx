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

function CheckCard({ label, description, help, checked, onChange, disabled }) {
  return (
    <label className={`admin-check-card ${checked ? "is-checked" : ""}`}>
      <input
        type="checkbox"
        checked={checked}
        onChange={onChange}
        disabled={disabled}
      />
      <div className="admin-check-card__text">
        <span className="admin-check-card__title">
          {label}
          <FieldHelp text={help} />
        </span>
        {description && <span className="admin-check-card__desc">{description}</span>}
      </div>
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
        setNotice("Product created successfully.");
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
      setNotice("Changes saved successfully.");
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
      setNotice("Media added to gallery.");
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
      <div className="admin-editor-wrap">
        <div className="admin-card-section text-center p-12">
          <p className="text-[var(--admin-muted)] text-base">Loading product details…</p>
        </div>
      </div>
    );
  }

  return (
    <div className="admin-editor-wrap">
      {/* Header bar */}
      <header className="admin-editor-header">
        <div className="admin-editor-header__main">
          <Link to="/admin/products" className="admin-linkish text-sm">
            ← Back to Products list
          </Link>
          <h1 className="admin-editor-header__title">
            <span>{isNew ? "Create Product" : form.name || "Edit Product"}</span>
            {!isNew && (
              <span
                className={`admin-status ${
                  form.is_active ? "admin-status--ok" : "admin-status--warn"
                }`}
              >
                {form.is_active ? "Active" : "Draft / Inactive"}
              </span>
            )}
          </h1>
        </div>
        <div className="flex items-center gap-3">
          {!isNew && form.slug && (
            <Link to={`/products/${form.slug}`} className="admin-btn" target="_blank" rel="noreferrer">
              <span>View Public Page ↗</span>
            </Link>
          )}
        </div>
      </header>

      {error && (
        <div className="admin-banner admin-banner--error" role="alert">
          <span>⚠️ {error}</span>
        </div>
      )}
      {notice && (
        <div className="admin-banner admin-banner--ok" role="status">
          <span>✓ {notice}</span>
        </div>
      )}

      <form id="product-admin-form" className="admin-form" onSubmit={onSave}>
        {/* Section 1: Basic Information */}
        <section className="admin-card-section">
          <div className="admin-card-section__head">
            <div>
              <h2 className="admin-card-section__title">
                <span className="admin-card-section__badge">1</span>
                Basic Details
              </h2>
              <p className="admin-card-section__subtitle">
                Core identity, pricing tier, tagline, and customer description.
              </p>
            </div>
          </div>

          <div className="admin-form__row">
            <Field label="Product Name" help="Public name displayed across cards, navigation, and landing pages.">
              <input
                required
                type="text"
                placeholder="e.g. Prady Microfinance"
                value={form.name}
                onChange={(e) => onNameChange(e.target.value)}
                disabled={!canManage}
              />
            </Field>

            <Field label="URL Slug" help="URL path (e.g. /products/prady-microfinance). Auto-generated from name.">
              <div className="admin-slug-row">
                <input
                  type="text"
                  value={form.slug}
                  onChange={(e) => onSlugChange(e.target.value)}
                  disabled={!canManage}
                  placeholder="prady-microfinance"
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
                    Auto-sync
                  </button>
                ) : null}
              </div>
            </Field>
          </div>

          <Field label="Tagline" help="Short punchy tagline shown under the hero header.">
            <input
              type="text"
              placeholder="e.g. Next-generation core banking for modern MFIs"
              value={form.tagline}
              onChange={(e) => set("tagline", e.target.value)}
              disabled={!canManage}
            />
          </Field>

          <Field label="Short Description" help="Brief summary used on homepage cards and search results.">
            <input
              type="text"
              placeholder="e.g. Complete loan management and savings automation."
              value={form.short}
              onChange={(e) => set("short", e.target.value)}
              disabled={!canManage}
            />
          </Field>

          <Field label="Full Overview & Features" help="Detailed overview for the main product detail page.">
            <textarea
              rows={5}
              placeholder="Describe product capabilities, benefits, and key features..."
              value={form.description}
              onChange={(e) => set("description", e.target.value)}
              disabled={!canManage}
            />
          </Field>
        </section>

        {/* Section 2: Positioning & Categorization */}
        <section className="admin-card-section">
          <div className="admin-card-section__head">
            <div>
              <h2 className="admin-card-section__title">
                <span className="admin-card-section__badge">2</span>
                Categorization & Priority
              </h2>
              <p className="admin-card-section__subtitle">
                Target audience, group category, icon, and display ordering.
              </p>
            </div>
          </div>

          <div className="admin-form__row">
            <Field label="Target Market / Audience" help="Target user base (e.g. Microfinance institutions, SACCOs, Enterprise).">
              <input
                type="text"
                placeholder="e.g. MFIs & Credit Unions"
                value={form.market}
                onChange={(e) => set("market", e.target.value)}
                disabled={!canManage}
              />
            </Field>

            <Field label="Icon Key" help="Marketing icon identifier (e.g. finance, users, building, shield).">
              <input
                type="text"
                placeholder="e.g. finance"
                value={form.icon}
                onChange={(e) => set("icon", e.target.value)}
                disabled={!canManage}
              />
            </Field>

            <Field label="Portfolio Group" help="Group category on the public products listing page.">
              <select value={form.group_key} onChange={(e) => set("group_key", e.target.value)} disabled={!canManage}>
                <option value="">— Select Category Group —</option>
                <option value="finance">finance</option>
                <option value="assets">assets</option>
                <option value="commerce">commerce</option>
              </select>
            </Field>

            <Field label="Display Order Priority" help="Sorting order. Lower numbers appear first on listings.">
              <input
                type="number"
                value={form.order}
                onChange={(e) => set("order", Number(e.target.value))}
                disabled={!canManage}
              />
            </Field>
          </div>
        </section>

        {/* Section 3: Actions & Conversion Buttons */}
        <section className="admin-card-section">
          <div className="admin-card-section__head">
            <div>
              <h2 className="admin-card-section__title">
                <span className="admin-card-section__badge">3</span>
                Call-to-Action Buttons
              </h2>
              <p className="admin-card-section__subtitle">
                Configure primary and secondary CTA buttons shown on product landing pages.
              </p>
            </div>
          </div>

          <div className="admin-form__row">
            <Field label="Primary CTA Label" help="Primary call to action button text.">
              <input
                type="text"
                placeholder="e.g. Request Demo"
                value={form.cta_label}
                onChange={(e) => set("cta_label", e.target.value)}
                disabled={!canManage}
              />
            </Field>

            <Field label="Primary CTA Action Type" help="Determines button action: open demo form, contact form, or external URL.">
              <select value={form.cta_type} onChange={(e) => set("cta_type", e.target.value)} disabled={!canManage}>
                <option value="demo">Demo modal form</option>
                <option value="contact">Contact modal form</option>
                <option value="external">External link / URL</option>
              </select>
            </Field>

            <Field label="Primary CTA Destination URL" help="Required if CTA Action Type is external.">
              <input
                type="text"
                placeholder="https://app.pradytec.com/signup"
                value={form.url}
                onChange={(e) => set("url", e.target.value)}
                disabled={!canManage}
              />
            </Field>
          </div>

          <div className="admin-form__row">
            <Field label="Secondary CTA Label" help="Optional secondary button text (leave blank to hide).">
              <input
                type="text"
                placeholder="e.g. Talk to Sales"
                value={form.secondary_cta_label}
                onChange={(e) => set("secondary_cta_label", e.target.value)}
                disabled={!canManage}
              />
            </Field>

            <Field label="Secondary Action Type" help="Destination behavior for the secondary button.">
              <select value={form.secondary_cta_type} onChange={(e) => set("secondary_cta_type", e.target.value)} disabled={!canManage}>
                <option value="contact">Contact modal form</option>
                <option value="demo">Demo modal form</option>
                <option value="external">External link / URL</option>
              </select>
            </Field>

            <Field label="Secondary Destination URL" help="URL if secondary type is set to external.">
              <input
                type="text"
                placeholder="https://..."
                value={form.secondary_cta_url}
                onChange={(e) => set("secondary_cta_url", e.target.value)}
                disabled={!canManage}
              />
            </Field>
          </div>
        </section>

        {/* Section 4: Visibility & Featuring Controls */}
        <section className="admin-card-section">
          <div className="admin-card-section__head">
            <div>
              <h2 className="admin-card-section__title">
                <span className="admin-card-section__badge">4</span>
                Publishing Status
              </h2>
              <p className="admin-card-section__subtitle">
                Manage active site publication and homepage featuring.
              </p>
            </div>
          </div>

          <div className="admin-check-card-group">
            <CheckCard
              label="Active & Published"
              description="Visible to website visitors on listings and navigation menus."
              help="When disabled, the product is saved as a draft and hidden from the public site."
              checked={!!form.is_active}
              onChange={(e) => set("is_active", e.target.checked)}
              disabled={!canManage}
            />
            <CheckCard
              label="Featured Product"
              description="Highlighted prominently on the homepage and top marketing cards."
              help="Featured items appear first with highlighted badges."
              checked={!!form.is_featured}
              onChange={(e) => set("is_featured", e.target.checked)}
              disabled={!canManage}
            />
          </div>
        </section>

        {/* Section 5: SEO Configuration */}
        <section className="admin-card-section">
          <div className="admin-card-section__head">
            <div>
              <h2 className="admin-card-section__title">
                <span className="admin-card-section__badge">5</span>
                SEO & Meta Info
              </h2>
              <p className="admin-card-section__subtitle">
                Optimize search engine listings and page titles.
              </p>
            </div>
          </div>

          <Field label="SEO Title Tag" help="Title shown in browser tabs and Google search snippet.">
            <input
              type="text"
              placeholder="e.g. Prady Microfinance — Automated Core Banking Solution"
              value={form.seo_title}
              onChange={(e) => set("seo_title", e.target.value)}
              disabled={!canManage}
            />
          </Field>

          <Field label="Meta Description" help="Search engine preview text (150–160 characters recommended).">
            <textarea
              rows={2}
              placeholder="e.g. Streamline loan disbursement, member accounts, and mobile banking with Prady Microfinance..."
              value={form.seo_description}
              onChange={(e) => set("seo_description", e.target.value)}
              disabled={!canManage}
            />
          </Field>
        </section>

        {/* Section 6: Cover Images */}
        <section className="admin-card-section">
          <div className="admin-card-section__head">
            <div>
              <h2 className="admin-card-section__title">
                <span className="admin-card-section__badge">6</span>
                Cover Imagery
              </h2>
              <p className="admin-card-section__subtitle">
                Main card poster, detail hero graphic, and mobile display artwork (JPEG, PNG, WebP, GIF up to 5MB).
              </p>
            </div>
          </div>

          <div className="admin-media-uploads">
            <div className="admin-upload-card">
              <Field label="Card Poster Image" help="4:3 visual used on homepage and portfolio grid cards.">
                <input
                  type="file"
                  accept="image/jpeg,image/png,image/webp,image/gif,.gif,.jpg,.jpeg,.png,.webp"
                  onChange={(e) => setPosterFile(e.target.files?.[0] || null)}
                  disabled={!canManage}
                />
              </Field>
              {form.poster_url && (
                <div>
                  <span className="text-xs text-[var(--admin-muted)] font-medium">Current Poster:</span>
                  <img src={form.poster_url} alt="Poster preview" className="admin-poster-preview" />
                </div>
              )}
            </div>

            <div className="admin-upload-card">
              <Field label="Desktop Hero Graphic" help="Wide header artwork for the product detail hero section.">
                <input
                  type="file"
                  accept="image/jpeg,image/png,image/webp,image/gif,.gif,.jpg,.jpeg,.png,.webp"
                  onChange={(e) => setHeroFile(e.target.files?.[0] || null)}
                  disabled={!canManage}
                />
              </Field>
              {form.hero_image_url && (
                <div>
                  <span className="text-xs text-[var(--admin-muted)] font-medium">Current Hero:</span>
                  <img src={form.hero_image_url} alt="Hero preview" className="admin-poster-preview" />
                </div>
              )}
            </div>

            <div className="admin-upload-card">
              <Field label="Mobile Visual (Optional)" help="Optimized mobile image shown on narrow smartphone screens.">
                <input
                  type="file"
                  accept="image/jpeg,image/png,image/webp,image/gif,.gif,.jpg,.jpeg,.png,.webp"
                  onChange={(e) => setMobileFile(e.target.files?.[0] || null)}
                  disabled={!canManage}
                />
              </Field>
              {form.mobile_image_url && (
                <div>
                  <span className="text-xs text-[var(--admin-muted)] font-medium">Current Mobile Visual:</span>
                  <img src={form.mobile_image_url} alt="Mobile visual preview" className="admin-poster-preview" />
                </div>
              )}
            </div>
          </div>
        </section>

        {/* Sticky Toolbar Bar */}
        <div className="admin-sticky-toolbar">
          <div className="flex items-center gap-3">
            <Link to="/admin/products" className="admin-btn">
              Cancel
            </Link>
            <span className="text-xs text-[var(--admin-muted)] hidden sm:inline">
              {isNew ? "Fill in required fields and save." : "Unsaved edits apply on save."}
            </span>
          </div>

          {canManage && (
            <button type="submit" className="admin-btn admin-btn--primary" disabled={saving}>
              {saving ? "Saving product…" : isNew ? "Create Product" : "Save Changes"}
            </button>
          )}
        </div>
      </form>

      {/* Section 7: Gallery Media Manager */}
      {!isNew && productId ? (
        <section className="admin-card-section admin-media-panel">
          <div className="admin-card-section__head">
            <div>
              <h2 className="admin-card-section__title">
                <span className="admin-card-section__badge">7</span>
                Product Gallery Media ({mediaItems.length})
              </h2>
              <p className="admin-card-section__subtitle">
                Screenshots, product demo videos (MP4/WebM), or GIFs. Mark one item as featured to set the main viewer.
              </p>
            </div>
          </div>

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
                    <strong>{item.title || item.alt_text || `Media #${item.id}`}</strong>
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
            {!mediaItems.length && (
              <p className="text-[var(--admin-muted)] text-sm py-4 col-span-full">
                No gallery items added yet. Use the form below to upload media.
              </p>
            )}
          </div>

          {canManage && (
            <form className="admin-upload-card mt-4" onSubmit={onAddMedia}>
              <h3 className="text-sm font-bold text-[var(--admin-forest)]">Add Gallery Media</h3>
              <div className="admin-form__row">
                <Field label="Media Type" help="Image/GIF or Video file / external URL.">
                  <select value={mediaForm.media_type} onChange={(e) => setMedia("media_type", e.target.value)}>
                    <option value="image">Image / GIF</option>
                    <option value="video">Video</option>
                  </select>
                </Field>

                <Field label="Media Title" help="Short label for gallery modal.">
                  <input
                    type="text"
                    placeholder="e.g. Dashboard Overview"
                    value={mediaForm.title}
                    onChange={(e) => setMedia("title", e.target.value)}
                  />
                </Field>

                <Field label="Sort Order" help="Lower order numbers display first.">
                  <input
                    type="number"
                    value={mediaForm.display_order}
                    onChange={(e) => setMedia("display_order", Number(e.target.value))}
                  />
                </Field>
              </div>

              <div className="admin-form__row">
                <Field label="Caption" help="Optional descriptive text displayed under media viewer.">
                  <input
                    type="text"
                    placeholder="e.g. Real-time member transaction ledger screen"
                    value={mediaForm.caption}
                    onChange={(e) => setMedia("caption", e.target.value)}
                  />
                </Field>

                <Field label="Alt Text" help="Accessibility text for screen readers and SEO.">
                  <input
                    type="text"
                    placeholder="e.g. Microfinance dashboard interface screenshot"
                    value={mediaForm.alt_text}
                    onChange={(e) => setMedia("alt_text", e.target.value)}
                  />
                </Field>
              </div>

              {mediaForm.media_type === "image" ? (
                <div className="admin-form__row">
                  <Field label="Image Category" help="Category tag for organizing screenshots.">
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

                  <Field label="Upload Image / GIF File" help="JPEG, PNG, WebP, or GIF file.">
                    <input
                      type="file"
                      accept="image/jpeg,image/png,image/webp,image/gif,.gif,.jpg,.jpeg,.png,.webp"
                      required
                      onChange={(e) => setMediaImage(e.target.files?.[0] || null)}
                    />
                  </Field>
                </div>
              ) : (
                <>
                  <div className="admin-form__row">
                    <Field label="Video Source Type" help="Upload MP4 file or link external URL.">
                      <select value={mediaForm.video_source} onChange={(e) => setMedia("video_source", e.target.value)}>
                        <option value="upload">Uploaded MP4/WebM file</option>
                        <option value="external">External Video Link (YouTube/Vimeo)</option>
                      </select>
                    </Field>

                    {mediaForm.video_source === "upload" ? (
                      <Field label="Video File" help="MP4 or WebM file format.">
                        <input
                          type="file"
                          accept="video/mp4,video/webm,.mp4,.webm"
                          required
                          onChange={(e) => setMediaVideo(e.target.files?.[0] || null)}
                        />
                      </Field>
                    ) : (
                      <Field label="External Video Link" help="Full HTTPS video link.">
                        <input
                          type="url"
                          required
                          value={mediaForm.video_url}
                          onChange={(e) => setMedia("video_url", e.target.value)}
                          placeholder="https://youtube.com/watch?v=..."
                        />
                      </Field>
                    )}
                  </div>

                  <Field label="Video Thumbnail (Optional)" help="Custom poster frame image.">
                    <input
                      type="file"
                      accept="image/jpeg,image/png,image/webp,image/gif,.gif,.jpg,.jpeg,.png,.webp"
                      onChange={(e) => setMediaThumb(e.target.files?.[0] || null)}
                    />
                  </Field>
                </>
              )}

              <div className="admin-check-card-group my-2">
                <CheckCard
                  label="Set as Featured Gallery Item"
                  description="Initial active slide on the public product viewer."
                  checked={!!mediaForm.is_featured}
                  onChange={(e) => setMedia("is_featured", e.target.checked)}
                />
                <CheckCard
                  label="Active & Visible"
                  description="Visible on public product page media viewer."
                  checked={!!mediaForm.is_active}
                  onChange={(e) => setMedia("is_active", e.target.checked)}
                />
              </div>

              <div>
                <button type="submit" className="admin-btn admin-btn--primary" disabled={mediaSaving}>
                  {mediaSaving ? "Uploading media…" : "Upload Media Item"}
                </button>
              </div>
            </form>
          )}
        </section>
      ) : (
        <div className="admin-card-section text-center p-6">
          <p className="admin-help text-base">Save product basic details first to attach gallery screenshots and media.</p>
        </div>
      )}
    </div>
  );
}

