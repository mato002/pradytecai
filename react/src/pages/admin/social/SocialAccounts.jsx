import React, { useEffect, useState } from "react";
import { useNavigate } from "react-router-dom";
import {
  getMockAccountSummary,
  getMockAvailableConnections,
  getMockSocialAccounts,
  PLATFORMS,
} from "../../../data/socialMockData";
import { ConnectAccountWizard } from "../../../components/social/ComposerParts";
import SocialPlatformIcon from "../../../components/social/SocialPlatformIcon";
import { SocialAccountCard } from "../../../components/social/SocialCards";
import {
  SocialEmptyState,
  SocialPageHeader,
  SocialSkeleton,
  SocialToast,
} from "../../../components/social/SocialShared";

export default function SocialAccounts() {
  const navigate = useNavigate();
  const [loading, setLoading] = useState(true);
  const [accounts, setAccounts] = useState([]);
  const [wizardOpen, setWizardOpen] = useState(false);
  const [toast, setToast] = useState("");
  const summary = getMockAccountSummary();
  const available = getMockAvailableConnections();

  useEffect(() => {
    const t = setTimeout(() => {
      setAccounts(getMockSocialAccounts());
      setLoading(false);
    }, 400);
    return () => clearTimeout(t);
  }, []);

  const connected = accounts.filter((a) => a.status !== "Disconnected");

  function act(msg) {
    setToast(msg);
    setTimeout(() => setToast(""), 2400);
  }

  return (
    <div className="social-workspace">
      <SocialPageHeader
        title="Social Accounts"
        description="Manage channels available for publishing and analytics."
        actions={
          <button type="button" className="btn-primary" onClick={() => setWizardOpen(true)}>
            + Connect account
          </button>
        }
        showCreate={false}
      />

      <div className="admin-kpi-grid social-account-summary">
        <article className="admin-kpi">
          <p className="admin-kpi__label" style={{ marginTop: 0 }}>
            Connected
          </p>
          <p className="admin-kpi__value">{summary.connected}</p>
        </article>
        <article className="admin-kpi">
          <p className="admin-kpi__label" style={{ marginTop: 0 }}>
            Attention
          </p>
          <p className="admin-kpi__value">{summary.attention}</p>
        </article>
        <article className="admin-kpi">
          <p className="admin-kpi__label" style={{ marginTop: 0 }}>
            Disconnected
          </p>
          <p className="admin-kpi__value">{summary.disconnected}</p>
        </article>
      </div>

      {loading ? (
        <SocialSkeleton variant="account" count={4} />
      ) : !connected.length ? (
        <SocialEmptyState
          title="No social accounts connected"
          description="Connect your social channels to start scheduling and managing content from Pradytec."
          actionLabel="Connect account"
          onAction={() => setWizardOpen(true)}
        />
      ) : (
        <div className="social-account-grid">
          {connected.map((account) => (
            <SocialAccountCard
              key={account.id}
              account={account}
              onAnalytics={() => navigate("/admin/social/analytics")}
              onManage={() => act(`Manage ${account.name} (mock)`)}
            />
          ))}
        </div>
      )}

      <section className="admin-panel" style={{ marginTop: "1.25rem" }}>
        <div className="admin-panel__head">
          <h2>Available connections</h2>
        </div>
        <div className="social-available-grid">
          {available.map((c) => (
            <button
              key={c.platform}
              type="button"
              className="social-available-card"
              onClick={() => setWizardOpen(true)}
            >
              <SocialPlatformIcon platform={c.platform} size={24} />
              <span>{PLATFORMS.find((p) => p.id === c.platform)?.label || c.label}</span>
              <span className="social-muted">Connect</span>
            </button>
          ))}
        </div>
      </section>

      <ConnectAccountWizard
        open={wizardOpen}
        onClose={() => setWizardOpen(false)}
        onComplete={(payload) => act(`Connected ${payload.platform} (mock)`)}
      />

      <SocialToast message={toast} onDismiss={() => setToast("")} />
    </div>
  );
}
