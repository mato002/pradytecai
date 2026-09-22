import React, { useEffect, useMemo, useState } from "react";
import { getMockScheduledPosts } from "../../../data/socialMockData";
import SocialPlatformIcon from "../../../components/social/SocialPlatformIcon";
import { CalendarPostCard } from "../../../components/social/SocialCards";
import {
  PostStatusBadge,
  SocialDrawer,
  SocialEmptyState,
  SocialFilterBar,
  SocialPageHeader,
  SocialSelect,
  SocialSkeleton,
  SocialToast,
} from "../../../components/social/SocialShared";

const MONTH_LABEL = "September 2026";
const DAYS_IN_MONTH = 30;
const START_WEEKDAY = 1; // Tue for Sep 1 2026... wait Sep 1 2026 is Tuesday. For calendar grid we need offset.

function buildMonthCells() {
  // September 2026 starts on Tuesday (1)
  const offset = 1;
  const cells = [];
  for (let i = 0; i < offset; i += 1) cells.push(null);
  for (let d = 1; d <= DAYS_IN_MONTH; d += 1) cells.push(d);
  return cells;
}

export default function SocialCalendar() {
  const [loading, setLoading] = useState(true);
  const [view, setView] = useState(() => (typeof window !== "undefined" && window.innerWidth < 768 ? "list" : "month"));
  const [posts, setPosts] = useState([]);
  const [selected, setSelected] = useState(null);
  const [toast, setToast] = useState("");
  const [filters, setFilters] = useState({
    account: "all",
    platform: "all",
    product: "all",
    campaign: "all",
    status: "all",
  });

  useEffect(() => {
    const t = setTimeout(() => {
      setPosts(getMockScheduledPosts());
      setLoading(false);
    }, 400);
    return () => clearTimeout(t);
  }, []);

  const filtered = useMemo(() => {
    return posts.filter((p) => {
      if (filters.platform !== "all" && p.platform !== filters.platform) return false;
      if (filters.product !== "all" && p.productId !== filters.product) return false;
      if (filters.campaign !== "all" && p.campaignId !== filters.campaign) return false;
      if (filters.status !== "all" && p.status !== filters.status) return false;
      return true;
    });
  }, [posts, filters]);

  const byDate = useMemo(() => {
    const map = {};
    filtered.forEach((p) => {
      const day = Number(p.date.split("-")[2]);
      if (!map[day]) map[day] = [];
      map[day].push(p);
    });
    return map;
  }, [filtered]);

  function act(msg) {
    setToast(msg);
    setTimeout(() => setToast(""), 2200);
  }

  const cells = buildMonthCells();
  const agendaDates = Object.keys(byDate)
    .map(Number)
    .sort((a, b) => a - b);

  if (loading) {
    return (
      <div className="social-workspace">
        <SocialPageHeader title="Calendar" description="Plan and review scheduled publishing across channels." />
        <SocialSkeleton variant="calendar" count={1} />
      </div>
    );
  }

  return (
    <div className="social-workspace">
      <SocialPageHeader title="Calendar" description="Plan and review scheduled publishing across channels." />

      <div className="social-cal-toolbar">
        <div className="social-cal-toolbar__nav">
          <button type="button" className="btn-ghost" aria-label="Previous month" onClick={() => act("Previous month (mock)")}>
            ‹
          </button>
          <h2>{MONTH_LABEL}</h2>
          <button type="button" className="btn-ghost" aria-label="Next month" onClick={() => act("Next month (mock)")}>
            ›
          </button>
          <button type="button" className="btn-ghost" onClick={() => act("Jumped to today (mock)")}>
            Today
          </button>
        </div>
        <div className="social-cal-toolbar__views" role="group" aria-label="Calendar view">
          {["month", "week", "list"].map((v) => (
            <button
              key={v}
              type="button"
              className={`btn-ghost ${view === v ? "is-active-toggle" : ""}`}
              onClick={() => setView(v)}
            >
              {v.charAt(0).toUpperCase() + v.slice(1)}
            </button>
          ))}
        </div>
      </div>

      <SocialFilterBar>
        <SocialSelect
          label="All accounts"
          value={filters.account}
          onChange={(v) => setFilters((f) => ({ ...f, account: v }))}
          options={[
            { value: "all", label: "All accounts" },
            { value: "acc-ig", label: "Instagram @pradytec" },
            { value: "acc-li", label: "LinkedIn Prady Technologies" },
          ]}
        />
        <SocialSelect
          label="All platforms"
          value={filters.platform}
          onChange={(v) => setFilters((f) => ({ ...f, platform: v }))}
          options={[
            { value: "all", label: "All platforms" },
            { value: "instagram", label: "Instagram" },
            { value: "facebook", label: "Facebook" },
            { value: "linkedin", label: "LinkedIn" },
            { value: "tiktok", label: "TikTok" },
            { value: "youtube", label: "YouTube" },
          ]}
        />
        <SocialSelect
          label="All products"
          value={filters.product}
          onChange={(v) => setFilters((f) => ({ ...f, product: v }))}
          options={[
            { value: "all", label: "All products" },
            { value: "mfi", label: "Microfinance" },
            { value: "fleet", label: "Fleet" },
            { value: "sms", label: "Bulk SMS" },
          ]}
        />
        <SocialSelect
          label="All campaigns"
          value={filters.campaign}
          onChange={(v) => setFilters((f) => ({ ...f, campaign: v }))}
          options={[
            { value: "all", label: "All campaigns" },
            { value: "mfi-awareness", label: "MFI Awareness" },
            { value: "fleet-launch", label: "Fleet Launch" },
          ]}
        />
        <SocialSelect
          label="All statuses"
          value={filters.status}
          onChange={(v) => setFilters((f) => ({ ...f, status: v }))}
          options={[
            { value: "all", label: "All statuses" },
            { value: "Draft", label: "Draft" },
            { value: "In review", label: "In review" },
            { value: "Approved", label: "Approved" },
            { value: "Scheduled", label: "Scheduled" },
            { value: "Published", label: "Published" },
            { value: "Failed", label: "Failed" },
          ]}
        />
      </SocialFilterBar>

      {!filtered.length ? (
        <SocialEmptyState
          title="Nothing scheduled yet"
          description="Create your first post or add content to your queue."
          actionLabel="Create post"
          to="/admin/social/create"
        />
      ) : view === "month" ? (
        <div className="social-cal-month admin-panel">
          <div className="social-cal-month__weekdays">
            {["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"].map((d) => (
              <span key={d}>{d}</span>
            ))}
          </div>
          <div className="social-cal-month__grid">
            {cells.map((day, idx) => (
              <div key={idx} className={`social-cal-day ${day ? "" : "is-empty"}`}>
                {day && (
                  <>
                    <span className="social-cal-day__num">{day}</span>
                    <div className="social-cal-day__posts">
                      {(byDate[day] || []).map((p) => (
                        <CalendarPostCard key={p.id} post={p} onClick={setSelected} />
                      ))}
                    </div>
                  </>
                )}
              </div>
            ))}
          </div>
        </div>
      ) : (
        <div className="social-cal-agenda admin-panel">
          {agendaDates.map((day) => (
            <div key={day} className="social-cal-agenda__group">
              <h3>
                {day === 22 ? "MON 22" : day === 23 ? "TUE 23" : day === 24 ? "WED 24" : `SEP ${day}`}
              </h3>
              <ul>
                {(byDate[day] || []).map((p) => (
                  <li key={p.id}>
                    <button type="button" className="social-cal-agenda__item" onClick={() => setSelected(p)}>
                      <span>{p.time}</span>
                      <SocialPlatformIcon platform={p.platform} size={16} />
                      <span>{p.title}</span>
                      <PostStatusBadge status={p.status} />
                    </button>
                  </li>
                ))}
              </ul>
            </div>
          ))}
        </div>
      )}

      <SocialDrawer
        open={!!selected}
        onClose={() => setSelected(null)}
        title={selected?.title || "Post"}
        footer={
          selected && (
            <>
              <button type="button" className="btn-ghost" onClick={() => act("Edit (mock)")}>
                Edit
              </button>
              <button type="button" className="btn-ghost" onClick={() => act("Duplicated (mock)")}>
                Duplicate
              </button>
              <button type="button" className="btn-ghost" onClick={() => act("Reschedule (mock)")}>
                Reschedule
              </button>
              <button type="button" className="btn-ghost" onClick={() => act("Cancelled (mock)")}>
                Cancel
              </button>
            </>
          )
        }
      >
        {selected && (
          <div className="social-drawer-detail">
            <p>{selected.caption}</p>
            <dl>
              <div>
                <dt>Platform</dt>
                <dd>
                  <SocialPlatformIcon platform={selected.platform} size={14} /> {selected.platform}
                </dd>
              </div>
              <div>
                <dt>Account</dt>
                <dd>{selected.accountId}</dd>
              </div>
              <div>
                <dt>Product</dt>
                <dd>{selected.product}</dd>
              </div>
              <div>
                <dt>Campaign</dt>
                <dd>{selected.campaign}</dd>
              </div>
              <div>
                <dt>Scheduled</dt>
                <dd>
                  {selected.date} · {selected.time} · Africa/Nairobi
                </dd>
              </div>
              <div>
                <dt>Status</dt>
                <dd>
                  <PostStatusBadge status={selected.status} />
                </dd>
              </div>
            </dl>
          </div>
        )}
      </SocialDrawer>

      <SocialToast message={toast} onDismiss={() => setToast("")} />
    </div>
  );
}
