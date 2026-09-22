/**
 * Centralized Social Media mock data (UI foundation only).
 * Replace these getters with real API calls in a later phase.
 * Do NOT treat statuses here as live provider sync.
 */

export const PLATFORMS = [
  { id: "instagram", label: "Instagram", color: "#E4405F" },
  { id: "facebook", label: "Facebook", color: "#1877F2" },
  { id: "linkedin", label: "LinkedIn", color: "#0A66C2" },
  { id: "tiktok", label: "TikTok", color: "#111111" },
  { id: "youtube", label: "YouTube", color: "#FF0000" },
  { id: "x", label: "X", color: "#111111" },
  { id: "pinterest", label: "Pinterest", color: "#E60023" },
  { id: "threads", label: "Threads", color: "#111111" },
  { id: "bluesky", label: "Bluesky", color: "#1285FE" },
  { id: "mastodon", label: "Mastodon", color: "#6364FF" },
  { id: "google_business", label: "Google Business", color: "#4285F4" },
];

export const PRODUCTS = [
  { id: "mfi", name: "Microfinance Platform" },
  { id: "fleet", name: "Fleet Management" },
  { id: "sms", name: "Bulk SMS" },
  { id: "crm", name: "CRM" },
  { id: "corporate", name: "Corporate / All Products" },
];

export const CAMPAIGNS = [
  { id: "mfi-awareness", name: "MFI Awareness Campaign" },
  { id: "fleet-launch", name: "Fleet Launch" },
  { id: "sms-awareness", name: "Bulk SMS Awareness" },
  { id: "crm-nurture", name: "CRM Nurture" },
];

export const CONTENT_STATUSES = [
  "Draft",
  "In review",
  "Changes requested",
  "Approved",
  "Scheduled",
  "Publishing",
  "Published",
  "Failed",
  "Cancelled",
];

export const CONNECTION_STATUSES = [
  "Connected",
  "Attention required",
  "Reconnect required",
  "Disconnected",
];

export const HEALTH_STATUSES = ["Healthy", "Warning", "Error"];

const socialAccounts = [
  {
    id: "acc-ig",
    platform: "instagram",
    name: "@pradytec",
    displayName: "Prady Technologies",
    status: "Connected",
    followers: 18420,
    publishing: true,
    analytics: true,
    inbox: true,
    lastSync: "6 minutes ago",
    productId: "corporate",
  },
  {
    id: "acc-fb",
    platform: "facebook",
    name: "Prady Technologies",
    displayName: "Prady Technologies",
    status: "Connected",
    followers: 9200,
    publishing: true,
    analytics: true,
    inbox: true,
    lastSync: "12 minutes ago",
    productId: "corporate",
  },
  {
    id: "acc-li",
    platform: "linkedin",
    name: "Prady Technologies",
    displayName: "Prady Technologies",
    status: "Connected",
    followers: 6400,
    publishing: true,
    analytics: true,
    inbox: false,
    lastSync: "18 minutes ago",
    productId: "mfi",
  },
  {
    id: "acc-tt",
    platform: "tiktok",
    name: "@pradytec",
    displayName: "Pradytec",
    status: "Connected",
    followers: 2100,
    publishing: true,
    analytics: true,
    inbox: false,
    lastSync: "25 minutes ago",
    productId: "fleet",
  },
  {
    id: "acc-yt",
    platform: "youtube",
    name: "Prady Technologies",
    displayName: "Prady Technologies",
    status: "Connected",
    followers: 1800,
    publishing: true,
    analytics: true,
    inbox: false,
    lastSync: "1 hour ago",
    productId: "corporate",
  },
  {
    id: "acc-x",
    platform: "x",
    name: "@pradytec",
    displayName: "Pradytec",
    status: "Attention required",
    followers: 3200,
    publishing: false,
    analytics: true,
    inbox: true,
    lastSync: "2 days ago",
    productId: "corporate",
  },
  {
    id: "acc-pin",
    platform: "pinterest",
    name: "Pradytec",
    displayName: "Pradytec",
    status: "Connected",
    followers: 890,
    publishing: true,
    analytics: true,
    inbox: false,
    lastSync: "40 minutes ago",
    productId: "sms",
  },
  {
    id: "acc-th",
    platform: "threads",
    name: "@pradytec",
    displayName: "Pradytec",
    status: "Connected",
    followers: 410,
    publishing: true,
    analytics: false,
    inbox: true,
    lastSync: "55 minutes ago",
    productId: "corporate",
  },
  {
    id: "acc-gb",
    platform: "google_business",
    name: "Prady Technologies Ltd",
    displayName: "Prady Technologies Ltd",
    status: "Disconnected",
    followers: null,
    publishing: false,
    analytics: false,
    inbox: false,
    lastSync: "Never",
    productId: null,
  },
];

