import React, { useMemo, useState } from "react";
import { Link, NavLink, Outlet, useLocation, useNavigate } from "react-router-dom";
import { useAuth } from "../auth/AuthContext";
import AdminBrandMark, { NavIcon } from "../components/admin/AdminBrandMark";

const NAV = [
  {
    section: "Command",
    items: [
      { to: "/admin", label: "Dashboard", perm: "dashboard.view", end: true, icon: "dashboard" },
      { to: "/admin/pulse", label: "Marketing Pulse", perm: "pulse.view", icon: "pulse" },
      { to: "/admin/tasks", label: "Tasks", perm: "tasks.view", icon: "tasks" },
    ],
  },
  {
    section: "Social Media",
    items: [
      { to: "/admin/social", label: "Overview", perm: "social_accounts.view", end: true, icon: "social" },
      { to: "/admin/social/create", label: "Create Post", perm: "content.view", icon: "compose" },
      { to: "/admin/social/calendar", label: "Calendar", perm: "content.view", icon: "calendar" },
      { to: "/admin/social/queue", label: "Queue", perm: "content.view", icon: "queue" },
      { to: "/admin/social/content", label: "Content", perm: "content.view", icon: "content" },
      { to: "/admin/social/approvals", label: "Approvals", perm: "content.view", icon: "approvals" },
      { to: "/admin/social/accounts", label: "Social Accounts", perm: "social_accounts.view", icon: "accounts" },
      { to: "/admin/social/analytics", label: "Analytics", perm: "analytics.view", icon: "analytics" },
      { to: "/admin/social/inbox", label: "Inbox", perm: "social_accounts.view", icon: "inbox" },
      { to: "/admin/social/health", label: "Integration Health", perm: "social_accounts.view", icon: "health" },
    ],
  },
  {
    section: "Marketing",
    items: [
      { to: "/admin/campaigns", label: "Campaigns", perm: "campaigns.view", icon: "campaigns" },
      { to: "/admin/analytics", label: "Marketing Analytics", perm: "analytics.view", icon: "analytics" },
    ],
  },
  {
    section: "Audience",
    items: [
      { to: "/admin/enquiries", label: "Leads", perm: "leads.view", icon: "leads" },
      { to: "/admin/demos", label: "Demo Requests", perm: "demo_requests.view", icon: "demos" },
      { to: "/admin/subscribers", label: "Subscribers", perm: "subscribers.view", icon: "subscribers" },
    ],
  },
  {
    section: "Website",
    items: [
      { to: "/admin/products", label: "Products", perm: "products.view", icon: "products" },
      { to: "/admin/blog", label: "Blog", perm: "blog.view", icon: "blog" },
    ],
  },
  {
    section: "People",
    items: [
      { to: "/admin/positions", label: "Careers", perm: "careers.view", icon: "careers" },
      { to: "/admin/applications", label: "Applications", perm: "careers.view", icon: "applications" },
    ],
  },
  {
    section: "Administration",
    items: [
      { to: "/admin/users", label: "Users", perm: "users.view", icon: "users" },
      { to: "/admin/roles", label: "Roles & Permissions", perm: "roles.view", icon: "roles" },
      { to: "/admin/integrations", label: "Integrations", perm: "integrations.view", icon: "integrations" },
      { to: "/admin/settings", label: "Settings", perm: "settings.view", icon: "settings" },
      { to: "/admin/activity-logs", label: "Activity Logs", perm: "audit.view", icon: "audit" },
    ],
  },
];

const TITLES = {
  "/admin": ["Dashboard", "Monitor products, campaigns, content and leads from one command centre."],
  "/admin/pulse": ["Marketing Pulse", "Open alerts and health signals across products and campaigns."],
  "/admin/tasks": ["Tasks", "Operational follow-ups for the marketing team."],
  "/admin/campaigns": ["Campaigns", "Plan and track marketing campaigns."],
  "/admin/content": ["Content Library", "Draft, review and publish content."],
  "/admin/content/calendar": ["Content Calendar", "Scheduled and published content timeline."],
  "/admin/content/approvals": ["Approvals", "Items waiting for review."],
  "/admin/social-accounts": ["Social Accounts", "Connected social destinations."],
  "/admin/analytics": ["Analytics", "Overview of leads, campaigns and content."],
  "/admin/enquiries": ["Leads", "Website enquiries and lead pipeline."],
  "/admin/demos": ["Demo Requests", "Demo pipeline and assignees."],
  "/admin/subscribers": ["Subscribers", "Newsletter subscribers."],
  "/admin/products": ["Products", "Website product catalogue."],
  "/admin/blog": ["Blog", "Public blog posts."],
  "/admin/positions": ["Careers", "Open positions."],
  "/admin/applications": ["Applications", "Job application pipeline."],
  "/admin/users": ["Users", "Team members and access scopes."],
  "/admin/roles": ["Roles & Permissions", "Role and permission matrix."],
  "/admin/integrations": ["Integrations", "Buffer, messaging and analytics connections."],
  "/admin/settings": ["Settings", "Site and marketing settings."],
  "/admin/activity-logs": ["Activity Logs", "Audit trail."],
  "/admin/profile": ["Profile", "Your account."],
};

