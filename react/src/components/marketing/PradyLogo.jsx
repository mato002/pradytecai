import React from "react";
import { Link } from "react-router-dom";

export default function PradyLogo({ variant = "nav", theme = "light" }) {
  const dark = theme === "dark";
  const markFill = dark ? "#ffffff" : "#0A3B75"; // Deep blue from image
  const cyan = "#19A7EF"; // Cyan from image
  const textColor = dark ? "#ffffff" : "#0A3B75";

  return (
    <Link to="/" className="prady-brand inline-flex items-center gap-4 no-underline" data-variant={variant}>
      <svg className="prady-brand__mark-svg" viewBox="0 0 48 48" fill="none" aria-hidden="true" style={{ width: '3.75rem', height: '3.75rem', flexShrink: 0 }}>
        {/* Dark blue shape */}
        <path d="M4 6 h34 v18 h-16 v18 h-18 z" fill={markFill} />
        {/* Cyan square */}
        <rect x="22" y="24" width="16" height="16" fill={cyan} />
      </svg>
      <span className="flex flex-col items-center justify-center leading-none">
        <span className="prady-brand__name text-4xl m-0 font-bold" style={{ color: textColor, letterSpacing: '0.05em' }}>
          PR<span style={{ color: cyan }}>A</span>DY
        </span>
        <span className="prady-brand__sub block m-0 mt-1 font-bold tracking-widest uppercase" style={{ color: textColor, fontSize: '0.75rem', letterSpacing: '0.12em' }}>
          TECHNOLOGIES LTD
        </span>
        <span className="block mt-3 mb-3" style={{ height: '2px', width: '60%', background: cyan }} />
        {variant === "nav" && (
          <span className="prady-brand__tagline block m-0 font-bold tracking-widest uppercase" style={{ color: textColor, fontSize: '0.55rem', border: 'none', padding: 0 }}>
            DOING IT DIFFERENTLY
          </span>
        )}
      </span>
    </Link>
  );
}