const availableConnections = [
  { platform: "bluesky", label: "Bluesky" },
  { platform: "mastodon", label: "Mastodon" },
  { platform: "google_business", label: "Google Business" },
];

const scheduledPosts = [
  {
    id: "sp-1",
    time: "09:00",
    date: "2026-09-22",
    platform: "instagram",
    accountId: "acc-ig",
    title: "Product awareness",
    caption: "Reduce arrears with better collections workflows. See how MFI teams modernize lending ops.",
    product: "Microfinance Platform",
    productId: "mfi",
    campaign: "MFI Awareness Campaign",
    campaignId: "mfi-awareness",
    status: "Published",
    author: "James N.",
    thumbnail: null,
  },
  {
    id: "sp-2",
    time: "11:30",
    date: "2026-09-22",
    platform: "linkedin",
    accountId: "acc-li",
    title: "MFI Platform",
    caption:
      "Helping financial institutions manage portfolios with clarity. Our Microfinance Platform supports collections, disbursements and borrower journeys at scale.",
    product: "Microfinance Platform",
    productId: "mfi",
    campaign: "MFI Awareness Campaign",
    campaignId: "mfi-awareness",
    status: "Scheduled",
    author: "James N.",
    thumbnail: null,
  },
  {
    id: "sp-3",
    time: "14:00",
    date: "2026-09-22",
    platform: "facebook",
    accountId: "acc-fb",
    title: "Fleet Management",
    caption: "Track, dispatch and optimize your fleet from one command centre.",
    product: "Fleet Management",
    productId: "fleet",
    campaign: "Fleet Launch",
    campaignId: "fleet-launch",
    status: "Scheduled",
    author: "Amina K.",
    thumbnail: null,
  },
  {
    id: "sp-4",
    time: "16:30",
    date: "2026-09-22",
    platform: "tiktok",
    accountId: "acc-tt",
    title: "Product demo",
    caption: "30-second look at fleet dispatch in action #fleet #logistics",
    product: "Fleet Management",
    productId: "fleet",
    campaign: "Fleet Launch",
    campaignId: "fleet-launch",
    status: "Scheduled",
    author: "Amina K.",
    thumbnail: null,
  },
  {
    id: "sp-5",
    time: "09:00",
    date: "2026-09-23",
    platform: "facebook",
    accountId: "acc-fb",
    title: "Fleet launch",
    caption: "Introducing smarter routes for East African fleets.",
    product: "Fleet Management",
    productId: "fleet",
    campaign: "Fleet Launch",
    campaignId: "fleet-launch",
    status: "Scheduled",
    author: "Amina K.",
    thumbnail: null,
  },
  {
    id: "sp-6",
    time: "13:30",
    date: "2026-09-23",
    platform: "tiktok",
    accountId: "acc-tt",
    title: "MFI tips",
    caption: "5 signs your lending ops need an upgrade.",
    product: "Microfinance Platform",
    productId: "mfi",
    campaign: "MFI Awareness Campaign",
    campaignId: "mfi-awareness",
    status: "Scheduled",
    author: "James N.",
    thumbnail: null,
  },
  {
    id: "sp-7",
    time: "10:00",
    date: "2026-09-24",
    platform: "instagram",
    accountId: "acc-ig",
    title: "MFI Awareness",
    caption: "Modernize your lending operations with Pradytec. #fintech #microfinance",
    product: "Microfinance Platform",
    productId: "mfi",
    campaign: "MFI Awareness Campaign",
    campaignId: "mfi-awareness",
    status: "Scheduled",
    author: "James N.",
    thumbnail: null,
  },
  {
    id: "sp-8",
    time: "16:00",
    date: "2026-09-24",
    platform: "youtube",
    accountId: "acc-yt",
    title: "Platform walkthrough",
    caption: "Full walkthrough of the Microfinance Platform for lending teams.",
    product: "Microfinance Platform",
    productId: "mfi",
    campaign: "MFI Awareness Campaign",
    campaignId: "mfi-awareness",
    status: "Approved",
    author: "Sarah W.",
    thumbnail: null,
  },
  {
    id: "sp-9",
    time: "11:00",
    date: "2026-09-25",
    platform: "linkedin",
    accountId: "acc-li",
    title: "Bulk SMS Awareness",
    caption: "Reach borrowers reliably with Bulk SMS — delivery reports included.",
    product: "Bulk SMS",
    productId: "sms",
    campaign: "Bulk SMS Awareness",
    campaignId: "sms-awareness",
    status: "Scheduled",
    author: "James N.",
    thumbnail: null,
  },
  {
    id: "sp-10",
    time: "15:00",
    date: "2026-09-21",
    platform: "linkedin",
    accountId: "acc-li",
    title: "MFI Product Awareness",
    caption: "Helping financial institutions manage lending with clarity.",
    product: "Microfinance Platform",
    productId: "mfi",
    campaign: "MFI Awareness Campaign",
    campaignId: "mfi-awareness",
    status: "Failed",
    author: "James N.",
    failReason: "Connection requires attention.",
    thumbnail: null,
  },
];