export default function AdminLayout() {
  const { user, logout, can } = useAuth();
  const navigate = useNavigate();
  const location = useLocation();
  const [sidebarOpen, setSidebarOpen] = useState(false);
  const [collapsed, setCollapsed] = useState(false);

  const isSocialWorkspace = location.pathname.startsWith("/admin/social");

  const [title, description] = useMemo(() => {
    const exact = TITLES[location.pathname];
    if (exact) return exact;
    const key = Object.keys(TITLES).find((k) => k !== "/admin" && location.pathname.startsWith(k));
    return key ? TITLES[key] : ["Admin", "Marketing Command Centre"];
  }, [location.pathname]);

  const initials = (user?.name || "PT")
    .split(" ")
    .map((p) => p[0])
    .join("")
    .slice(0, 2)
    .toUpperCase();

  async function onLogout() {
    await logout();
    navigate("/login");
  }

  return (
    <div className={`admin-shell ${collapsed ? "is-collapsed" : ""}`} data-theme="prady-forest">
      {sidebarOpen && (
        <button
          type="button"
          className="fixed inset-0 z-30 bg-black/50 lg:hidden"
          aria-label="Close sidebar"
          onClick={() => setSidebarOpen(false)}
        />
      )}

      <aside
        className={`admin-sidebar ${sidebarOpen ? "translate-x-0" : "-translate-x-full"} lg:translate-x-0 ${
          collapsed ? "lg:hidden" : ""
        }`}
      >
        <div className="flex items-start justify-between gap-2">
          <Link to="/" className="admin-sidebar__brand">
            <AdminBrandMark />
            <span className="admin-sidebar__brand-text">
              <strong>Prady</strong>
              <span>Technologies Ltd</span>
            </span>
          </Link>
          <button
            type="button"
            className="admin-icon-btn hidden lg:inline-flex"
            style={{ background: "rgba(255,255,255,0.08)", borderColor: "rgba(255,255,255,0.15)", color: "#fff" }}
            onClick={() => setCollapsed(true)}
            title="Hide sidebar"
          >
            «
          </button>
          <button
            type="button"
            className="admin-icon-btn lg:hidden"
            style={{ background: "rgba(255,255,255,0.08)", borderColor: "rgba(255,255,255,0.15)", color: "#fff" }}
            onClick={() => setSidebarOpen(false)}
          >
            ✕
          </button>
        </div>
        <p className="admin-sidebar__tagline">Doing it differently</p>

        <nav className="admin-sidebar__nav">
          {NAV.map((group) => {
            const items = group.items.filter((i) => can(i.perm) || can("careers.manage"));
            if (!items.length) return null;
            return (
              <div key={group.section} className="admin-nav-section">
                <p className="admin-nav-section__label">{group.section}</p>
                <div className="space-y-1">
                  {items.map((item) => (
                    <NavLink
                      key={item.to}
                      to={item.to}
                      end={item.end || item.to === "/admin/social"}
                      className={({ isActive }) => `nav-pill ${isActive ? "nav-pill--active" : ""}`}
                      onClick={() => setSidebarOpen(false)}
                    >
                      <NavIcon name={item.icon} />
                      <span>{item.label}</span>
                    </NavLink>
                  ))}
                </div>
              </div>
            );
          })}
        </nav>

        <div className="admin-sidebar__foot">
          <p>© {new Date().getFullYear()} Prady Technologies Ltd</p>
          <p>Marketing Command Centre</p>
        </div>
      </aside>

      {collapsed && (
        <button
          type="button"
          className="admin-float-toggle hidden lg:flex"
          onClick={() => setCollapsed(false)}
          title="Show sidebar"
        >
          ☰
        </button>
      )}

      <div className="admin-main">
        <header className="admin-header">
          <div className="admin-header__bar">
            <button type="button" className="btn-ghost lg:hidden" onClick={() => setSidebarOpen(true)}>
              Menu
            </button>

            <form
              className="admin-search"
              onSubmit={(e) => {
                e.preventDefault();
                const q = new FormData(e.currentTarget).get("q");
                if (q) navigate(`/admin/enquiries?q=${encodeURIComponent(String(q))}`);
              }}
            >
              <span aria-hidden="true">⌕</span>
              <input name="q" placeholder="Search leads, content, users..." />
            </form>

            <div className="admin-header__actions">
              <button type="button" className="admin-icon-btn" title="Notifications" aria-label="Notifications">
                🔔
                <span className="admin-icon-btn__badge">3</span>
              </button>
              <div className="admin-user-chip">
                <span className="admin-user-chip__avatar">{initials}</span>
                <span className="admin-user-chip__meta">
                  <strong>{user?.name || "User"}</strong>
                  <span>{user?.role?.replaceAll("_", " ") || "Team"}</span>
                </span>
              </div>
              <Link to="/admin/enquiries" className="btn-ghost hidden md:inline-flex">
                Review leads
              </Link>
              <Link to="/admin/blog" className="btn-primary hidden sm:inline-flex">
                + Create update
              </Link>
              <button type="button" className="btn-ghost" onClick={onLogout}>
                Logout
              </button>
            </div>
          </div>
        </header>

        {!isSocialWorkspace && (
          <div className="admin-page-head">
            <span className="admin-page-head__eyebrow">
              <span style={{ width: 8, height: 8, borderRadius: 99, background: "#43a047" }} />
              Live sync
            </span>
            <h1>{title}</h1>
            <p>{description}</p>
          </div>
        )}

        <main className="admin-content">
          <Outlet />
        </main>
      </div>
    </div>
  );
}
