import React, { useEffect, useMemo, useState } from "react";
import { getMockInboxThreads } from "../../../data/socialMockData";
import SocialPlatformIcon from "../../../components/social/SocialPlatformIcon";
import {
  SocialEmptyState,
  SocialFilterBar,
  SocialPageHeader,
  SocialSelect,
  SocialSkeleton,
  SocialToast,
} from "../../../components/social/SocialShared";

const TABS = ["All", "Comments", "Messages", "Mentions"];

export default function SocialInbox() {
  const [loading, setLoading] = useState(true);
  const [threads, setThreads] = useState([]);
  const [tab, setTab] = useState("All");
  const [filter, setFilter] = useState({ platform: "all", unread: false, assigned: false });
  const [activeId, setActiveId] = useState(null);
  const [reply, setReply] = useState("");
  const [toast, setToast] = useState("");
  const [mobileShowDetail, setMobileShowDetail] = useState(false);

  useEffect(() => {
    const t = setTimeout(() => {
      const data = getMockInboxThreads();
      setThreads(data);
      setActiveId(data[0]?.id || null);
      setLoading(false);
    }, 400);
    return () => clearTimeout(t);
  }, []);

  const filtered = useMemo(() => {
    return threads.filter((th) => {
      if (tab !== "All" && th.type !== tab) return false;
      if (filter.platform !== "all" && th.platform !== filter.platform) return false;
      if (filter.unread && !th.unread) return false;
      if (filter.assigned && !th.assignedToMe) return false;
      return true;
    });
  }, [threads, tab, filter]);

  const active = filtered.find((t) => t.id === activeId) || filtered[0] || null;

  function act(msg) {
    setToast(msg);
    setTimeout(() => setToast(""), 2200);
  }

  function openThread(id) {
    setActiveId(id);
    setMobileShowDetail(true);
    setThreads((prev) => prev.map((t) => (t.id === id ? { ...t, unread: false } : t)));
  }

  return (
    <div className="social-workspace">
      <SocialPageHeader
        title="Inbox"
        description="Comments, messages and mentions across connected channels. UI preview only."
        showCreate={false}
      />

      <div className="social-tabs" role="tablist" aria-label="Inbox type">
        {TABS.map((t) => (
          <button
            key={t}
            type="button"
            role="tab"
            aria-selected={tab === t}
            className={tab === t ? "is-active" : ""}
            onClick={() => setTab(t)}
          >
            {t}
          </button>
        ))}
      </div>

      <SocialFilterBar>
        <SocialSelect
          label="All platforms"
          value={filter.platform}
          onChange={(v) => setFilter((f) => ({ ...f, platform: v }))}
          options={[
            { value: "all", label: "All platforms" },
            { value: "instagram", label: "Instagram" },
            { value: "facebook", label: "Facebook" },
            { value: "linkedin", label: "LinkedIn" },
            { value: "x", label: "X" },
          ]}
        />
        <label className="social-check social-check--inline">
          <input
            type="checkbox"
            checked={filter.unread}
            onChange={(e) => setFilter((f) => ({ ...f, unread: e.target.checked }))}
          />
          Unread
        </label>
        <label className="social-check social-check--inline">
          <input
            type="checkbox"
            checked={filter.assigned}
            onChange={(e) => setFilter((f) => ({ ...f, assigned: e.target.checked }))}
          />
          Assigned to me
        </label>
      </SocialFilterBar>

      {loading ? (
        <SocialSkeleton variant="inbox" count={1} />
      ) : !filtered.length ? (
        <SocialEmptyState title="Inbox is empty" description="No conversations match your filters." />
      ) : (
        <div className={`social-inbox ${mobileShowDetail ? "is-detail" : ""}`}>
          <div className="social-inbox__list admin-panel">
            <div className="admin-panel__head">
              <h2>Conversations</h2>
            </div>
            <ul>
              {filtered.map((th) => (
                <li key={th.id}>
                  <button
                    type="button"
                    className={`social-inbox__item ${active?.id === th.id ? "is-active" : ""} ${
                      th.unread ? "is-unread" : ""
                    }`}
                    onClick={() => openThread(th.id)}
                  >
                    <SocialPlatformIcon platform={th.platform} size={16} />
                    <div>
                      <strong>{th.name}</strong>
                      <p>{th.preview}</p>
                    </div>
                    <span>{th.time}</span>
                  </button>
                </li>
              ))}
            </ul>
          </div>

          <div className="social-inbox__detail admin-panel">
            {active ? (
              <>
                <div className="social-inbox__detail-head">
                  <button
                    type="button"
                    className="btn-ghost social-inbox__back"
                    onClick={() => setMobileShowDetail(false)}
                  >
                    ← Back
                  </button>
                  <SocialPlatformIcon platform={active.platform} size={18} />
                  <div>
                    <strong>{active.name}</strong>
                    <span>
                      {active.platform.charAt(0).toUpperCase() + active.platform.slice(1)} · {active.type}
                    </span>
                  </div>
                </div>

                {active.postContext && (
                  <div className="social-inbox__post-context">
                    <span>Post</span>
                    <p>&ldquo;{active.postContext}&rdquo;</p>
                  </div>
                )}

                <div className="social-inbox__messages">
                  {active.messages.map((m) => (
                    <div key={m.id} className={`social-inbox__bubble ${m.mine ? "is-mine" : ""}`}>
                      <strong>{m.from}</strong>
                      <p>{m.body}</p>
                      <span>{m.time}</span>
                    </div>
                  ))}
                </div>

                <div className="social-inbox__reply">
                  <label className="sr-only" htmlFor="inbox-reply">
                    Reply
                  </label>
                  <textarea
                    id="inbox-reply"
                    rows={3}
                    value={reply}
                    onChange={(e) => setReply(e.target.value)}
                    placeholder="Write a reply…"
                  />
                  <div className="social-inbox__actions">
                    <button
                      type="button"
                      className="btn-primary"
                      onClick={() => {
                        act("Reply sent (mock)");
                        setReply("");
                      }}
                    >
                      Reply
                    </button>
                    <button type="button" className="btn-ghost" onClick={() => act("Assigned (mock)")}>
                      Assign
                    </button>
                    <button type="button" className="btn-ghost" onClick={() => act("Marked resolved (mock)")}>
                      Mark resolved
                    </button>
                    <button type="button" className="btn-ghost" onClick={() => act("Create lead (mock)")}>
                      Create lead
                    </button>
                  </div>
                </div>
              </>
            ) : (
              <p className="social-muted p-6">Select a conversation</p>
            )}
          </div>
        </div>
      )}

      <SocialToast message={toast} onDismiss={() => setToast("")} />
    </div>
  );
}
