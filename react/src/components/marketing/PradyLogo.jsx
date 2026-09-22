import React from "react";
import { Link } from "react-router-dom";

export default function PradyLogo({ variant = "nav", theme = "light" }) {
  const dark = theme === "dark";
  const markFill = dark ? "#fff" : "#053171";
  const cyan = "#19A7EF";
  const muted = dark ? "rgba(255,255,255,0.72)" : "#535B67";
  const nameColor = dark ? "#fff" : "#053171";

  return (
    <Link to="/" className="prady-brand inline-flex items-center gap-3 no-underline" data-variant={variant}>
      <svg className="prady-brand__mark-svg" viewBox="0 0 48 48" fill="none" aria-hidden="true">
        <path d="M6 6h28v16H22v8H6V6z" fill={markFill} />
        <path d="M22 22h16v16H22V22z" fill={markFill} />
        <rect x="28" y="28" width="14" height="14" fill={cyan} />
      </svg>
      <span className="leading-none">
        <span className="prady-brand__name relative inline-block" style={{ color: nameColor }}>
          PRADY
          <span className="prady-brand__a-bar" style={{ background: cyan }} />
        </span>
        <span className="prady-brand__sub block" style={{ color: muted }}>
          Technologies Ltd
        </span>
        {variant === "nav" && !dark && (
          <span className="prady-brand__tagline" style={{ color: muted }}>
            Doing It Differently
          </span>
        )}
      </span>
    </Link>
  );
}
