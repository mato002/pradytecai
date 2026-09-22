import React from "react";

const PATHS = {
  instagram: (
    <>
      <rect x="2" y="2" width="20" height="20" rx="5" />
      <circle cx="12" cy="12" r="4" />
      <circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none" />
    </>
  ),
  facebook: <path d="M14 8h3V4h-3c-2.8 0-5 2.2-5 5v2H6v4h3v7h4v-7h3l1-4h-4V9c0-.6.4-1 1-1z" />,
  linkedin: (
    <>
      <rect x="3" y="3" width="18" height="18" rx="2" />
      <path d="M8 10v7M8 7v.01M12 17v-4.5a2 2 0 014 0V17" />
    </>
  ),
  tiktok: <path d="M14 4v9.2a3.8 3.8 0 11-3.2-3.75V12a1.5 1.5 0 101.2 1.47V4h2zm2 0c.6 2.2 2.2 3.8 4.4 4.2" />,
  youtube: (
    <>
      <rect x="2" y="5" width="20" height="14" rx="4" />
      <path d="M10 9l6 3-6 3V9z" fill="currentColor" stroke="none" />
    </>
  ),
  x: <path d="M4 4l7.2 8.4L4.5 20H7l5.2-5.8L16.8 20H20l-7.4-8.7L19.5 4H17l-4.8 5.4L8.2 4H4z" />,
  pinterest: (
    <>
      <circle cx="12" cy="12" r="9" />
      <path d="M10.5 20l1.5-6s-.6-1.2.3-2.1c.9-.9 2.1-.4 2.1-.4 1.8.2 3-1.5 2.7-3.2-.4-2.2-2.5-3-4.5-2.5-2.3.6-3.5 2.8-3 5 .2.9.8 1.6.8 1.6" />
    </>
  ),
  threads: <path d="M12 4c4 0 6.5 2.2 6.5 6.2 0 5.2-4.2 8.3-6.5 9.3-2.3-1-6.5-4.1-6.5-9.3C5.5 6.2 8 4 12 4zm0 3.2c-1.6 0-2.8 1.1-2.8 2.8 0 3.4 2.2 5.2 2.8 5.6.6-.4 2.8-2.2 2.8-5.6 0-1.7-1.2-2.8-2.8-2.8z" />,
  bluesky: <path d="M6 7c2.5 2 4 4.8 6 8.5C14 11.8 15.5 9 18 7c1.5-1.2 3-1.5 3-.2 0 2.8-1.8 8.2-4.2 10.5-1.4 1.3-2.6.6-3.5-.6-.3-.4-.6-.8-.8-1.1-.2.3-.5.7-.8 1.1-.9 1.2-2.1 1.9-3.5.6C5.8 15 4 9.6 4 6.8c0-1.3 1.5-1 2 .2z" />,
  mastodon: (
    <>
      <rect x="4" y="3" width="16" height="14" rx="5" />
      <path d="M8 8v4.5a2 2 0 002 2h.5M16 8v4.5a2 2 0 01-2 2H13.5M12 17v3" />
    </>
  ),
  google_business: (
    <>
      <path d="M4 10h16v9H4z" />
      <path d="M7 10V7a5 5 0 0110 0v3" />
      <circle cx="12" cy="14.5" r="1.5" fill="currentColor" stroke="none" />
    </>
  ),
};

const COLORS = {
  instagram: "#E4405F",
  facebook: "#1877F2",
  linkedin: "#0A66C2",
  tiktok: "#111111",
  youtube: "#FF0000",
  x: "#111111",
  pinterest: "#E60023",
  threads: "#111111",
  bluesky: "#1285FE",
  mastodon: "#6364FF",
  google_business: "#4285F4",
};

export default function SocialPlatformIcon({ platform, size = 18, className = "" }) {
  const color = COLORS[platform] || "#004d40";
  return (
    <span
      className={`social-platform-icon ${className}`}
      style={{ color, width: size, height: size }}
      aria-hidden="true"
    >
      <svg width={size} height={size} viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.7" strokeLinecap="round" strokeLinejoin="round">
        {PATHS[platform] || PATHS.instagram}
      </svg>
    </span>
  );
}

export function platformColor(platform) {
  return COLORS[platform] || "#004d40";
}
