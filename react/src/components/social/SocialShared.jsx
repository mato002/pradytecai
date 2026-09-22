import React, { useEffect, useId, useRef } from "react";
import { Link } from "react-router-dom";
import SocialPlatformIcon from "./SocialPlatformIcon";

/* ---------- Status badges ---------- */

const STATUS_CLASS = {
  Draft: "social-badge--neutral",
  "In review": "social-badge--info",
  "Changes requested": "social-badge--warn",
  Approved: "social-badge--ok",
  Scheduled: "social-badge--info",
  Publishing: "social-badge--info",
  Published: "social-badge--ok",
  Failed: "social-badge--danger",
  Cancelled: "social-badge--neutral",
  Connected: "social-badge--ok",
  "Attention required": "social-badge--warn",
  "Reconnect required": "social-badge--danger",
  Disconnected: "social-badge--neutral",
  Healthy: "social-badge--ok",
  Warning: "social-badge--warn",
  Error: "social-badge--danger",
  Pending: "social-badge--warn",
  Available: "social-badge--neutral",
};

export function PostStatusBadge({ status }) {
  return <span className={`social-badge ${STATUS_CLASS[status] || "social-badge--neutral"}`}>{status}</span>;
}

export function ConnectionHealthBadge({ status, showDot = true }) {
  const tone =
    status === "Healthy" || status === "Connected"
      ? "ok"
      : status === "Warning" || status === "Attention required"
        ? "warn"
        : status === "Error" || status === "Disconnected" || status === "Reconnect required"
          ? "danger"
          : "neutral";
  return (
    <span className={`social-health-badge social-health-badge--${tone}`}>
      {showDot && <span className="social-health-badge__dot" aria-hidden="true" />}
      {status}
    </span>
  );
}

/* ---------- Page header ---------- */

export function SocialPageHeader({ title, description, actions, showCreate = true }) {
  return (
    <div className="social-page-header">
      <div className="social-page-header__text">
        <h1>{title}</h1>
        {description && <p>{description}</p>}
      </div>
      <div className="social-page-header__actions">
        {actions}
        {showCreate && (
          <Link to="/admin/social/create" className="btn-primary social-create-btn">
            <span aria-hidden="true">+</span> Create post
          </Link>
        )}
      </div>
    </div>
  );
}

/* ---------- Empty / loading ---------- */

export function SocialEmptyState({ title, description, actionLabel, onAction, to }) {
  return (
    <div className="social-empty">
      <h3>{title}</h3>
      {description && <p>{description}</p>}
      {to && (
        <Link to={to} className="btn-primary">
          {actionLabel}
        </Link>
      )}
      {!to && onAction && (
        <button type="button" className="btn-primary" onClick={onAction}>
          {actionLabel}
        </button>
      )}
    </div>
  );
}

export function SocialSkeleton({ variant = "card", count = 1 }) {
  return (
    <div className={`social-skel-grid social-skel-grid--${variant}`} aria-busy="true" aria-label="Loading">
      {Array.from({ length: count }).map((_, i) => (
        <div key={i} className={`social-skel social-skel--${variant}`} />
      ))}
    </div>
  );
}

/* ---------- Filter bar ---------- */

export function SocialFilterBar({ children }) {
  return <div className="social-filter-bar">{children}</div>;
}

export function SocialSelect({ label, value, onChange, options, id }) {
  const autoId = useId();
  const selectId = id || autoId;
  return (
    <label className="social-select" htmlFor={selectId}>
      <span className="sr-only">{label}</span>
      <select id={selectId} value={value} onChange={(e) => onChange(e.target.value)} aria-label={label}>
        {options.map((o) => (
          <option key={o.value} value={o.value}>
            {o.label}
          </option>
        ))}
      </select>
    </label>
  );
}

/* ---------- Modal / Drawer ---------- */

export function SocialModal({ open, onClose, title, children, footer, size = "md" }) {
  const ref = useRef(null);
  useEffect(() => {
    if (!open) return undefined;
    const onKey = (e) => {
      if (e.key === "Escape") onClose?.();
    };
    document.addEventListener("keydown", onKey);
    const prev = document.activeElement;
    ref.current?.focus();
    return () => {
      document.removeEventListener("keydown", onKey);
      prev?.focus?.();
    };
  }, [open, onClose]);

  if (!open) return null;
  return (
    <div className="social-modal-root" role="presentation">
      <button type="button" className="social-modal-backdrop" aria-label="Close dialog" onClick={onClose} />
      <div
        className={`social-modal social-modal--${size}`}
        role="dialog"
        aria-modal="true"
        aria-label={title}
        tabIndex={-1}
        ref={ref}
      >
        <div className="social-modal__head">
          <h2>{title}</h2>
          <button type="button" className="admin-icon-btn" aria-label="Close" onClick={onClose}>
            ✕
          </button>
        </div>
        <div className="social-modal__body">{children}</div>
        {footer && <div className="social-modal__foot">{footer}</div>}
      </div>
    </div>
  );
}

