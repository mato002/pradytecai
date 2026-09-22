import React, { useEffect, useState } from "react";
import { Link } from "react-router-dom";
import { api } from "../../api/client";

export default function Dashboard() {
  const [data, setData] = useState(null);
  const [analytics, setAnalytics] = useState(null);
  const [pulse, setPulse] = useState([]);

  useEffect(() => {
    api("/dashboard/").then(setData).catch(() => setData(null));
    api("/analytics/overview/").then(setAnalytics).catch(() => setAnalytics(null));
    api("/pulse/")
      .then((res) => setPulse(res.results || res || []))
      .catch(() => setPulse([]));
  }, []);

  const cards = [
    {
      label: "New leads",
      value: data?.leads_new,
      trend: "Needs attention",
      trendClass: "admin-kpi__trend--down",
      iconClass: "admin-kpi__icon--danger",
      icon: "!",
    },
    {
      label: "Draft content",
      value: data?.content_draft,
      trend: "In pipeline",
      trendClass: "admin-kpi__trend--up",
      iconClass: "admin-kpi__icon--warn",
      icon: "✎",
    },
    {
      label: "Total leads",
      value: analytics?.leads,
      trend: "Audience growth",
      trendClass: "admin-kpi__trend--up",
      iconClass: "admin-kpi__icon--ok",
      icon: "✓",
    },
    {
      label: "Open alerts",
      value: analytics?.open_alerts,
      trend: "Pulse signals",
      trendClass: analytics?.open_alerts ? "admin-kpi__trend--down" : "admin-kpi__trend--up",
      iconClass: "admin-kpi__icon--forest",
      icon: "●",
    },
  ];

  return (
    <div className="space-y-5">
      <div className="admin-kpi-grid">
        {cards.map((c) => (
          <article key={c.label} className="admin-kpi">
            <div className="admin-kpi__top">
              <div>
                <p className="admin-kpi__label" style={{ marginTop: 0 }}>
                  {c.label}
                </p>
                <p className="admin-kpi__value">{c.value ?? "—"}</p>
                <p className={`admin-kpi__trend ${c.trendClass}`}>{c.trend}</p>
              </div>
              <span className={`admin-kpi__icon ${c.iconClass}`}>{c.icon}</span>
            </div>
          </article>
        ))}
      </div>

      <div className="grid gap-4 xl:grid-cols-[1.4fr_1fr]">
        <section className="admin-panel">
          <div className="admin-panel__head">
            <h2>Marketing Pulse</h2>
            <Link to="/admin/pulse">View all →</Link>
          </div>
          <div className="p-4 space-y-3">
            {(Array.isArray(pulse) ? pulse : []).slice(0, 5).map((alert) => (
              <div
                key={alert.id}
                className="flex items-start justify-between gap-3 rounded-xl border border-[var(--admin-border)] bg-[#fbfcfb] px-3 py-3"
              >
                <div>
                  <p className="font-semibold text-[var(--admin-forest)]">{alert.title || alert.rule_key}</p>
                  <p className="mt-1 text-sm text-[var(--admin-muted)] line-clamp-2">{alert.message}</p>
                </div>
                <span
                  className={`admin-status ${
                    alert.severity === "critical" ? "admin-status--danger" : "admin-status--warn"
                  }`}
                >
                  {alert.severity || "open"}
                </span>
              </div>
            ))}
            {!pulse?.length && (
              <p className="text-sm text-[var(--admin-muted)] px-1 py-6 text-center">
                No open pulse alerts. Systems look healthy.
              </p>
            )}
          </div>
        </section>

        <section className="admin-panel">
          <div className="admin-panel__head">
            <h2>Quick actions</h2>
          </div>
          <div className="p-4 grid gap-3">
            <Link to="/admin/enquiries" className="btn-primary justify-center">
              Review leads
            </Link>
            <Link to="/admin/content" className="btn-ghost justify-center">
              Open content library
            </Link>
            <Link to="/admin/campaigns" className="btn-ghost justify-center">
              Manage campaigns
            </Link>
            <Link to="/admin/analytics" className="btn-ghost justify-center">
              View analytics
            </Link>
          </div>
          <div className="px-4 pb-4">
            <div
              className="rounded-xl px-4 py-5 text-white"
              style={{ background: "linear-gradient(135deg, #004d40 0%, #0a5c4a 55%, #c9a227 160%)" }}
            >
              <p className="text-sm opacity-90">Your lending partner energy — applied to marketing ops</p>
              <p className="mt-2 text-lg font-semibold">Doing it differently</p>
            </div>
          </div>
        </section>
      </div>
    </div>
  );
}
