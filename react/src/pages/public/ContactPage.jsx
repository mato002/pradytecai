import React, { useEffect, useState } from "react";
import { useSearchParams } from "react-router-dom";
import { api } from "../../api/client";
import { portfolio } from "../../data/portfolio";

export default function ContactPage() {
  const [params] = useSearchParams();
  const [form, setForm] = useState({
    name: "",
    company: "",
    email: "",
    phone: "",
    subject: params.get("product") ? `Demo: ${params.get("product")}` : "",
    message: "",
    topic: params.get("topic") || "",
  });
  const [status, setStatus] = useState("");

  useEffect(() => {
    document.title = "Contact | Prady Technologies";
  }, []);

  async function onSubmit(e) {
    e.preventDefault();
    setStatus("Sending…");
    try {
      await api("/public/contact/", { method: "POST", body: form });
      setStatus("Thank you — we received your message.");
      setForm((f) => ({ ...f, name: "", company: "", email: "", phone: "", subject: "", message: "" }));
    } catch (err) {
      setStatus(err.message || "Failed to send.");
    }
  }

  function set(k, v) {
    setForm((f) => ({ ...f, [k]: v }));
  }

  return (
    <section className="mkt-section mkt-section--light">
      <div className="mkt-container" style={{ display: "grid", gap: "2rem", gridTemplateColumns: "repeat(auto-fit,minmax(280px,1fr))" }}>
        <div>
          <h1 className="mkt-section__title mkt-section__title--left">Contact</h1>
          <p className="mkt-section__subtitle" style={{ textAlign: "left", marginInline: 0 }}>
            Tell us what you need and we&apos;ll route it to the right team.
          </p>
          <ul className="mt-6 space-y-3 text-sm" style={{ color: "var(--text-secondary)" }}>
            <li>
              <strong>Phone:</strong>{" "}
              <a href={portfolio.contact.phone_href}>{portfolio.contact.phone}</a>
            </li>
            <li>
              <strong>Email:</strong>{" "}
              <a href={portfolio.contact.email_href}>{portfolio.contact.email}</a>
            </li>
            <li>
              <strong>Location:</strong> {portfolio.contact.location}
            </li>
            <li>
              <strong>Hours:</strong> {portfolio.contact.hours}
            </li>
          </ul>
        </div>
        <form onSubmit={onSubmit} className="rounded-xl border bg-white p-6 shadow-sm space-y-3">
          {["name", "company", "email", "phone", "subject"].map((k) => (
            <input
              key={k}
              required={["name", "email", "subject"].includes(k)}
              className="w-full rounded-lg border border-slate-300 px-3 py-2.5"
              placeholder={k.charAt(0).toUpperCase() + k.slice(1)}
              value={form[k]}
              onChange={(e) => set(k, e.target.value)}
            />
          ))}
          <textarea
            required
            className="w-full rounded-lg border border-slate-300 px-3 py-2.5"
            rows={5}
            placeholder="Message"
            value={form.message}
            onChange={(e) => set("message", e.target.value)}
          />
          <button type="submit" className="mkt-btn mkt-btn--primary w-full justify-center">
            Send message
          </button>
          {status && <p className="text-sm text-slate-600">{status}</p>}
        </form>
      </div>
    </section>
  );
}
