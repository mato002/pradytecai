import React, { useEffect } from "react";

export default function FaqPage() {
  useEffect(() => {
    document.title = "FAQ | Prady Technologies";
  }, []);
  const faqs = [
    {
      q: "What industries does Prady serve?",
      a: "Microfinance, SACCOs, chamas, fleet & logistics, property, automotive, social commerce and tourism.",
    },
    {
      q: "How do I request a demo?",
      a: "Use the Contact page or Get Demo button and tell us which product you are evaluating.",
    },
    {
      q: "Where are you based?",
      a: "Nairobi, Kenya. We support institutions across African markets.",
    },
  ];
  return (
    <section className="mkt-section mkt-section--light">
      <div className="mkt-container" style={{ maxWidth: 800 }}>
        <h1 className="mkt-section__title mkt-section__title--left">FAQ</h1>
        <div className="mt-8 space-y-4">
          {faqs.map((f) => (
            <details key={f.q} className="rounded-xl border bg-white p-5">
              <summary className="cursor-pointer font-semibold text-[var(--prady-navy)]">{f.q}</summary>
              <p className="mt-3 text-slate-600">{f.a}</p>
            </details>
          ))}
        </div>
      </div>
    </section>
  );
}