const queuePosts = {
  "acc-ig": {
    Monday: [
      { id: "q1", time: "09:00", title: "Product awareness", platform: "instagram", status: "Scheduled" },
      { id: "q2", time: "16:00", title: "MFI tips", platform: "instagram", status: "Scheduled" },
    ],
    Tuesday: [
      { id: "q3", time: "09:00", title: "Fleet Management", platform: "instagram", status: "Scheduled" },
      { id: "q4", time: "16:00", title: null, platform: "instagram", status: "Available" },
    ],
    Wednesday: [
      { id: "q5", time: "09:00", title: null, platform: "instagram", status: "Available" },
      { id: "q6", time: "16:00", title: "CRM highlight", platform: "instagram", status: "Draft" },
    ],
    Thursday: [
      { id: "q7", time: "09:00", title: "Collections tip", platform: "instagram", status: "Scheduled" },
      { id: "q8", time: "16:00", title: null, platform: "instagram", status: "Available" },
    ],
    Friday: [{ id: "q9", time: "09:00", title: "Week wrap", platform: "instagram", status: "In review" }],
  },
};

const postingSchedule = {
  MON: ["09:00", "16:00"],
  TUE: ["09:00", "16:00"],
  WED: ["09:00", "16:00"],
  THU: ["09:00", "16:00"],
  FRI: ["09:00"],
  SAT: [],
  SUN: [],
};

