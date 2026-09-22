import React, { useEffect, useState } from "react";
import { useSearchParams } from "react-router-dom";
import { api } from "../../api/client";
import { portfolio } from "../../data/portfolio";

export default function ContactPage() {
  const [params] = useSearchParams();
  const productName = params.get("product") || "";
  const productSlug = params.get("product_slug") || "";
  const requestType = params.get("request_type") || (productSlug || productName ? "demo" : "contact");

  const [form, setForm] = useState({
    name: "",
    company: "",
    email: "",
    phone: "",
    subject: productName ? `Demo: ${productName}` : "",
    message: "",
    topic: params.get("topic") || "",
    preferred_at: "",
    request_type: requestType,
    product_slug: productSlug,
  });
  const [loading, setLoading] = useState(false);
  const [feedback, setFeedback] = useState(null);

  useEffect(() => {
    document.title = "Contact | Prady Technologies";
  }, []);

  async function onSubmit(e) {
    e.preventDefault();
    setLoading(true);
    setFeedback(null);
    try {
      const payload = {
        name: form.name,
        company: form.company,
        email: form.email,
        phone: form.phone,
        subject: form.subject,
        message: form.message,
        topic: form.topic,
        request_type: form.request_type || requestType,
        product_slug: form.product_slug || undefined,
        preferred_at: form.preferred_at || undefined,
        source: productSlug ? "product_page" : "contact_page",
        landing_page: window.location.pathname + window.location.search,
      };
      
      // Simulate 0.5s network delay
      await new Promise(r => setTimeout(r, 500));
      
      await api("/public/contact/", { method: "POST", body: payload });
      setFeedback({ type: "success", message: "Thank you — we received your message." });
      setForm((f) => ({
        ...f,
        name: "",
        company: "",
        email: "",
        phone: "",
        subject: productName ? `Demo: ${productName}` : "",
        message: "",
        preferred_at: "",
      }));
    } catch (err) {
      setFeedback({ type: "error", message: err.message || "Failed to send." });
    } finally {
      setLoading(false);
    }
  }

  function set(k, v) {
    setForm((f) => ({ ...f, [k]: v }));
  }

  return (
    <section className="mkt-section mkt-section--light">
      <div
        className="mkt-container"
        style={{ display: "grid", gap: "2rem", gridTemplateColumns: "repeat(auto-fit,minmax(280px,1fr))" }}
      >
        <div>
          <h1 className="mkt-section__title mkt-section__title--left">Contact</h1>
          <p className="mkt-section__subtitle" style={{ textAlign: "left", marginInline: 0 }}>
            Tell us what you need and we&apos;ll route it to the right team.
          </p>
          {(productName || productSlug) && (
            <p className="mt-4 text-sm" style={{ color: "var(--text-secondary)" }}>
              Request related to: <strong>{productName || productSlug}</strong>
            </p>
          )}
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
          {(form.request_type === "demo" || productSlug || productName) && (
            <label className="block text-sm text-slate-600">
              Preferred demo / contact date (optional)
              <input
                type="datetime-local"
                className="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2.5"
                value={form.preferred_at}
                onChange={(e) => set("preferred_at", e.target.value)}
              />
            </label>
          )}
          <textarea
            required
            className="w-full rounded-lg border border-slate-300 px-3 py-2.5"
            rows={5}
            placeholder="Message"
            value={form.message}
            onChange={(e) => set("message", e.target.value)}
          />
          <button type="submit" className="mkt-btn mkt-btn--primary w-full justify-center" disabled={loading}>
            {loading ? (
              <>
                <svg className="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4"></circle>
                  <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Sending...
              </>
            ) : "Send message"}
          </button>
          {feedback && (
            <div className={`mt-3 p-3 rounded-lg text-sm border ${
              feedback.type === "success" 
                ? "bg-green-50 text-green-700 border-green-200" 
                : "bg-red-50 text-red-700 border-red-200"
            }`}>
              {feedback.message}
            </div>
          )}
        </form>
      </div>
    </section>
  );
}
