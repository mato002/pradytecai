import React from "react";

/** Compact white-on-green mark matching Prady brand boards */
export default function AdminBrandMark({ className = "admin-sidebar__mark" }) {
  return (
    <span className={className} aria-hidden="true">
      <svg width="22" height="22" viewBox="0 0 48 48" fill="none">
        <path d="M6 6h28v16H22v8H6V6z" fill="#004d40" />
        <path d="M22 22h16v16H22V22z" fill="#004d40" />
        <rect x="28" y="28" width="14" height="14" fill="#c9a227" />
      </svg>
    </span>
  );
}

export function NavIcon({ name }) {
  const common = {
    className: "nav-pill__icon",
    viewBox: "0 0 24 24",
    fill: "none",
    stroke: "currentColor",
    strokeWidth: "1.8",
    strokeLinecap: "round",
    strokeLinejoin: "round",
    "aria-hidden": true,
  };
  const paths = {
    dashboard: "M4 4h7v7H4zm9 0h7v5h-7zM4 13h7v7H4zm9 3h7v4h-7z",
    pulse: "M3 12h4l2-6 4 12 2-6h6",
    tasks: "M9 11l2 2 4-4M5 5h14v14H5z",
    campaigns: "M4 19V5m4 14V9m4 10V7m4 12v-6m4 6V9",
    calendar: "M8 3v3M16 3v3M4 8h16M5 5h14a1 1 0 011 1v13a1 1 0 01-1 1H5a1 1 0 01-1-1V6a1 1 0 011-1z",
    content: "M7 4h10a2 2 0 012 2v12a2 2 0 01-2 2H7a2 2 0 01-2-2V6a2 2 0 012-2zm0 4h10",
    approvals: "M9 12l2 2 4-4M12 3a9 9 0 110 18 9 9 0 010-18z",
    social: "M17 8a3 3 0 11-2.8-3M7 14a3 3 0 100 6 3 3 0 000-6zm10 0a3 3 0 100 6 3 3 0 000-6zM9.5 15.5l5-3",
    compose: "M12 5v14M5 12h14M16 4l4 4-10 10H6v-4L16 4z",
    queue: "M4 6h16M4 12h16M4 18h10",
    accounts: "M16 7a4 4 0 11-8 0 4 4 0 018 0zM4 20a8 8 0 0116 0M19 11v6M16 14h6",
    inbox: "M4 6h16v12H4zM4 10l8 5 8-5",
    health: "M12 3a9 9 0 100 18 9 9 0 000-18zm0 4v5l3 2",
    analytics: "M4 19V5M8 19v-8M12 19v-5M16 19V9M20 19v-3",
    leads: "M4 6h16M4 12h10M4 18h7",
    demos: "M8 5v14l11-7z",
    subscribers: "M4 6h16v12H4zM4 6l8 7 8-7",
    products: "M4 7l8-3 8 3v10l-8 3-8-3V7z",
    blog: "M5 5h14v14H5zM8 9h8M8 13h6",
    careers: "M4 9h16v10H4zM9 9V7a3 3 0 016 0v2",
    applications: "M8 7h8M8 11h8M8 15h5M6 4h12v16H6z",
    users: "M12 12a4 4 0 100-8 4 4 0 000 8zM4 20a8 8 0 0116 0",
    roles: "M12 3l8 4v6c0 5-3.5 8-8 9-4.5-1-8-4-8-9V7l8-4z",
    integrations: "M8 12h8M12 8v8M7 7l2 2M15 7l2 2M7 17l2-2M15 17l2-2",
    settings: "M12 8a4 4 0 100 8 4 4 0 000-8zm0-5v2m0 14v2m9-9h-2M5 12H3m15.4-6.4l-1.4 1.4M7 17l-1.4 1.4m12.8 0L17 17M7 7L5.6 5.6",
    audit: "M9 5h11M9 12h11M9 19h11M4 5h.01M4 12h.01M4 19h.01",
  };
  return (
    <svg {...common}>
      <path d={paths[name] || paths.dashboard} />
    </svg>
  );
}
