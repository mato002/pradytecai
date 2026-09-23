import assert from "node:assert/strict";
import {
  productCta,
  resolveSections,
  showOverview,
  heroSources,
  mediaIsRenderable,
  pickFeaturedMedia,
  visibleMedia,
  mediaThumbSrc,
} from "../src/lib/productDetailModel.js";

const micro = {
  name: "Prady Microfinance",
  slug: "prady-microfinance",
  tagline: "Run lending from one platform.",
  short_description: "Short lending copy.",
  overview: "A longer overview of loans, collections, payments and operations for institutions.",
  cta_label: "Request demo",
  cta_type: "demo",
  secondary_cta_label: "Talk to Our Team",
  secondary_cta_type: "contact",
  poster_url: "/media/products/posters/mfi.jpg",
  highlights: [{ id: 1, title: "Loan Management" }],
  audiences: [{ id: 1, title: "Microfinance Institutions" }],
  problems: [{ id: 1, title: "Manual loan tracking" }],
  capability_groups: [{ id: 1, title: "Lending", capabilities: [{ id: 1, title: "Appraisal" }] }],
  workflow_steps: [{ id: 1, title: "Application" }],
  media: [],
  integrations: [{ id: 1, name: "M-Pesa" }],
  controls: [{ id: 1, title: "Role-based access" }],
  outcomes: [{ id: 1, title: "Faster loan processing" }],
  implementation_steps: [{ id: 1, title: "Go live" }],
  faqs: [{ id: 1, question: "Who is it for?", answer: "Lenders." }],
  custom_sections: [],
  page_sections: [
    { id: 1, section_type: "highlights", display_order: 10, is_enabled: true, title_override: "" },
    { id: 2, section_type: "media", display_order: 20, is_enabled: true, title_override: "In the product" },
    { id: 3, section_type: "faq", display_order: 5, is_enabled: false, title_override: "Hidden" },
    { id: 4, section_type: "capabilities", display_order: 30, is_enabled: true },
  ],
};

const gps = {
  name: "GPS Hosting",
  slug: "gps-hosting",
  short_description: "Tracking for fleets.",
  cta_label: "Request demo",
  cta_type: "demo",
  highlights: [{ title: "Vehicle tracking" }],
  audiences: [{ title: "Fleet operators" }],
  capability_groups: [{ title: "Tracking", capabilities: [{ title: "Live location" }] }],
  workflow_steps: [{ title: "Register devices" }],
  implementation_steps: [{ title: "Go live" }],
  faqs: [],
  integrations: [],
  page_sections: [
    { id: 1, section_type: "capabilities", display_order: 20, is_enabled: true, title_override: "Tracking capabilities" },
    { id: 2, section_type: "highlights", display_order: 10, is_enabled: true },
    { id: 3, section_type: "workflow", display_order: 30, is_enabled: true },
  ],
};

const property = {
  name: "Property Management",
  slug: "property-management",
  short_description: "Rent and tenants.",
  cta_label: "Request demo",
  cta_type: "demo",
  audiences: [{ title: "Landlords" }],
  capability_groups: [{ title: "Rent collection", capabilities: [{ title: "Arrears" }] }],
  outcomes: [{ title: "Arrears you can see" }],
  custom_sections: [{ title: "Maintenance stays with the tenancy", body: "Follow-up stays on the record." }],
  faqs: [{ question: "What does it cover?", answer: "Properties and rent." }],
  workflow_steps: [],
  page_sections: [
    { id: 1, section_type: "audience", display_order: 10, is_enabled: true },
    { id: 2, section_type: "custom", display_order: 40, is_enabled: true },
    { id: 3, section_type: "faq", display_order: 50, is_enabled: true },
    { id: 4, section_type: "workflow", display_order: 20, is_enabled: true },
  ],
};

const bare = {
  name: "Bare",
  slug: "bare",
  short_description: "Only the hero.",
  cta_label: "Request demo",
  cta_type: "demo",
  highlights: [{ title: "One highlight" }],
  page_sections: [],
};

const microSections = resolveSections(micro).map((section) => section.type);
assert.deepEqual(microSections, [
  "highlights",
  "audience",
  "problems",
  "capabilities",
  "workflow",
  "integrations",
  "controls",
  "outcomes",
  "implementation",
]);
assert.equal(microSections.includes("media"), false);
assert.equal(microSections.includes("faq"), false);

const gpsResolved = resolveSections(gps);
assert.equal(gpsResolved.find((section) => section.type === "capabilities").title, "Tracking capabilities");
const gpsSections = gpsResolved.map((section) => section.type);
assert.deepEqual(gpsSections, ["highlights", "audience", "capabilities", "workflow", "implementation"]);
assert.equal(gpsSections.includes("faq"), false);
assert.equal(gpsSections.includes("integrations"), false);

const propertySections = resolveSections(property).map((section) => section.type);
assert.deepEqual(propertySections, ["audience", "capabilities", "outcomes", "custom", "faq"]);
assert.equal(propertySections.includes("workflow"), false);

assert.deepEqual(resolveSections(bare).map((section) => section.type), ["highlights"]);

const demo = productCta(micro, "primary");
assert.equal(demo.external, false);
assert.match(demo.to, /request_type=demo/);
assert.match(demo.to, /product_slug=prady-microfinance/);
const talk = productCta(micro, "secondary");
assert.match(talk.to, /request_type=contact/);
assert.equal(productCta(property, "secondary"), null);

assert.equal(heroSources({ poster_url: "/media/a.jpg" }).desktop, "/media/a.jpg");
assert.equal(heroSources({ hero_image_url: "/h.jpg", poster_url: "/p.jpg" }).desktop, "/h.jpg");
assert.equal(heroSources({}).desktop, "");
assert.equal(heroSources({ slug: "prady-microfinance" }).desktop, "/images/mfi.jpg");
assert.equal(
  heroSources({ slug: "prady-microfinance", poster_url: "/media/x.jpg" }).desktop,
  "/media/x.jpg",
);

assert.equal(showOverview({ short_description: "Same", overview: "Same" }), "");
assert.ok(showOverview(micro).includes("longer overview"));

const mixedMediaProduct = {
  media: [
    {
      id: 1,
      media_type: "image",
      image_url: "/media/a.jpg",
      is_featured: false,
      display_order: 20,
    },
    {
      id: 2,
      media_type: "video",
      video_source: "upload",
      video_file_url: "/media/v.mp4",
      thumbnail_url: "/media/t.jpg",
      is_featured: true,
      display_order: 10,
    },
    { id: 3, media_type: "image", image_url: "", is_featured: false },
    { id: 4, media_type: "video", video_source: "upload", video_file_url: "", is_featured: false },
  ],
};
assert.equal(visibleMedia(mixedMediaProduct).length, 2);
assert.equal(pickFeaturedMedia(mixedMediaProduct.media).id, 2);
assert.equal(mediaIsRenderable({ media_type: "image", image_url: "/x.png" }), true);
assert.equal(mediaIsRenderable({ media_type: "video", video_url: "https://example.com/v" }), true);
assert.equal(mediaThumbSrc(mixedMediaProduct.media[1]), "/media/t.jpg");
assert.equal(mediaThumbSrc({ media_type: "video", video_file_url: "/v.mp4" }), "");

console.log("product detail model checks passed");
