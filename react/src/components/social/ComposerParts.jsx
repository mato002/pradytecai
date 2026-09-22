import React, { useMemo, useState } from "react";
import SocialPlatformIcon from "./SocialPlatformIcon";
import { SocialAccountChip } from "./SocialShared";
import { PLATFORMS } from "../../data/socialMockData";

export function ComposerAccountSelector({ accounts, selectedIds, onToggle }) {
  return (
    <fieldset className="social-composer-accounts">
      <legend>Publish to</legend>
      <div className="social-composer-accounts__list">
        {accounts.map((acc) => (
          <SocialAccountChip
            key={acc.id}
            account={acc}
            selected={selectedIds.includes(acc.id)}
            onToggle={onToggle}
          />
        ))}
      </div>
    </fieldset>
  );
}

export function PlatformTabs({ platforms, active, onChange, label = "Platform" }) {
  return (
    <div className="social-platform-tabs" role="tablist" aria-label={label}>
      {platforms.map((p) => {
        const id = typeof p === "string" ? p : p.id;
        const name = typeof p === "string" ? PLATFORMS.find((x) => x.id === p)?.label || p : p.label;
        return (
          <button
            key={id}
            type="button"
            role="tab"
            aria-selected={active === id}
            className={`social-platform-tabs__btn ${active === id ? "is-active" : ""}`}
            onClick={() => onChange(id)}
          >
            {id !== "original" && <SocialPlatformIcon platform={id} size={14} />}
            {name}
          </button>
        );
      })}
    </div>
  );
}

export function PostPreview({ platform, caption, accountName = "Prady Technologies", handle = "@pradytec", mediaLabel = "Media preview" }) {
  return (
    <div className={`social-preview social-preview--${platform}`}>
      <div className="social-preview__head">
        <span className="social-preview__avatar" aria-hidden="true">
          PT
        </span>
        <div>
          <strong>{accountName}</strong>
          <span>{handle}</span>
        </div>
        <SocialPlatformIcon platform={platform} size={18} />
      </div>
      <div className="social-preview__media" aria-hidden="true">
        {mediaLabel}
      </div>
      <div className="social-preview__caption">
        {(caption || "").split("\n").map((line, i) => (
          <p key={i}>{line || "\u00A0"}</p>
        ))}
      </div>
      <div className="social-preview__stats" aria-hidden="true">
        <span>♡ 1,284</span>
        <span>comments 73</span>
      </div>
    </div>
  );
}

export function ScheduleModal({ open, onClose, onSchedule, onQueue }) {
  const [date, setDate] = useState("2026-09-23");
  const [time, setTime] = useState("10:00");
  const [tz] = useState("Africa/Nairobi");

  if (!open) return null;

  return (
    <div className="social-modal-root" role="presentation">
      <button type="button" className="social-modal-backdrop" aria-label="Close" onClick={onClose} />
      <div className="social-modal social-modal--sm" role="dialog" aria-modal="true" aria-label="Schedule post">
        <div className="social-modal__head">
          <h2>Schedule post</h2>
          <button type="button" className="admin-icon-btn" aria-label="Close" onClick={onClose}>
            ✕
          </button>
        </div>
        <div className="social-modal__body social-schedule-form">
          <label>
            Date
            <input type="date" value={date} onChange={(e) => setDate(e.target.value)} />
          </label>
          <label>
            Time
            <input type="time" value={time} onChange={(e) => setTime(e.target.value)} />
          </label>
          <label>
            Timezone
            <input type="text" value={tz} readOnly />
          </label>
          <button type="button" className="btn-ghost social-schedule-form__queue" onClick={() => onQueue?.({ date, time, tz })}>
            Add to next available queue slot
          </button>
        </div>
        <div className="social-modal__foot">
          <button type="button" className="btn-ghost" onClick={onClose}>
            Cancel
          </button>
          <button type="button" className="btn-primary" onClick={() => onSchedule?.({ date, time, tz })}>
            Schedule post
          </button>
        </div>
      </div>
    </div>
  );
}

