/** Pure helpers for the database-driven product detail page. */

export const DEFAULT_SECTION_ORDER = [
  "highlights",
  "audience",
  "problems",
  "capabilities",
  "workflow",
  "media",
  "integrations",
  "controls",
  "outcomes",
  "implementation",
  "faq",
  "custom",
];

export const DEFAULT_SECTION_TITLES = {
  highlights: "",
  audience: "Who it is for",
  problems: "Problems solved",
  capabilities: "Core capabilities",
  workflow: "How it works",
  media: "See the product",
  integrations: "Integrations",
  controls: "Security and controls",
  outcomes: "Outcomes",
  implementation: "Getting started",
  faq: "FAQ",
  custom: "",
};

export const SECTION_LABELS = {
  highlights: "Highlights",
  audience: "Who it is for",
  problems: "Problems solved",
  capabilities: "Core capabilities",
  workflow: "How it works",
  media: "See the product",
  integrations: "Integrations",
  controls: "Security and controls",
  outcomes: "Outcomes",
  implementation: "Getting started",
  faq: "FAQ",
  custom: "More",
};

export function text(value) {
  return typeof value === "string" ? value.trim() : "";
}

function rows(list) {
  return Array.isArray(list) ? list : [];
}

/** True when the API media row can be shown in the gallery. */
export function mediaIsRenderable(item) {
  if (!item) return false;
  if (text(item.media_type) === "video") {
    return Boolean(text(item.video_file_url) || text(item.video_url));
  }
  return Boolean(text(item.image_url));
}

export function visibleMedia(product) {
  return rows(product?.media).filter(mediaIsRenderable);
}

/** Featured row if active; otherwise first by API order (display_order). */
export function pickFeaturedMedia(items) {
  const list = Array.isArray(items) ? items.filter(mediaIsRenderable) : [];
  if (!list.length) return null;
  return list.find((item) => item.is_featured) || list[0];
}

export function mediaThumbSrc(item) {
  if (!item) return "";
  if (text(item.media_type) === "video") {
    return text(item.thumbnail_url) || text(item.image_url) || "";
  }
  return text(item.image_url) || text(item.thumbnail_url) || "";
}

export function hasSectionContent(product, type) {
  if (!product) return false;
  switch (type) {
    case "highlights":
      return rows(product.highlights).some((item) => text(item?.title));
    case "audience":
      return rows(product.audiences).some((item) => text(item?.title));
    case "problems":
      return rows(product.problems).some((item) => text(item?.title));
    case "capabilities":
      return rows(product.capability_groups).some((group) => text(group?.title));
    case "workflow":
      return rows(product.workflow_steps).some((item) => text(item?.title));
    case "media":
      return visibleMedia(product).length > 0;
    case "integrations":
      return rows(product.integrations).some((item) => text(item?.name));
    case "controls":
      return rows(product.controls).some((item) => text(item?.title));
    case "outcomes":
      return rows(product.outcomes).some((item) => text(item?.title));
    case "implementation":
      return rows(product.implementation_steps).some((item) => text(item?.title));
    case "faq":
      return rows(product.faqs).some((item) => text(item?.question) && text(item?.answer));
    case "custom":
      return rows(product.custom_sections).some((item) => text(item?.title) || text(item?.body));
    default:
      return false;
  }
}

function sectionRecord(section, fallbackKey) {
  return {
    type: section.type || section.section_type,
    title: text(section.title_override || section.title),
    subtitle: text(section.subtitle),
    key: fallbackKey,
  };
}

function insertByDefaultOrder(placed, section) {
  const index = DEFAULT_SECTION_ORDER.indexOf(section.type);
  let at = placed.length;
  if (index !== -1) {
    for (let i = 0; i < placed.length; i += 1) {
      const other = DEFAULT_SECTION_ORDER.indexOf(placed[i].type);
      if (other > index) {
        at = i;
        break;
      }
    }
  }
  placed.splice(at, 0, section);
}

/**
 * Enabled page sections set the order. Disabled sections stay hidden.
 * A type with data and no section row is inserted in the default order.
 * Empty sections are omitted. No page sections at all uses the default order.
 */