const contentItems = [
  {
    id: "c1",
    caption: "Reduce arrears with better collections workflows across your MFI branches.",
    platforms: ["instagram", "facebook", "linkedin"],
    product: "Microfinance Platform",
    productId: "mfi",
    campaign: "MFI Awareness Campaign",
    campaignId: "mfi-awareness",
    status: "Scheduled",
    scheduledAt: "23 Sep · 10:00",
    author: "James N.",
    thumbnail: null,
  },
  {
    id: "c2",
    caption: "5 signs your lending operations need a platform upgrade.",
    platforms: ["instagram", "tiktok"],
    product: "Microfinance Platform",
    productId: "mfi",
    campaign: "MFI Awareness Campaign",
    campaignId: "mfi-awareness",
    status: "Published",
    scheduledAt: "20 Sep · 09:00",
    author: "Amina K.",
    thumbnail: null,
  },
  {
    id: "c3",
    caption: "Dispatch smarter. Track every vehicle from one dashboard.",
    platforms: ["facebook", "linkedin"],
    product: "Fleet Management",
    productId: "fleet",
    campaign: "Fleet Launch",
    campaignId: "fleet-launch",
    status: "Draft",
    scheduledAt: null,
    author: "Sarah W.",
    thumbnail: null,
  },
  {
    id: "c4",
    caption: "Bulk SMS that borrowers actually receive — with delivery reports.",
    platforms: ["linkedin", "x"],
    product: "Bulk SMS",
    productId: "sms",
    campaign: "Bulk SMS Awareness",
    campaignId: "sms-awareness",
    status: "In review",
    scheduledAt: null,
    author: "James N.",
    thumbnail: null,
  },
  {
    id: "c5",
    caption: "CRM pipelines built for product demos and lending partners.",
    platforms: ["linkedin"],
    product: "CRM",
    productId: "crm",
    campaign: "CRM Nurture",
    campaignId: "crm-nurture",
    status: "Failed",
    scheduledAt: "21 Sep · 15:00",
    author: "Amina K.",
    thumbnail: null,
  },
  {
    id: "c6",
    caption: "Walkthrough: Microfinance Platform for collections teams.",
    platforms: ["youtube", "linkedin"],
    product: "Microfinance Platform",
    productId: "mfi",
    campaign: "MFI Awareness Campaign",
    campaignId: "mfi-awareness",
    status: "Approved",
    scheduledAt: "24 Sep · 16:00",
    author: "Sarah W.",
    thumbnail: null,
  },
];

const approvalItems = [
  {
    id: "ap1",
    title: "MFI Awareness Campaign",
    caption: "Modernize your lending operations with clearer collections and borrower journeys.",
    platforms: ["instagram", "facebook", "linkedin"],
    createdBy: "Marketing Officer",
    submittedAt: "22 Sep 2026 · 11:43",
    status: "Pending",
    product: "Microfinance Platform",
  },
  {
    id: "ap2",
    title: "Fleet Launch teaser",
    caption: "See fleet dispatch in under 30 seconds.",
    platforms: ["tiktok", "instagram"],
    createdBy: "Amina K.",
    submittedAt: "21 Sep 2026 · 16:20",
    status: "Pending",
    product: "Fleet Management",
  },
  {
    id: "ap3",
    title: "Bulk SMS reliability story",
    caption: "Delivery reports your ops team can trust.",
    platforms: ["linkedin"],
    createdBy: "James N.",
    submittedAt: "20 Sep 2026 · 09:10",
    status: "Approved",
    product: "Bulk SMS",
  },
  {
    id: "ap4",
    title: "CRM nurture sequence",
    caption: "Turn demo requests into qualified conversations.",
    platforms: ["facebook", "linkedin"],
    createdBy: "Sarah W.",
    submittedAt: "19 Sep 2026 · 14:05",
    status: "Changes requested",
    product: "CRM",
    changeNote: "Please shorten the caption and add a clearer CTA.",
  },
];

const overviewKpis = [
  { id: "accounts", label: "Connected accounts", value: "8" },
  { id: "scheduled", label: "Scheduled posts", value: "14" },
  { id: "published", label: "Published this month", value: "42" },
  { id: "followers", label: "Total followers", value: "38,420" },
  { id: "engagement", label: "Engagement", value: "6.8%" },
  { id: "attention", label: "Needs attention", value: "2", tone: "warn" },
];

const recentPerformance = [
  { label: "Reach", value: "124,820", delta: "+18%" },
  { label: "Engagements", value: "8,423", delta: "+12%" },
  { label: "Link clicks", value: "2,194", delta: "+21%" },
  { label: "Followers gained", value: "643", delta: "+9%" },
];

