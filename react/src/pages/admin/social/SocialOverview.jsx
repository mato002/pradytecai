import React, { useEffect, useMemo, useState } from "react";
import { Link } from "react-router-dom";
import {
  getMockActivity,
  getMockOverviewKpis,
  getMockPerformanceSeries,
  getMockRecentPerformance,
  getMockScheduledPosts,
  getMockSocialAccounts,
  getMockTodaysPublishing,
  getMockUpcomingPosts,
} from "../../../data/socialMockData";
import SocialPlatformIcon from "../../../components/social/SocialPlatformIcon";
import {
  ConnectionHealthBadge,
  MiniSparkline,
  PostStatusBadge,
  SocialPageHeader,
  SocialSkeleton,
  SocialToast,
} from "../../../components/social/SocialShared";
import { FailedPostBanner } from "../../../components/social/SocialCards";

export default function SocialOverview() {
  const [loading, setLoading] = useState(true);
  const [toast, setToast] = useState("");
  const [posts, setPosts] = useState([]);

  useEffect(() => {
    const t = setTimeout(() => {
      setPosts(getMockTodaysPublishing());
      setLoading(false);
    }, 450);
    return () => clearTimeout(t);
  }, []);

  const kpis = getMockOverviewKpis();
  const accounts = getMockSocialAccounts().filter((a) => a.status !== "Disconnected");
  const upcoming = getMockUpcomingPosts();
  const perf = getMockRecentPerformance();
  const series = getMockPerformanceSeries();
  const activity = getMockActivity();
  const failed = useMemo(() => getMockScheduledPosts().find((p) => p.status === "Failed"), []);

  function act(msg) {
    setToast(msg);
    setTimeout(() => setToast(""), 2400);
  }

  const upcomingGroups = useMemo(() => {
    const map = {};
    upcoming.forEach((p) => {
      const key =
        p.date === "2026-09-22" ? "Today" : p.date === "2026-09-23" ? "Tomorrow" : p.date === "2026-09-24" ? "Thursday" : p.date;
      if (!map[key]) map[key] = [];
      map[key].push(p);
    });
    return Object.entries(map);
  }, [upcoming]);

  if (loading) {
    return (
      <div className="social-workspace">
        <SocialPageHeader
          title="Social Media"
          description="Manage publishing, scheduling, engagement and performance across all Pradytec social channels."
        />
        <SocialSkeleton variant="kpi" count={6} />
        <SocialSkeleton variant="panel" count={2} />
      </div>
    );
  }

  return (
    <div className="social-workspace">
      <SocialPageHeader
        title="Social Media"
        description="Manage publishing, scheduling, engagement and performance across all Pradytec social channels."
      />

      {failed && (
        <FailedPostBanner
          post={failed}
          onRetry={() => act("Retry queued (mock)")}
          onEdit={() => act("Opening editor (mock)")}
          onViewAccount={() => act("Opening account (mock)")}
        />
      )}

      <div className="admin-kpi-grid">
        {kpis.map((k) => (
          <article key={k.id} className="admin-kpi">
            <p className="admin-kpi__label" style={{ marginTop: 0 }}>
              {k.label}
            </p>
            <p className="admin-kpi__value">{k.value}</p>
            {k.tone === "warn" && <p className="admin-kpi__trend admin-kpi__trend--down">Needs review</p>}
          </article>
        ))}
      </div>

      <section className="admin-panel">
        <div className="admin-panel__head">
          <h2>Account health</h2>
          <Link to="/admin/social/health">Integration health →</Link>
        </div>
        <div className="social-health-row">
          {accounts.map((a) => (
            <div key={a.id} className="social-health-row__item">
              <SocialPlatformIcon platform={a.platform} size={18} />
              <span className="social-health-row__name">
                {a.platform.charAt(0).toUpperCase() + a.platform.slice(1)}
              </span>
              <ConnectionHealthBadge status={a.status} />
            </div>
          ))}
        </div>
      </section>

      <div className="social-overview-grid">
        <section className="admin-panel">
          <div className="admin-panel__head">
            <h2>Today&apos;s publishing</h2>
          </div>
          <div className="social-today-list">
            {posts.map((p) => (
              <div key={p.id} className="social-today-row">
                <span className="social-today-row__time">{p.time}</span>
                <SocialPlatformIcon platform={p.platform} size={16} />
                <span className="social-today-row__platform">
                  {p.platform.charAt(0).toUpperCase() + p.platform.slice(1)}
                </span>
                <span className="social-today-row__title">{p.title}</span>
                <PostStatusBadge status={p.status} />
                <div className="social-today-row__actions">
                  <button type="button" className="btn-ghost" onClick={() => act(`Preview: ${p.title}`)}>
                    Preview
                  </button>
                  <button type="button" className="btn-ghost" onClick={() => act(`Edit: ${p.title}`)}>
                    Edit
                  </button>
                  <button type="button" className="btn-ghost" onClick={() => act(`Duplicated: ${p.title}`)}>
                    Duplicate
                  </button>
                  <button type="button" className="btn-ghost" onClick={() => act(`Reschedule: ${p.title}`)}>
                    Reschedule
                  </button>
                </div>
              </div>
            ))}
          </div>
        </section>

        <section className="admin-panel">
          <div className="admin-panel__head">
            <h2>Upcoming</h2>
            <Link to="/admin/social/calendar">View calendar →</Link>
          </div>
          <div className="social-upcoming">
            {upcomingGroups.map(([day, items]) => (
              <div key={day} className="social-upcoming__group">
                <h3>{day}</h3>
                <ul>
                  {items.map((p) => (
                    <li key={p.id}>
                      <SocialPlatformIcon platform={p.platform} size={14} />
                      <span>{p.platform.charAt(0).toUpperCase() + p.platform.slice(1)}</span>
                      <span className="social-upcoming__time">{p.time}</span>
                    </li>
                  ))}
                </ul>
              </div>
            ))}
          </div>
        </section>
      </div>

      <div className="social-overview-grid">
        <section className="admin-panel">
          <div className="admin-panel__head">
            <h2>Recent performance</h2>
            <Link to="/admin/social/analytics">Full analytics →</Link>
          </div>
          <div className="social-perf-panel">
            <ul className="social-perf-stats">
              {perf.map((row) => (
                <li key={row.label}>
                  <span>{row.label}</span>
                  <strong>{row.value}</strong>
                  <em>{row.delta}</em>
                </li>
              ))}
            </ul>
            <MiniSparkline series={series} />
          </div>
        </section>

        <section className="admin-panel">
          <div className="admin-panel__head">
            <h2>Recent activity</h2>
            <Link to="/admin/social/health">View health →</Link>
          </div>
          <ul className="social-activity-list">
            {activity.map((a) => (
              <li key={a.id}>
                <span className="social-activity-list__when">{a.when}</span>
                <span className="social-activity-list__time">{a.time}</span>
                <span>{a.label}</span>
              </li>
            ))}
          </ul>
        </section>
      </div>

      <SocialToast message={toast} onDismiss={() => setToast("")} />
    </div>
  );
}
