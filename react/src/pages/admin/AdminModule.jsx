import React, { useEffect, useState } from "react";
import { Navigate } from "react-router-dom";
import { api } from "../../api/client";
import { useAuth } from "../../auth/AuthContext";

function summary(row) {
  return (
    row.name ||
    row.title ||
    row.email ||
    row.key ||
    row.subject ||
    row.rule_key ||
    row.provider ||
    row.platform ||
    JSON.stringify(row).slice(0, 100)
  );
}

function statusClass(status) {
  const s = String(status || "").toLowerCase();
  if (["active", "published", "sent", "approved", "subscribed", "done"].includes(s)) {
    return "admin-status--ok";
  }
  if (["pending", "draft", "scheduled", "in_review", "open", "requested"].includes(s)) {
    return "admin-status--warn";
  }
  if (["failed", "error", "rejected", "cancelled", "critical"].includes(s)) {
    return "admin-status--danger";
  }
  return "";
}

export default function AdminModule({ resource, title, perm, single = false }) {
  const { can } = useAuth();
  const [rows, setRows] = useState([]);
  const [error, setError] = useState("");
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    if (!can(perm) && !can("careers.manage")) {
      setLoading(false);
      return;
    }
    setLoading(true);
    api(`/${resource}/`)
      .then((data) => {
        if (Array.isArray(data)) setRows(data);
        else if (data?.results) setRows(data.results);
        else setRows([data]);
      })
      .catch((err) => setError(err.message || "Failed to load"))
      .finally(() => setLoading(false));
  }, [resource, perm, can, single]);

  if (!can(perm) && !can("careers.manage")) return <Navigate to="/admin" replace />;

  return (
    <div className="admin-panel">
      <div className="admin-panel__head">
        <h2>
          {title} · {rows.length} records
        </h2>
      </div>
      {loading && <p className="p-6 text-[var(--admin-muted)]">Loading…</p>}
      {error && <p className="p-6 text-[var(--admin-danger)]">{error}</p>}
      {!loading && !error && (
        <div className="overflow-x-auto">
          <table className="admin-table">
            <thead>
              <tr>
                <th>ID</th>
                <th>Summary</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              {rows.map((row, idx) => {
                const status =
                  row.status ??
                  (row.is_active === false ? "inactive" : row.is_active ? "active" : "—");
                return (
                  <tr key={row.id ?? idx}>
                    <td className="text-[var(--admin-muted)]">{row.id ?? "—"}</td>
                    <td className="font-semibold text-[var(--admin-forest)]">{summary(row)}</td>
                    <td>
                      <span className={`admin-status ${statusClass(status)}`}>{status}</span>
                    </td>
                  </tr>
                );
              })}
              {!rows.length && (
                <tr>
                  <td colSpan={3} className="text-[var(--admin-muted)]">
                    No records yet.
                  </td>
                </tr>
              )}
            </tbody>
          </table>
        </div>
      )}
    </div>
  );
}