export function ConnectAccountWizard({ open, onClose, onComplete }) {
  const [step, setStep] = useState(1);
  const [platform, setPlatform] = useState(null);
  const [selectedAccounts, setSelectedAccounts] = useState(["@pradytec"]);
  const [productId, setProductId] = useState("corporate");

  const connectable = useMemo(
    () => [
      "instagram",
      "facebook",
      "linkedin",
      "tiktok",
      "youtube",
      "x",
      "pinterest",
      "threads",
      "bluesky",
      "mastodon",
      "google_business",
    ],
    []
  );

  function resetAndClose() {
    setStep(1);
    setPlatform(null);
    setSelectedAccounts(["@pradytec"]);
    setProductId("corporate");
    onClose?.();
  }

  function toggleAcc(name) {
    setSelectedAccounts((prev) => (prev.includes(name) ? prev.filter((n) => n !== name) : [...prev, name]));
  }

  if (!open) return null;

  return (
    <div className="social-modal-root" role="presentation">
      <button type="button" className="social-modal-backdrop" aria-label="Close" onClick={resetAndClose} />
      <div className="social-modal social-modal--lg" role="dialog" aria-modal="true" aria-label="Connect account">
        <div className="social-modal__head">
          <h2>
            {step === 1 && "Choose a platform"}
            {step === 2 && `Connecting to ${PLATFORMS.find((p) => p.id === platform)?.label || ""}`}
            {step === 3 && "Choose accounts"}
            {step === 4 && "Capabilities"}
            {step === 5 && "Assign product"}
            {step === 6 && "Account connected successfully"}
          </h2>
          <button type="button" className="admin-icon-btn" aria-label="Close" onClick={resetAndClose}>
            ✕
          </button>
        </div>
        <div className="social-modal__body">
          {step === 1 && (
            <div className="social-connect-grid">
              {connectable.map((id) => (
                <button
                  key={id}
                  type="button"
                  className={`social-connect-card ${platform === id ? "is-selected" : ""}`}
                  onClick={() => setPlatform(id)}
                >
                  <SocialPlatformIcon platform={id} size={28} />
                  <span>{PLATFORMS.find((p) => p.id === id)?.label || id}</span>
                </button>
              ))}
            </div>
          )}
          {step === 2 && (
            <div className="social-connect-step">
              <SocialPlatformIcon platform={platform} size={40} />
              <p>
                You&apos;ll be redirected to {PLATFORMS.find((p) => p.id === platform)?.label} to authorize Pradytec
                Marketing.
              </p>
              <p className="social-muted">Demo only — no OAuth is performed in this UI foundation.</p>
            </div>
          )}
          {step === 3 && (
            <div className="social-connect-step">
              <label className="social-check">
                <input
                  type="checkbox"
                  checked={selectedAccounts.includes("@pradytec")}
                  onChange={() => toggleAcc("@pradytec")}
                />
                @pradytec
              </label>
              <label className="social-check">
                <input
                  type="checkbox"
                  checked={selectedAccounts.includes("@pradylabs")}
                  onChange={() => toggleAcc("@pradylabs")}
                />
                @pradylabs
              </label>
            </div>
          )}
          {step === 4 && (
            <ul className="social-cap-list">
              <li>✓ Publishing</li>
              <li>✓ Analytics</li>
              <li>✓ Comments</li>
            </ul>
          )}
          {step === 5 && (
            <div className="social-connect-step">
              {[
                ["corporate", "Corporate / All Products"],
                ["mfi", "Microfinance"],
                ["fleet", "Fleet"],
                ["sms", "Bulk SMS"],
              ].map(([id, label]) => (
                <label key={id} className="social-check">
                  <input type="radio" name="product" checked={productId === id} onChange={() => setProductId(id)} />
                  {label}
                </label>
              ))}
            </div>
          )}
          {step === 6 && (
            <div className="social-connect-step social-connect-success">
              <p className="social-connect-success__icon" aria-hidden="true">
                ✓
              </p>
              <p>Account connected successfully</p>
              <p className="social-muted">Mock state only — replace with real OAuth in a later phase.</p>
            </div>
          )}
        </div>
        <div className="social-modal__foot">
          {step > 1 && step < 6 && (
            <button type="button" className="btn-ghost" onClick={() => setStep((s) => s - 1)}>
              Back
            </button>
          )}
          {step === 1 && (
            <button type="button" className="btn-primary" disabled={!platform} onClick={() => setStep(2)}>
              Continue
            </button>
          )}
          {step === 2 && (
            <button type="button" className="btn-primary" onClick={() => setStep(3)}>
              Continue
            </button>
          )}
          {step === 3 && (
            <button
              type="button"
              className="btn-primary"
              disabled={!selectedAccounts.length}
              onClick={() => setStep(4)}
            >
              Continue
            </button>
          )}
          {step === 4 && (
            <button type="button" className="btn-primary" onClick={() => setStep(5)}>
              Continue
            </button>
          )}
          {step === 5 && (
            <button type="button" className="btn-primary" onClick={() => setStep(6)}>
              Finish
            </button>
          )}
          {step === 6 && (
            <button
              type="button"
              className="btn-primary"
              onClick={() => {
                onComplete?.({ platform, selectedAccounts, productId });
                resetAndClose();
              }}
            >
              Done
            </button>
          )}
        </div>
      </div>
    </div>
  );
}
