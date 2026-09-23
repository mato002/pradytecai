/** Marketing chrome (contact, capabilities, industries, group layout).
 *  Product catalog content is loaded from the Django API / database.
 */
export const portfolio = {
  company: "Prady Technologies Ltd",
  tagline: "Doing It Differently",
  contact: {
    phone: "+254 722 295 194",
    phone_href: "tel:+254722295194",
    email: "marketing@pradytecai.com",
    email_href: "mailto:marketing@pradytecai.com",
    location: "Nairobi, Kenya",
    hours: "Mon – Fri: 8:00 AM – 6:00 PM EAT",
  },
  solutions: [
    {
      name: "Financial Services",
      short: "Lending, savings, collections and payments.",
      icon: "finance",
      href: "/products",
    },
    {
      name: "Business Operations",
      short: "HR, payroll and everyday workflows.",
      icon: "hr",
      href: "/services",
    },
    {
      name: "Mobility & Tracking",
      short: "Vehicles, assets and field intelligence.",
      icon: "location",
      href: "/products/gps-hosting",
    },
    {
      name: "Digital Commerce",
      short: "Selling, customers and connected payments.",
      icon: "live",
      href: "/products/live-commerce",
    },
  ],
  trust: [
    { label: "Secure by Design", icon: "shield" },
    { label: "Built for African Businesses", icon: "building" },
    { label: "Dedicated Support", icon: "support" },
  ],
  manage: [
    { label: "Lending & Collections", icon: "finance", href: "/products/prady-microfinance" },
    { label: "SACCO & Members", icon: "users", href: "/products/sacco-system" },
    { label: "GPS & Fleet Tracking", icon: "location", href: "/products/gps-hosting" },
    { label: "Property & Rent", icon: "building", href: "/products/property-management" },
    { label: "Automotive Parts", icon: "car", href: "/products/spareme" },
    { label: "Social Selling", icon: "live", href: "/products/live-commerce" },
    { label: "Travel & Payments", icon: "wallet", href: "/products/mtalii-travel-wallet" },
    { label: "Custom Software", icon: "code", href: "/#capabilities" },
  ],
  /** Layout helper only — product copy/images come from the API. */
  product_groups: [
    {
      key: "finance",
      title: "Finance & Community",
      slugs: ["prady-microfinance", "rafiki-loan", "sacco-system", "chama-system"],
    },
    {
      key: "assets",
      title: "Business, Mobility & Assets",
      slugs: ["gps-hosting", "property-management", "spareme"],
    },
    {
      key: "commerce",
      title: "Commerce & Travel",
      slugs: ["live-commerce", "mtalii-travel-wallet"],
    },
  ],
  capabilities: [
    {
      name: "Custom Software Development",
      short: "Bespoke modules and workflows tailored to your institution.",
      icon: "code",
      href: "/contact?topic=custom",
    },
    {
      name: "Cloud & Hosting",
      short: "Secure hosting, monitoring and reliable cloud operations.",
      icon: "cloud",
      href: "/contact",
    },
    {
      name: "Integrations & APIs",
      short: "Connect Prady platforms with your existing systems and partners.",
      icon: "handshake",
      href: "/contact",
    },
    {
      name: "Payments & M-Pesa Integrations",
      short: "Collections, disbursements and payment workflows for African markets.",
      icon: "wallet",
      href: "/contact",
    },
    {
      name: "Implementation & Migration",
      short: "Structured rollout, data migration and go-live support.",
      icon: "rocket",
      href: "/contact",
    },
    {
      name: "Training & Support",
      short: "Onboarding, documentation and responsive operational support.",
      icon: "support",
      href: "/contact",
    },
  ],
  industries: [
    {
      name: "Microfinance & Lending",
      short: "Operating systems for MFIs, credit companies and digital lenders.",
      icon: "finance",
      href: "/products/prady-microfinance",
    },
    {
      name: "SACCOs",
      short: "Membership, savings, loans and governance for cooperatives.",
      icon: "users",
      href: "/products/sacco-system",
    },
    {
      name: "Chamas",
      short: "Transparent group accounts, contributions and welfare tracking.",
      icon: "group",
      href: "/products/chama-system",
    },
    {
      name: "Fleet & Logistics",
      short: "GPS hosting and tracking for vehicles, fleets and resellers.",
      icon: "location",
      href: "/products/gps-hosting",
    },
    {
      name: "Property",
      short: "Units, tenants, leases, rent and maintenance in one platform.",
      icon: "building",
      href: "/products/property-management",
    },
    {
      name: "Automotive",
      short: "Vehicle-first spare parts matching and dealer inventory.",
      icon: "car",
      href: "/products/spareme",
    },
    {
      name: "Social Commerce",
      short: "Live selling platforms for creators, SMEs and merchants.",
      icon: "live",
      href: "/products/live-commerce",
    },
    {
      name: "Tourism",
      short: "Travel wallets, FX and merchant acceptance for tourism.",
      icon: "wallet",
      href: "/products/mtalii-travel-wallet",
    },
  ],
};
