import React, { useEffect, useState } from "react";
import { Link } from "react-router-dom";
import { api } from "../../api/client";

export default function CareersPage() {
  const [positions, setPositions] = useState([]);
  useEffect(() => {
    document.title = "Careers | Prady Technologies";
    api("/public/positions/")
      .then(setPositions)
      .catch(() => setPositions([]));
  }, []);

  return (
    <section className="mkt-section mkt-section--white">
      <div className="mkt-container" style={{ maxWidth: 900 }}>
        <h1 className="mkt-section__title mkt-section__title--left">Careers</h1>
        <p className="mkt-section__subtitle" style={{ textAlign: "left", marginInline: 0 }}>
          Join the team building platforms for ambitious African businesses.
        </p>
        <ul className="mt-10 space-y-4">
          {positions.map((p) => (
            <li key={p.id} className="rounded-xl border border-[var(--border-light)] bg-white p-6">
              <h2 className="text-xl font-semibold text-[var(--prady-navy,#053171)]">{p.title}</h2>
              <p className="mt-1 text-sm text-slate-500">
                {p.type} · {p.location}
              </p>
              <p className="mt-3 text-slate-600 whitespace-pre-wrap">{p.description}</p>
              <Link
                to={`/careers/${p.id}/apply`}
                className="mkt-btn mkt-btn--primary mt-4 inline-flex"
              >
                Apply
              </Link>
            </li>
          ))}
          {!positions.length && <p className="text-slate-500">No open positions right now.</p>}
        </ul>
      </div>
    </section>
  );
}
