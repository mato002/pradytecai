import React, { useEffect, useMemo, useState } from "react";
import { getMockApprovals } from "../../../data/socialMockData";
import SocialPlatformIcon from "../../../components/social/SocialPlatformIcon";
import {
  PostStatusBadge,
  SocialEmptyState,
  SocialPageHeader,
  SocialSkeleton,
  SocialToast,
} from "../../../components/social/SocialShared";

const TABS = ["Pending", "Approved", "Changes requested"];

export default function SocialApprovals() {
  const [loading, setLoading] = useState(true);
  const [items, setItems] = useState([]);
  const [tab, setTab] = useState("Pending");
  const [toast, setToast] = useState("");

  useEffect(() => {
    const t = setTimeout(() => {
      setItems(getMockApprovals());
      setLoading(false);
    }, 350);
    return () => clearTimeout(t);
  }, []);

  const filtered = useMemo(() => items.filter((i) => i.status === tab), [items, tab]);

  function act(id, nextStatus, msg) {
    setItems((prev) => prev.map((i) => (i.id === id ? { ...i, status: nextStatus } : i)));
    setToast(msg);
    setTimeout(() => setToast(""), 2200);
  }

  return (
    <div className="social-workspace">
      <SocialPageHeader title="Approvals" description="Review posts waiting for marketing sign-off." />

      <div className="social-tabs" role="tablist" aria-label="Approval status">
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

      {loading ? (
        <SocialSkeleton variant="list" count={3} />
      ) : !filtered.length ? (
        <SocialEmptyState title="You're all caught up" description="There are no posts waiting for approval." />
      ) : (
        <div className="social-approval-list">
          {filtered.map((item) => (
            <article key={item.id} className="social-approval-card admin-panel">
              <div className="social-approval-card__head">
                <div>
                  <p className="social-approval-card__eyebrow">Waiting for approval</p>
                  <h3>{item.title}</h3>
                </div>
                <PostStatusBadge status={item.status === "Pending" ? "In review" : item.status} />
              </div>
              <p className="social-approval-card__caption">{item.caption}</p>
              <div className="social-approval-card__platforms">
                {item.platforms.map((p) => (
                  <span key={p}>
                    <SocialPlatformIcon platform={p} size={14} />
                    {p.charAt(0).toUpperCase() + p.slice(1)}
                  </span>
                ))}
              </div>
              <dl className="social-approval-card__meta">
                <div>
                  <dt>Created by</dt>
                  <dd>{item.createdBy}</dd>
                </div>
                <div>
                  <dt>Submitted</dt>
                  <dd>{item.submittedAt}</dd>
                </div>
                <div>
                  <dt>Product</dt>
                  <dd>{item.product}</dd>
                </div>
              </dl>
              {item.changeNote && <p className="social-approval-card__note">{item.changeNote}</p>}
              <div className="social-approval-card__actions">
                <button type="button" className="btn-ghost" onClick={() => act(item.id, item.status, "Preview (mock)")}>
                  Preview
                </button>
                {tab === "Pending" && (
                  <>
                    <button
                      type="button"
                      className="btn-ghost"
                      onClick={() => act(item.id, "Changes requested", "Changes requested (mock)")}
                    >
                      Request changes
                    </button>
                    <button
                      type="button"
                      className="btn-primary"
                      onClick={() => act(item.id, "Approved", "Approved (mock)")}
                    >
                      Approve
                    </button>
                  </>
                )}
              </div>
            </article>
          ))}
        </div>
      )}

      <SocialToast message={toast} onDismiss={() => setToast("")} />
    </div>
  );
}
