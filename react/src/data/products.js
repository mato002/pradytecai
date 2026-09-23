/**
 * Hardcoded public product catalog for the marketing site.
 * Prefer these for homepage/products until admin media is complete.
 * poster_url paths are served from react/public/images/.
 */

export const MAIN_PRODUCTS = [
  {
    id: "prady-microfinance",
    slug: "prady-microfinance",
    name: "Prady Microfinance",
    short:
      "Complete operating platform for MFIs and lenders covering loans, collections, payments and institutional operations.",
    short_description:
      "Complete operating platform for MFIs and lenders covering loans, collections, payments and institutional operations.",
    description:
      "A complete operating platform for microfinance institutions and lenders, covering customers, loans, collections, payments, M-Pesa, accounting, reporting, HR, controls and institutional operations.",
    market: "MFIs, credit companies and lenders",
    icon: "finance",
    group_key: "finance",
    poster_url: "/images/mfi.jpg",
    cta_label: "Request demo",
    cta_type: "demo",
    cta_url: "",
    is_featured: true,
    display_order: 10,
  },
  {
    id: "rafiki-loan",
    slug: "rafiki-loan",
    name: "Rafiki Loan",
    short: "Digital lending marketplace connecting borrowers with institutions and capital providers.",
    short_description:
      "Digital lending marketplace connecting borrowers with institutions and capital providers.",
    description:
      "A digital lending marketplace connecting people seeking financing with institutions or people offering financing. Supports discovery, matching and the journey between borrower and finance provider.",
    market: "Borrowers, lenders and capital providers",
    icon: "handshake",
    group_key: "finance",
    poster_url: null,
    cta_label: "Request demo",
    cta_type: "demo",
    cta_url: "",
    is_featured: false,
    display_order: 20,
  },
  {
    id: "sacco-system",
    slug: "sacco-system",
    name: "SACCO System",
    short: "Digital operating system for SACCOs covering membership, savings and loans.",
    short_description: "Digital operating system for SACCOs covering membership, savings and loans.",
    description:
      "A digital operating system for SACCO and member-based financial institutions covering membership, savings, contributions, loans, accounts, governance and institutional administration.",
    market: "SACCOs and member-based financial institutions",
    icon: "users",
    group_key: "finance",
    poster_url: null,
    cta_label: "Request demo",
    cta_type: "demo",
    cta_url: "",
    is_featured: false,
    display_order: 30,
  },
  {
    id: "chama-system",
    slug: "chama-system",
    name: "Chama System",
    short: "Simple digital platform for chamas, investment clubs and welfare groups.",
    short_description: "Simple digital platform for chamas, investment clubs and welfare groups.",
    description:
      "A simplified digital platform for investment groups and chamas covering members, contributions, welfare, loans, projects/investments, commitments, documents and transparent group accounts.",
    market: "Chamas, investment clubs and welfare groups",
    icon: "group",
    group_key: "finance",
    poster_url: null,
    cta_label: "Request demo",
    cta_type: "demo",
    cta_url: "",
    is_featured: false,
    display_order: 40,
  },
  {
    id: "gps-hosting",
    slug: "gps-hosting",
    name: "GPS Hosting & Tracking Platform",
    short: "GPS tracking and hosting for vehicle trackers, fleets and GPS resellers.",
    short_description: "GPS tracking and hosting for vehicle trackers, fleets and GPS resellers.",
    description:
      "A GPS tracking and hosting platform for compatible vehicle trackers, fleets and GPS resellers, including support for Prady-branded tracking devices.",
    market: "Vehicle owners, fleet operators, logistics businesses and GPS resellers",
    icon: "location",
    group_key: "assets",
    poster_url: null,
    cta_label: "Request demo",
    cta_type: "demo",
    cta_url: "",
    is_featured: false,
    display_order: 50,
  },
  {
    id: "property-management",
    slug: "property-management",
    name: "Property Management System",
    short: "Manage properties, tenants, leases, rent, arrears and maintenance in one place.",
    short_description:
      "Manage properties, tenants, leases, rent, arrears and maintenance in one place.",
    description:
      "A platform for managing properties, units, tenants, leases, rent, payments, arrears, maintenance and property performance.",
    market: "Landlords, property managers and real-estate businesses",
    icon: "building",
    group_key: "assets",
    poster_url: null,
    cta_label: "Request demo",
    cta_type: "demo",
    cta_url: "",
    is_featured: false,
    display_order: 60,
  },
  {
    id: "spareme",
    slug: "spareme",
    name: "SpareMe",
    short: "Vehicle-first automotive commerce matching spare parts to exact vehicles.",
    short_description: "Vehicle-first automotive commerce matching spare parts to exact vehicles.",
    description:
      "A vehicle-first automotive commerce and intelligence ecosystem. Vehicle owners identify their exact vehicle while dealers manage inventory and compatible spare parts can be matched to vehicles.",
    market: "Vehicle owners, spare-parts dealers, mechanics, suppliers and garages",
    icon: "car",
    group_key: "assets",
    poster_url: null,
    cta_label: "Request demo",
    cta_type: "demo",
    cta_url: "",
    is_featured: false,
    display_order: 70,
  },
  {
    id: "live-commerce",
    slug: "live-commerce",
    name: "Live Commerce / Social Selling Platform",
    short: "Social-commerce platform for live shows, audiences and lasting product shelves.",
    short_description:
      "Social-commerce platform for live shows, audiences and lasting product shelves.",
    description:
      "A social-commerce platform where sellers and creators can announce live shows, attract audiences and showcase products during and after live broadcasts.",
    market: "Social sellers, creators, SMEs and online merchants",
    icon: "live",
    group_key: "commerce",
    poster_url: null,
    cta_label: "Request demo",
    cta_type: "demo",
    cta_url: "",
    is_featured: false,
    display_order: 80,
  },
  {
    id: "mtalii-travel-wallet",
    slug: "mtalii-travel-wallet",
    name: "Mtalii Travel Wallet",
    short: "Tourism-focused wallet for payments, FX and local merchant acceptance.",
    short_description: "Tourism-focused wallet for payments, FX and local merchant acceptance.",
    description:
      "A tourism-focused wallet and travel platform designed around tourists and local merchants, including payments, FX, merchant acceptance and tourism-oriented financial services.",
    market: "Tourists, tour operators, guides and local merchants",
    icon: "wallet",
    group_key: "commerce",
    poster_url: null,
    cta_label: "Request demo",
    cta_type: "demo",
    cta_url: "",
    is_featured: false,
    display_order: 90,
  },
];

export function getProductBySlug(slug) {
  return MAIN_PRODUCTS.find((p) => p.slug === slug) || null;
}

export function getFeaturedProduct() {
  return MAIN_PRODUCTS.find((p) => p.is_featured) || MAIN_PRODUCTS[0] || null;
}
