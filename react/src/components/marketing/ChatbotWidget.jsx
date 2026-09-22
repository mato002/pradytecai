import React, { useState } from "react";
import { Link } from "react-router-dom";

export default function ChatbotWidget() {
  const [open, setOpen] = useState(false);

  return (
    <div style={{ position: "fixed", bottom: 24, right: 24, zIndex: 10001 }}>
      {open && (
        <div className="mb-3 w-[360px] max-w-[90vw] overflow-hidden rounded-xl border border-slate-200 bg-white shadow-2xl">
          <div
            className="flex items-center justify-between px-4 py-3 text-white"
            style={{ background: "linear-gradient(110deg, #00398C 0%, #0557A6 48%, #0487CD 100%)" }}
          >
            <div>
              <p className="text-sm font-semibold">Prady Assistant</p>
              <p className="text-[11px] text-sky-100">Online • Typically replies in a few minutes</p>
            </div>
            <button type="button" className="px-2 text-lg" onClick={() => setOpen(false)}>
              ✕
            </button>
          </div>
          <div className="space-y-3 bg-slate-50 px-4 py-3 text-sm">
            <p className="rounded-xl border border-slate-200 bg-white px-3 py-2 shadow-sm text-gray-800">
              Hi, welcome to Prady Technologies. Tell us briefly what you need and we&apos;ll route it to
              the right team.
            </p>
            <div className="flex flex-wrap gap-2">
              {["Microfinance demo", "SACCO System", "Talk to sales"].map((q) => (
                <Link
                  key={q}
                  to="/contact"
                  className="rounded-lg bg-slate-100 px-3 py-1 text-[11px] text-slate-700 hover:bg-slate-200"
                  onClick={() => setOpen(false)}
                >
                  {q}
                </Link>
              ))}
            </div>
          </div>
        </div>
      )}
      <button
        type="button"
        onClick={() => setOpen((v) => !v)}
        className="flex h-14 w-14 items-center justify-center rounded-full text-white shadow-lg"
        style={{ background: "linear-gradient(110deg, #00398C 0%, #0557A6 48%, #0487CD 100%)" }}
        aria-label="Open chat"
      >
        <svg width="22" height="22" viewBox="0 0 48 48" fill="none">
          <path d="M6 6h28v16H22v8H6V6z" fill="#fff" />
          <path d="M22 22h16v16H22V22z" fill="#fff" />
          <rect x="28" y="28" width="14" height="14" fill="#19A7EF" />
        </svg>
      </button>
    </div>
  );
}
