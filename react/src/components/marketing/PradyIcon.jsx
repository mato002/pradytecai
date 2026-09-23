import React from "react";

const paths = {
  finance: "M4 10h16v2H4zm2-4h12v2H6zm-2 8h16v6H4z",
  users: "M12 12a4 4 0 100-8 4 4 0 000 8zm-7 9a7 7 0 0114 0",
  building: "M4 21V5a1 1 0 011-1h6v17H4zm8 0V9h7a1 1 0 011 1v11h-8z",
  location: "M12 21s7-5.33 7-11a7 7 0 10-14 0c0 5.67 7 11 7 11zm0-9a2 2 0 110-4 2 2 0 010 4z",
  car: "M5 16l1.5-4.5A2 2 0 018.4 10h7.2a2 2 0 011.9 1.5L19 16M7 16h10M7 19a1.5 1.5 0 100-3 1.5 1.5 0 000 3zm10 0a1.5 1.5 0 100-3 1.5 1.5 0 000 3z",
  live: "M15 10l4.55-2.27A1 1 0 0121 8.62v6.76a1 1 0 01-1.45.89L15 14M4 8h9a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4a2 2 0 012-2z",
  wallet: "M3 7a2 2 0 012-2h12a2 2 0 012 2v2H5a2 2 0 000 4h14v2a2 2 0 01-2 2H5a2 2 0 01-2-2V7zm14 4h2",
  code: "M8 8l-4 4 4 4M16 8l4 4-4 4M14 4l-4 16",
  cloud: "M7 18a4 4 0 010-8 5 5 0 019.9 1.5A3.5 3.5 0 0117 18H7z",
  handshake: "M8 13l3 3 6-6M4 12l4 4M16 8l4 4",
  rocket: "M5 19l4-1 9-9a3 3 0 00-4-4L5 15l-1 4zm9-11l2 2",
  support: "M12 3a7 7 0 00-7 7v2a3 3 0 003 3h1v-4H7a5 5 0 0110 0h-2v4h1a3 3 0 003-3v-2a7 7 0 00-7-7z",
  shield: "M12 3l8 3v6c0 5-3.5 8.5-8 10-4.5-1.5-8-5-8-10V6l8-3z",
  clock: "M12 7v5l3 2M12 21a9 9 0 110-18 9 9 0 010 18z",
  piggy: "M5 12a7 7 0 0114 0v4H5v-4zm14 1h2M8 18v2m8-2v2",
  cog: "M12 8a4 4 0 100 8 4 4 0 000-8zm0-5v2m0 14v2m9-9h-2M5 12H3m15.4-6.4l-1.4 1.4M7 17l-1.4 1.4m12.8 0L17 17M7 7L5.6 5.6",
  hr: "M12 12a4 4 0 100-8 4 4 0 000 8zM4 20a8 8 0 0116 0",
  group: "M7 12a3 3 0 100-6 3 3 0 000 6zm10 0a3 3 0 100-6 3 3 0 000 6zM3 20a5 5 0 0110 0M11 20a5 5 0 0110 0",
  check: "M5 13l4 4L19 7",
};

export function hasPradyIcon(name) {
  return Boolean(name && Object.prototype.hasOwnProperty.call(paths, name));
}

export default function PradyIcon({ name = "code", className = "w-6 h-6" }) {
  const d = paths[name] || paths.code;
  return (
    <svg className={className} viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.8" aria-hidden="true">
      <path strokeLinecap="round" strokeLinejoin="round" d={d} />
    </svg>
  );
}
