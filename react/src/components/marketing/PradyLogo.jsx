import React from "react";
import { Link } from "react-router-dom";

/** Official Prady mark + wordmark lockup for header/footer. */
export default function PradyLogo({ variant = "nav", theme = "light" }) {
  const dark = theme === "dark";
  const markFill = dark ? "#ffffff" : "#0B3F8F";
  const cyan = "#1598CD";
  const textColor = dark ? "#ffffff" : "#0B3F8F";
  const tagColor = dark ? "rgba(255,255,255,0.78)" : "#1598CD";

  return (
    <Link
      to="/"
      className={`prady-brand prady-brand--${variant}${dark ? " prady-brand--dark" : ""}`}
      data-variant={variant}
      aria-label="Prady Technologies home"
    >
      <svg
        className="prady-brand__mark"
        viewBox="0 0 48 48"
        fill="none"
        aria-hidden="true"
      >
        <path d="M4 4h32v18H20v22H4V4z" fill={markFill} />
        <rect x="22" y="24" width="18" height="18" fill={cyan} />
      </svg>

      <span className="prady-brand__text">
        <span className="prady-brand__name" style={{ color: textColor }}>
          PR<span className="prady-brand__a">
            A
            <span className="prady-brand__a-bar" style={{ background: cyan }} aria-hidden="true" />
          </span>
          DY
        </span>
        <span className="prady-brand__sub" style={{ color: textColor }}>
          TECHNOLOGIES LTD
        </span>
        <span className="prady-brand__rule" aria-hidden="true" />
        <span className="prady-brand__tagline" style={{ color: tagColor }}>
          DOING IT DIFFERENTLY
        </span>
      </span>
    </Link>
  );
}
