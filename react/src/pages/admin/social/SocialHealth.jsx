import React, { useEffect, useState } from "react";
import { Link } from "react-router-dom";
import { getMockActivity, getMockIntegrationHealth } from "../../../data/socialMockData";
import SocialPlatformIcon from "../../../components/social/SocialPlatformIcon";
import {
  ConnectionHealthBadge,
  SocialEmptyState,
  SocialPageHeader,
  SocialSkeleton,
  SocialToast,
} from "../../../components/social/SocialShared";

export default function SocialHealth() {
  const [loading, setLoading] = useState(true);
  const [items, setItems] = useState([]);
  const [toast, setToast] = useState("");
  const activity = getMockActivity();

  useEffect(() => {
    const t = setTimeout(() => {
      setItems(getMockIntegrationHealth());
      setLoading(false);
    }, 400);
    return () => clearTimeout(t);
  }, []);

  function act(msg) {
    setToast(msg);
    setTimeout(() => setToast(""), 2200);
  }

  return (
    <div className="social-workspace">
      <SocialPageHeader
        title="Integration Health"
        description="Understand connection status without technical jargon."
        showCreate={false}
        actions={
          <Link to="/admin/social/accounts" className="btn-ghost">
            Social accounts
          </Link>
        }
      />

      {loading ? (
        <SocialSkeleton variant="account" count={3} />
      ) : !items.length ? (
        <SocialEmptyState
          title="No integrations yet"
          description="Connect an account to monitor publishing and analytics health."
          actionLabel="Connect account"
          to="/admin/social/accounts"
        />
      ) : (
        <div className="social-health-grid">
          {items.map((item) => (
            <article key={item.id} className="social-health-card admin-panel">
              <div className="social-health-card__top">
                <div className="social-health-card__identity">
                  <SocialPlatformIcon platform={item.platform} size={22} />
                  <div>
                    <h3>
                      {item.platform.charAt(0).toUpperCase() + item.platform.slice(1).replace("_", " ")}{" "}
                      {item.name}
                    </h3>
                    <ConnectionHealthBadge status={item.health} />
                  </div>
                </div>
                <button type="button" className="btn-ghost" onClick={() => act(`Manage ${item.name} (mock)`)}>
                  Manage
                </button>
              </div>
              <dl className="social-health-card__grid">
                <div>
                  <dt>Connection</dt>
                  <dd>{item.connection}</dd>
                </div>
                <div>
                  <dt>Publishing</dt>
                  <dd>{item.publishing}</dd>
                </div>
                <div>
                  <dt>Analytics</dt>
                  <dd>{item.analytics}</dd>
                </div>
                <div>
                  <dt>Permissions</dt>
                  <dd>{item.permissions}</dd>
                </div>
                <div>
                  <dt>Last successful sync</dt>
                  <dd>{item.lastSync}</dd>
                </div>
                <div>
                  <dt>Token</dt>
                  <dd>{item.token}</dd>
                </div>
              </dl>
            </article>
          ))}
        </div>
      )}

      <section className="admin-panel" style={{ marginTop: "1.25rem" }}>
        <div className="admin-panel__head">
          <h2>Recent activity</h2>
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

      <SocialToast message={toast} onDismiss={() => setToast("")} />
    </div>
  );
}