export function SocialDrawer({ open, onClose, title, children, footer }) {
  useEffect(() => {
    if (!open) return undefined;
    const onKey = (e) => {
      if (e.key === "Escape") onClose?.();
    };
    document.addEventListener("keydown", onKey);
    return () => document.removeEventListener("keydown", onKey);
  }, [open, onClose]);

  if (!open) return null;
  return (
    <div className="social-drawer-root" role="presentation">
      <button type="button" className="social-modal-backdrop" aria-label="Close panel" onClick={onClose} />
      <aside className="social-drawer" role="dialog" aria-modal="true" aria-label={title}>
        <div className="social-drawer__head">
          <h2>{title}</h2>
          <button type="button" className="admin-icon-btn" aria-label="Close" onClick={onClose}>
            ✕
          </button>
        </div>
        <div className="social-drawer__body">{children}</div>
        {footer && <div className="social-drawer__foot">{footer}</div>}
      </aside>
    </div>
  );
}

/* ---------- Toast ---------- */

export function SocialToast({ message, onDismiss }) {
  if (!message) return null;
  return (
    <div className="social-toast" role="status">
      <span>{message}</span>
      <button type="button" aria-label="Dismiss" onClick={onDismiss}>
        ✕
      </button>
    </div>
  );
}

/* ---------- Chips / cards helpers ---------- */

export function SocialAccountChip({ account, selected, onToggle }) {
  return (
    <button
      type="button"
      className={`social-account-chip ${selected ? "is-selected" : ""}`}
      onClick={() => onToggle?.(account.id)}
      aria-pressed={!!selected}
    >
      <SocialPlatformIcon platform={account.platform} size={16} />
      <span>{account.name}</span>
      {selected && <span className="social-account-chip__check" aria-hidden="true">✓</span>}
    </button>
  );
}

export function AnalyticsMetricCard({ label, value, delta }) {
  return (
    <article className="admin-kpi">
      <p className="admin-kpi__label" style={{ marginTop: 0 }}>
        {label}
      </p>
      <p className="admin-kpi__value">{value}</p>
      {delta && <p className="admin-kpi__trend admin-kpi__trend--up">{delta}</p>}
    </article>
  );
}

export function MiniSparkline({ series, keys = ["reach", "engagement"] }) {
  const w = 280;
  const h = 80;
  const pad = 6;
  const max = Math.max(...series.flatMap((d) => keys.map((k) => d[k] || 0)), 1);

  function pathFor(key, color) {
    const pts = series.map((d, i) => {
      const x = pad + (i / Math.max(series.length - 1, 1)) * (w - pad * 2);
      const y = h - pad - ((d[key] || 0) / max) * (h - pad * 2);
      return `${x},${y}`;
    });
    return (
      <polyline
        key={key}
        fill="none"
        stroke={color}
        strokeWidth="2.5"
        strokeLinejoin="round"
        strokeLinecap="round"
        points={pts.join(" ")}
      />
    );
  }

  return (
    <svg className="social-sparkline" viewBox={`0 0 ${w} ${h}`} role="img" aria-label="Performance chart">
      {pathFor(keys[0], "#004d40")}
      {keys[1] && pathFor(keys[1], "#c9a227")}
    </svg>
  );
}

export function PercentBars({ items, getLabel = (i) => i.label, getPercent = (i) => i.percent, renderIcon }) {
  return (
    <ul className="social-percent-list">
      {items.map((item) => (
        <li key={item.id || item.platform || getLabel(item)}>
          <div className="social-percent-list__row">
            <span className="social-percent-list__label">
              {renderIcon?.(item)}
              {getLabel(item)}
            </span>
            <span className="social-percent-list__pct">{getPercent(item)}%</span>
          </div>
          <div className="social-percent-list__track" aria-hidden="true">
            <div className="social-percent-list__fill" style={{ width: `${getPercent(item)}%` }} />
          </div>
        </li>
      ))}
    </ul>
  );
}