const performanceSeries = [
  { day: "Mon", reach: 42, engagement: 28 },
  { day: "Tue", reach: 55, engagement: 34 },
  { day: "Wed", reach: 48, engagement: 30 },
  { day: "Thu", reach: 70, engagement: 44 },
  { day: "Fri", reach: 62, engagement: 40 },
  { day: "Sat", reach: 38, engagement: 22 },
  { day: "Sun", reach: 50, engagement: 32 },
];

const analyticsMetrics = [
  { id: "followers", label: "Followers", value: "38,420", delta: "+2.1%" },
  { id: "reach", label: "Reach", value: "248,400", delta: "+18%" },
  { id: "impressions", label: "Impressions", value: "382,120", delta: "+14%" },
  { id: "engagements", label: "Engagements", value: "24,840", delta: "+12%" },
  { id: "clicks", label: "Link clicks", value: "8,420", delta: "+21%" },
  { id: "rate", label: "Engagement rate", value: "6.5%", delta: "+0.4pp" },
];

const analyticsSeries = [
  { day: "1", followers: 30, reach: 40, engagement: 22, clicks: 12 },
  { day: "5", followers: 34, reach: 48, engagement: 28, clicks: 16 },
  { day: "10", followers: 38, reach: 55, engagement: 32, clicks: 18 },
  { day: "15", followers: 42, reach: 62, engagement: 36, clicks: 22 },
  { day: "20", followers: 48, reach: 70, engagement: 42, clicks: 26 },
  { day: "25", followers: 52, reach: 78, engagement: 48, clicks: 30 },
  { day: "30", followers: 58, reach: 85, engagement: 52, clicks: 34 },
];

const performanceByPlatform = [
  { platform: "instagram", label: "Instagram", percent: 42 },
  { platform: "linkedin", label: "LinkedIn", percent: 28 },
  { platform: "facebook", label: "Facebook", percent: 18 },
  { platform: "tiktok", label: "TikTok", percent: 12 },
];

const performanceByProduct = [
  { id: "mfi", label: "Microfinance", percent: 42 },
  { id: "fleet", label: "Fleet Management", percent: 24 },
  { id: "sms", label: "Bulk SMS", percent: 18 },
  { id: "crm", label: "CRM", percent: 16 },
];

const performanceByCampaign = [
  { id: "mfi-awareness", label: "MFI Awareness", percent: 38 },
  { id: "fleet-launch", label: "Fleet Launch", percent: 26 },
  { id: "sms-awareness", label: "Bulk SMS Awareness", percent: 20 },
  { id: "crm-nurture", label: "CRM Nurture", percent: 16 },
];

const topPosts = [
  {
    id: "tp1",
    platform: "instagram",
    product: "MFI Platform",
    caption: "5 signs your lending operations need...",
    reach: "34,820",
    engagement: "2,482",
    clicks: "311",
  },
  {
    id: "tp2",
    platform: "linkedin",
    product: "Microfinance Platform",
    caption: "Helping financial institutions manage...",
    reach: "22,140",
    engagement: "1,890",
    clicks: "540",
  },
  {
    id: "tp3",
    platform: "facebook",
    product: "Fleet Management",
    caption: "Track, dispatch and optimize your fleet...",
    reach: "18,600",
    engagement: "1,204",
    clicks: "280",
  },
  {
    id: "tp4",
    platform: "tiktok",
    product: "Fleet Management",
    caption: "30-second look at fleet dispatch...",
    reach: "41,200",
    engagement: "3,110",
    clicks: "190",
  },
  {
    id: "tp5",
    platform: "linkedin",
    product: "Bulk SMS",
    caption: "Reach borrowers reliably with Bulk SMS...",
    reach: "12,480",
    engagement: "980",
    clicks: "420",
  },
];

