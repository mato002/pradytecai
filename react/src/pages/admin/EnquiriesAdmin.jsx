import React, { useCallback, useEffect, useState } from "react";
import { Navigate } from "react-router-dom";
import { api, ensureCsrf } from "../../api/client";
import { useAuth } from "../../auth/AuthContext";

const STATUSES = [
  { value: "new", label: "New" },
  { value: "contacted", label: "Contacted" },
  { value: "in_progress", label: "In Progress" },
  { value: "completed", label: "Completed" },
  { value: "closed", label: "Closed" },
];

export default function EnquiriesAdmin() {
  const { can } = useAuth();
  const canManage = can("leads.manage") || can("inbox.reply") || can("leads.assign");
  const [rows, setRows] = useState([]);
  const [selected, setSelected] = useState(null);
  const [error, setError] = useState("");
  const [loading, setLoading] = useState(true);
  const [notice, setNotice] = useState("");

  const load = useCallback(() => {
    setLoading(true);
    api("/enquiries/?page_size=100")
      .then((data) => {
        const list = Array.isArray(data) ? data : data?.results || [];
        setRows(list);
        setSelected((prev) => {
          if (!prev) return list[0] || null;
          return list.find((r) => r.id === prev.id) || list[0] || null;
        });
      })
      .catch((err) => setError(err.message || "Failed to load"))
      .finally(() => setLoading(false));
  }, []);

  useEffect(() => {
    if (!can("leads.view")) return;
    load();
  }, [can, load]);

  if (!can("leads.view")) return <Navigate to="/admin" replace />;

  async function updateStatus(status) {
    if (!selected || !canManage) return;
    setNotice("");
    try {
      await ensureCsrf();
      const updated = await api(`/enquiries/${selected.id}/set_status/`, {
        method: "POST",
        body: { status },
      });
      setSelected(updated);
      setNotice("Status updated.");
      load();
    } catch (err) {
      setNotice(err.message || "Failed to update status");
    }
  }

  return (
    <div className="admin-panel">
      <div className="admin-panel__head">
        <h2>Leads / Enquiries · {rows.length}</h2>
      </div>
      {loading && <p className="p-6 text-[var(--admin-muted)]">Loading…</p>}
      {error && <p className="p-6 text-[var(--admin-danger)]">{error}</p>}
      {!loading && !error && (
        <div className="admin-split">
          <div className="overflow-x-auto">
            <table className="admin-table">
              <thead>
                <tr>
                  <th>When</th>
                  <th>Requester</th>
                  <th>Product</th>
                  <th>Type</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                {rows.map((row) => (
                  <tr
                    key={row.id}
                    className={selected?.id === row.id ? "admin-row--active" : ""}
                    onClick={() => setSelected(row)}
                    style={{ cursor: "pointer" }}
                  >
                    <td className="text-[var(--admin-muted)] text-sm">
                      {row.created_at ? new Date(row.created_at).toLocaleString() : "—"}
                    </td>
                    <td>
                      <div className="font-semibold">{row.name}</div>
                      <div className="text-xs text-[var(--admin-muted)]">{row.email}</div>
                    </td>
                    <td>{row.product_name || "—"}</td>
                    <td>{row.request_type || "—"}</td>
                    <td>
                      <span className="admin-status">{row.status}</span>
                    </td>
                  </tr>
                ))}
                {!rows.length && (
                  <tr>
                    <td colSpan={5} className="text-[var(--admin-muted)]">
                      No enquiries yet.
                    </td>
                  </tr>
                )}
              </tbody>
            </table>
          </div>

          {selected && (
            <div className="admin-form">
              <h3>Enquiry #{selected.id}</h3>
              <dl className="admin-dl">
                <div>
                  <dt>Requester</dt>
                  <dd>{selected.name}</dd>
                </div>
                <div>
                  <dt>Email</dt>
                  <dd>
                    <a href={`mailto:${selected.email}`}>{selected.email}</a>
                  </dd>
                </div>
                <div>
                  <dt>Phone</dt>
                  <dd>{selected.phone || "—"}</dd>
                </div>
                <div>
                  <dt>Company</dt>
                  <dd>{selected.company || "—"}</dd>
                </div>
                <div>
                  <dt>Product</dt>
                  <dd>{selected.product_name || "—"}</dd>
                </div>
                <div>
                  <dt>Request type</dt>
                  <dd>{selected.request_type || "—"}</dd>
                </div>
                <div>
                  <dt>Preferred date</dt>
                  <dd>
                    {selected.preferred_at
                      ? new Date(selected.preferred_at).toLocaleString()
                      : "—"}
                  </dd>
                </div>
                <div>
                  <dt>Source / page</dt>
                  <dd>
                    {selected.source || "—"}
                    {selected.landing_page ? ` · ${selected.landing_page}` : ""}
                  </dd>
                </div>
                <div>
                  <dt>Subject</dt>
                  <dd>{selected.subject}</dd>
                </div>
                <div>
                  <dt>Message</dt>
                  <dd className="whitespace-pre-wrap">{selected.message}</dd>
                </div>
                <div>
                  <dt>Received</dt>
                  <dd>
                    {selected.created_at
                      ? new Date(selected.created_at).toLocaleString()
                      : "—"}
                  </dd>
                </div>
              </dl>
              {canManage && (
                <FieldStatus
                  value={selected.status}
                  onChange={updateStatus}
                />
              )}
              {notice && <p className="text-sm text-[var(--admin-muted)]">{notice}</p>}
            </div>
          )}
        </div>
      )}
    </div>
  );
}

function FieldStatus({ value, onChange }) {
  return (
    <label className="admin-field">
      <span>Status</span>
      <select value={value || "new"} onChange={(e) => onChange(e.target.value)}>
        {STATUSES.map((s) => (
          <option key={s.value} value={s.value}>
            {s.label}
          </option>
        ))}
      </select>
    </label>
  );
}
