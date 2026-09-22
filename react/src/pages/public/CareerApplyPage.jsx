import React, { useEffect, useState } from "react";
import { useParams, Link } from "react-router-dom";
import { api, ensureCsrf } from "../../api/client";

export default function CareerApplyPage() {
  const { id } = useParams();
  const [status, setStatus] = useState("");
  const [form, setForm] = useState({
    name: "",
    email: "",
    phone: "",
    cover_letter: "",
  });
  const [resume, setResume] = useState(null);

  useEffect(() => {
    document.title = "Apply | Prady Technologies";
  }, []);

  async function onSubmit(e) {
    e.preventDefault();
    setStatus("Submitting…");
    try {
      await ensureCsrf();
      const fd = new FormData();
      fd.append("position_id", id);
      Object.entries(form).forEach(([k, v]) => fd.append(k, v));
      if (resume) fd.append("resume", resume);
      await api("/public/careers/apply/", { method: "POST", body: fd });
      setStatus("Application received. Thank you!");
    } catch (err) {
      setStatus(err.message || "Failed to submit.");
    }
  }

  return (
    <section className="mkt-section mkt-section--light">
      <div className="mkt-container" style={{ maxWidth: 640 }}>
        <Link to="/careers" className="text-sm text-[var(--prady-sky)]">
          ← Back to careers
        </Link>
        <h1 className="mkt-section__title mkt-section__title--left mt-4">Apply</h1>
        <form onSubmit={onSubmit} className="mt-6 space-y-3 rounded-xl border bg-white p-6">
          {["name", "email", "phone"].map((k) => (
            <input
              key={k}
              required={k !== "phone"}
              className="w-full rounded-lg border px-3 py-2.5"
              placeholder={k}
              value={form[k]}
              onChange={(e) => setForm((f) => ({ ...f, [k]: e.target.value }))}
            />
          ))}
          <textarea
            required
            className="w-full rounded-lg border px-3 py-2.5"
            rows={6}
            placeholder="Cover letter"
            value={form.cover_letter}
            onChange={(e) => setForm((f) => ({ ...f, cover_letter: e.target.value }))}
          />
          <input type="file" accept=".pdf,.doc,.docx" onChange={(e) => setResume(e.target.files?.[0])} />
          <button type="submit" className="mkt-btn mkt-btn--primary">
            Submit application
          </button>
          {status && <p className="text-sm text-slate-600">{status}</p>}
        </form>
      </div>
    </section>
  );
}