export function resolveSections(product) {
  const configured = rows(product?.page_sections)
    .filter((section) => section && text(section.section_type))
    .slice()
    .sort((a, b) => (a.display_order ?? 0) - (b.display_order ?? 0) || (a.id ?? 0) - (b.id ?? 0));

  if (!configured.length) {
    return DEFAULT_SECTION_ORDER.filter((type) => hasSectionContent(product, type)).map((type) => ({
      type,
      title: "",
      subtitle: "",
      key: type,
    }));
  }

  const known = new Set(configured.map((section) => section.section_type));
  const placed = [];
  configured.forEach((section) => {
    if (section.is_enabled === false) return;
    if (!hasSectionContent(product, section.section_type)) return;
    if (placed.some((item) => item.type === section.section_type)) return;
    placed.push(
      sectionRecord(section, `section-${section.id || section.section_type}`)
    );
  });

  DEFAULT_SECTION_ORDER.forEach((type) => {
    if (known.has(type) || !hasSectionContent(product, type)) return;
    insertByDefaultOrder(placed, { type, title: "", subtitle: "", key: type });
  });

  return placed;
}

export function sectionHeading(section) {
  if (!section) return "";
  return text(section.title) || DEFAULT_SECTION_TITLES[section.type] || "";
}

export function productCta(product, which = "primary") {
  if (!product) return null;
  const secondary = which === "secondary";
  const label = secondary ? text(product.secondary_cta_label) : text(product.cta_label) || "Request demo";
  if (secondary && !label) return null;
  const type = (secondary ? product.secondary_cta_type : product.cta_type) || (secondary ? "contact" : "demo");
  const url = text(secondary ? product.secondary_cta_url : product.cta_url || product.url);
  if (type === "external" && url) {
    return { label, external: true, href: url };
  }
  const requestType = type === "contact" ? "contact" : "demo";
  const params = new URLSearchParams();
  if (text(product.name)) params.set("product", product.name);
  if (text(product.slug)) params.set("product_slug", product.slug);
  params.set("request_type", requestType);
  return { label, external: false, to: `/contact?${params.toString()}` };
}

/** Known static posters in /images when DB media is not attached yet. */
const STATIC_POSTER_BY_SLUG = {
  "prady-microfinance": "/images/mfi.jpg",
};

export function heroSources(product) {
  const slug = text(product?.slug);
  const fallback = STATIC_POSTER_BY_SLUG[slug] || "";
  const desktop = text(product?.hero_image_url) || text(product?.poster_url) || fallback;
  const mobile = text(product?.mobile_image_url);
  return { desktop, mobile };
}

export function showOverview(product) {
  const overview = text(product?.overview || product?.description);
  const short = text(product?.short_description || product?.short);
  const tagline = text(product?.tagline);
  if (!overview || overview === short || overview === tagline) return "";
  if (short && overview.length < short.length + 24) return "";
  return overview;
}

export function applyProductSeo(product) {
  if (!product || typeof document === "undefined") return;
  const name = text(product.name) || "Product";
  document.title = text(product.seo_title) || `${name} | Prady Technologies`;
  const description = text(product.seo_description) || text(product.short_description) || text(product.short);
  let meta = document.querySelector('meta[name="description"]');
  if (!meta) {
    meta = document.createElement("meta");
    meta.setAttribute("name", "description");
    document.head.appendChild(meta);
  }
  meta.setAttribute("content", description);
  meta.setAttribute("data-prady-product-seo", "1");

  let link = document.querySelector('link[rel="canonical"]');
  if (!link) {
    link = document.createElement("link");
    link.setAttribute("rel", "canonical");
    document.head.appendChild(link);
  }
  const slug = text(product.slug);
  link.setAttribute("href", `${window.location.origin}/products/${encodeURIComponent(slug)}`);
  link.setAttribute("data-prady-product-seo", "1");
}

export function clearProductSeo() {
  if (typeof document === "undefined") return;
  document.querySelectorAll("[data-prady-product-seo]").forEach((node) => node.remove());
}
