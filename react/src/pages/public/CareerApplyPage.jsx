import React, { useEffect, useState } from "react";
import { useParams, Link } from "react-router-dom";
import { api, ensureCsrf } from "../../api/client";

export default function CareerApplyPage() {
  const { id } = useParams();
  const [loading, setLoading] = useState(false);
  const [feedback, setFeedback] = useState(null);
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
    setLoading(true);
    setFeedback(null);
    try {
      await ensureCsrf();
      const fd = new FormData();
      fd.append("position_id", id);
      Object.entries(form).forEach(([k, v]) => fd.append(k, v));
      if (resume) fd.append("resume", resume);
      
      // Simulate 0.5s network delay
      await new Promise(r => setTimeout(r, 500));
      
      await api("/public/careers/apply/", { method: "POST", body: fd });
      setFeedback({ type: "success", message: "Application received. Thank you!" });
    } catch (err) {
      setFeedback({ type: "error", message: err.message || "Failed to submit." });
    } finally {
      setLoading(false);
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
          <input type="file" accept=".pdf,.doc,.docx" onChange={(e) => setResume(e.target.files?.[0])} className="w-full text-sm" />
          <button type="submit" className="mkt-btn mkt-btn--primary w-full justify-center" disabled={loading}>
            {loading ? (
              <>
                <svg className="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4"></circle>
                  <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Submitting...
              </>
            ) : "Submit application"}
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
