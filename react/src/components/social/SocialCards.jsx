import React from "react";
import SocialPlatformIcon from "./SocialPlatformIcon";
import { ConnectionHealthBadge, PostStatusBadge } from "./SocialShared";

export function SocialAccountCard({ account, onManage, onAnalytics }) {
  return (
    <article className="social-account-card">
      <div className="social-account-card__top">
        <div className="social-account-card__identity">
          <span className="social-account-card__avatar" style={{ borderColor: "currentColor" }}>
            <SocialPlatformIcon platform={account.platform} size={22} />
          </span>
          <div>
            <h3>{account.platform.charAt(0).toUpperCase() + account.platform.slice(1).replace("_", " ")}</h3>
            <p>{account.name}</p>
          </div>
        </div>
        <ConnectionHealthBadge status={account.status} />
      </div>

      <p className="social-account-card__followers">
        {account.followers != null ? `${account.followers.toLocaleString()} followers` : "Not connected"}
      </p>

      <ul className="social-account-card__caps">
        <li>
          Publishing {account.publishing ? <span className="ok">✓</span> : <span className="no">—</span>}
        </li>
        <li>
          Analytics {account.analytics ? <span className="ok">✓</span> : <span className="no">—</span>}
        </li>
        <li>
          Inbox {account.inbox ? <span className="ok">✓</span> : <span className="no">—</span>}
        </li>
      </ul>

      <p className="social-account-card__sync">Last sync · {account.lastSync}</p>

      <div className="social-account-card__actions">
        <button type="button" className="btn-ghost" onClick={() => onAnalytics?.(account)}>
          View analytics
        </button>
        <button type="button" className="btn-ghost" onClick={() => onManage?.(account)}>
          Manage
        </button>
      </div>
    </article>
  );
}

export function PostCard({ item, view = "list", onPreview, onEdit, onDuplicate, onDelete }) {
  return (
    <article className={`social-post-card social-post-card--${view}`}>
      <div className="social-post-card__thumb" aria-hidden="true">
        <span>IMG</span>
      </div>
      <div className="social-post-card__body">
        <p className="social-post-card__caption">{item.caption}</p>
        <div className="social-post-card__platforms">
          {item.platforms.map((p) => (
            <SocialPlatformIcon key={p} platform={p} size={14} />
          ))}
          <span>{item.platforms.map((p) => p.charAt(0).toUpperCase() + p.slice(1)).join(" + ")}</span>
        </div>
        <div className="social-post-card__meta">
          <span>{item.product}</span>
          <PostStatusBadge status={item.status} />
          {item.scheduledAt && <span>{item.scheduledAt}</span>}
          <span>{item.author}</span>
        </div>
      </div>
      <div className="social-post-card__actions">
        <button type="button" className="btn-ghost" onClick={() => onPreview?.(item)}>
          Preview
        </button>
        <button type="button" className="btn-ghost" onClick={() => onEdit?.(item)}>
          Edit
        </button>
        <button type="button" className="btn-ghost" onClick={() => onDuplicate?.(item)}>
          Duplicate
        </button>
        <button type="button" className="btn-ghost" onClick={() => onDelete?.(item)}>
          Delete
        </button>
      </div>
    </article>
  );
}

export function CalendarPostCard({ post, onClick }) {
  return (
    <button type="button" className="social-cal-card" onClick={() => onClick?.(post)}>
      <span className="social-cal-card__time">{post.time}</span>
      <span className="social-cal-card__row">
        <SocialPlatformIcon platform={post.platform} size={14} />
        <strong>{post.product || post.title}</strong>
      </span>
      <span className="social-cal-card__title">{post.title}</span>
      <PostStatusBadge status={post.status} />
    </button>
  );
}

export function FailedPostBanner({ post, onRetry, onEdit, onViewAccount }) {
  if (!post) return null;
  return (
    <div className="social-fail-banner" role="alert">
      <div>
        <h3>Publish failed</h3>
        <p className="social-fail-banner__meta">
          <SocialPlatformIcon platform={post.platform} size={16} />
          <span>
            {post.platform.charAt(0).toUpperCase() + post.platform.slice(1)} · {post.title}
          </span>
        </p>
        <p>The post could not be published.</p>
        <p className="social-fail-banner__reason">
          Reason: {post.failReason || "Connection requires attention."}
        </p>
      </div>
      <div className="social-fail-banner__actions">
        <button type="button" className="btn-primary" onClick={() => onRetry?.(post)}>
          Retry
        </button>
        <button type="button" className="btn-ghost" onClick={() => onEdit?.(post)}>
          Edit
        </button>
        <button type="button" className="btn-ghost" onClick={() => onViewAccount?.(post)}>
          View account
        </button>
      </div>
    </div>
  );
}