const inboxThreads = [
  {
    id: "in1",
    type: "Comments",
    platform: "instagram",
    name: "Jane Mwangi",
    preview: "How much does the microfinance system cost?",
    time: "2 min ago",
    unread: true,
    assignedToMe: true,
    postContext: "Modernize your lending operations...",
    messages: [
      {
        id: "m1",
        from: "Jane Mwangi",
        body: "How much does the microfinance system cost?",
        time: "2 min ago",
        mine: false,
      },
    ],
  },
  {
    id: "in2",
    type: "Messages",
    platform: "facebook",
    name: "Kevin Otieno",
    preview: "Can we book a fleet demo for next week?",
    time: "18 min ago",
    unread: true,
    assignedToMe: false,
    postContext: null,
    messages: [
      {
        id: "m2",
        from: "Kevin Otieno",
        body: "Hi — can we book a fleet demo for next week?",
        time: "18 min ago",
        mine: false,
      },
      {
        id: "m3",
        from: "You",
        body: "Thanks Kevin! Happy to help — which city are you based in?",
        time: "12 min ago",
        mine: true,
      },
      {
        id: "m4",
        from: "Kevin Otieno",
        body: "Nairobi. Tuesday or Wednesday works.",
        time: "10 min ago",
        mine: false,
      },
    ],
  },
  {
    id: "in3",
    type: "Mentions",
    platform: "x",
    name: "@techkenya",
    preview: "Interesting approach from @pradytec on MFI ops",
    time: "1 hour ago",
    unread: false,
    assignedToMe: false,
    postContext: null,
    messages: [
      {
        id: "m5",
        from: "@techkenya",
        body: "Interesting approach from @pradytec on MFI ops",
        time: "1 hour ago",
        mine: false,
      },
    ],
  },
  {
    id: "in4",
    type: "Comments",
    platform: "linkedin",
    name: "Grace Wanjiru",
    preview: "Do you support multi-branch portfolios?",
    time: "Yesterday",
    unread: false,
    assignedToMe: true,
    postContext: "Helping financial institutions manage portfolios...",
    messages: [
      {
        id: "m6",
        from: "Grace Wanjiru",
        body: "Do you support multi-branch portfolios?",
        time: "Yesterday",
        mine: false,
      },
    ],
  },
];

const integrationHealth = [
  {
    id: "h-ig",
    accountId: "acc-ig",
    platform: "instagram",
    name: "@pradytec",
    health: "Healthy",
    connection: "Connected",
    publishing: "Operational",
    analytics: "Operational",
    permissions: "4 / 4",
    lastSync: "6 minutes ago",
    token: "Healthy",
  },
  {
    id: "h-fb",
    accountId: "acc-fb",
    platform: "facebook",
    name: "Prady Technologies",
    health: "Healthy",
    connection: "Connected",
    publishing: "Operational",
    analytics: "Operational",
    permissions: "4 / 4",
    lastSync: "12 minutes ago",
    token: "Healthy",
  },
  {
    id: "h-li",
    accountId: "acc-li",
    platform: "linkedin",
    name: "Prady Technologies",
    health: "Healthy",
    connection: "Connected",
    publishing: "Operational",
    analytics: "Operational",
    permissions: "3 / 3",
    lastSync: "18 minutes ago",
    token: "Healthy",
  },
  {
    id: "h-tt",
    accountId: "acc-tt",
    platform: "tiktok",
    name: "@pradytec",
    health: "Healthy",
    connection: "Connected",
    publishing: "Operational",
    analytics: "Operational",
    permissions: "3 / 3",
    lastSync: "25 minutes ago",
    token: "Healthy",
  },
  {
    id: "h-x",
    accountId: "acc-x",
    platform: "x",
    name: "@pradytec",
    health: "Warning",
    connection: "Attention required",
    publishing: "Paused",
    analytics: "Operational",
    permissions: "2 / 4",
    lastSync: "2 days ago",
    token: "Expiring soon",
  },
  {
    id: "h-gb",
    accountId: "acc-gb",
    platform: "google_business",
    name: "Prady Technologies Ltd",
    health: "Error",
    connection: "Disconnected",
    publishing: "Unavailable",
    analytics: "Unavailable",
    permissions: "0 / 3",
    lastSync: "Never",
    token: "Missing",
  },
];

const activity = [
  { id: "a1", time: "11:43", label: "LinkedIn post published", when: "Today" },
  { id: "a2", time: "10:10", label: "Instagram analytics synced", when: "Today" },
  { id: "a3", time: "09:00", label: "Facebook post published", when: "Today" },
  { id: "a4", time: "08:40", label: "TikTok connection refreshed", when: "Today" },
  { id: "a5", time: "16:20", label: "X connection requires attention", when: "Yesterday" },
];

const composerDefaults = {
  caption:
    "Helping financial institutions manage lending with clarity. Explore the Microfinance Platform from Pradytec.",
  hashtags: "#fintech #microfinance #pradytec",
  destinationUrl: "https://pradytec.com/products/microfinance",
  cta: "Learn more",
  campaignId: "mfi-awareness",
  productId: "mfi",
  note: "",
  overrides: {
    instagram: "Short visual caption + hashtags\n\nModernize lending ops.\n#fintech #microfinance",
    facebook: "Helping financial institutions manage lending with clarity. Learn more about our Microfinance Platform.",
    linkedin:
      "Long professional copy\n\nHelping financial institutions manage portfolios with clarity. Our Microfinance Platform supports collections, disbursements and borrower journeys at scale across East Africa.",
    x: "Short condensed caption\n\nModernize lending ops with Pradytec MFI Platform.",
    tiktok: "Product demo energy — lending ops, remixed. #fintech",
  },
};

/* --- Mock service getters (swap for API later) --- */

export function getMockSocialAccounts() {
  return socialAccounts;
}

export function getMockAvailableConnections() {
  return availableConnections;
}

export function getMockScheduledPosts() {
  return scheduledPosts;
}

export function getMockTodaysPublishing() {
  return scheduledPosts.filter((p) => p.date === "2026-09-22");
}

export function getMockUpcomingPosts() {
  return scheduledPosts.filter((p) => p.date >= "2026-09-22" && p.status !== "Failed").slice(0, 7);
}

export function getMockQueuePosts(accountId = "acc-ig") {
  return queuePosts[accountId] || queuePosts["acc-ig"];
}

export function getMockPostingSchedule() {
  return postingSchedule;
}

export function getMockContentItems() {
  return contentItems;
}

export function getMockApprovals() {
  return approvalItems;
}

export function getMockOverviewKpis() {
  return overviewKpis;
}

export function getMockRecentPerformance() {
  return recentPerformance;
}

export function getMockPerformanceSeries() {
  return performanceSeries;
}

export function getMockAnalyticsMetrics() {
  return analyticsMetrics;
}

export function getMockAnalyticsSeries() {
  return analyticsSeries;
}

export function getMockPerformanceByPlatform() {
  return performanceByPlatform;
}

export function getMockPerformanceByProduct() {
  return performanceByProduct;
}

export function getMockPerformanceByCampaign() {
  return performanceByCampaign;
}

export function getMockTopPosts() {
  return topPosts;
}

export function getMockInboxThreads() {
  return inboxThreads;
}

export function getMockIntegrationHealth() {
  return integrationHealth;
}

export function getMockActivity() {
  return activity;
}

export function getMockComposerDefaults() {
  return { ...composerDefaults, overrides: { ...composerDefaults.overrides } };
}

export function getMockAccountSummary() {
  const accounts = socialAccounts;
  return {
    connected: accounts.filter((a) => a.status === "Connected").length,
    attention: accounts.filter((a) => a.status === "Attention required").length,
    disconnected: accounts.filter((a) => a.status === "Disconnected" || a.status === "Reconnect required").length,
  };
}

export function getPlatformLabel(id) {
  return PLATFORMS.find((p) => p.id === id)?.label || id;
}
