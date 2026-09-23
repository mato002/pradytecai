import React, { useCallback, useEffect, useState } from "react";
import { Link, Navigate } from "react-router-dom";
import { api, ensureCsrf } from "../../api/client";
import { useAuth } from "../../auth/AuthContext";

export default function ProductsAdmin() {
  const { can } = useAuth();
  const canManage = can("products.manage");
  const [rows, setRows] = useState([]);
  const [error, setError] = useState("");
  const [loading, setLoading] = useState(true);

  const load = useCallback(() => {
    setLoading(true);
    api("/products/?page_size=100")
      .then((data) => {
        if (Array.isArray(data)) setRows(data);
        else if (data?.results) setRows(data.results);
        else setRows([]);
      })
      .catch((err) => setError(err.message || "Failed to load"))
      .finally(() => setLoading(false));
  }, []);

  useEffect(() => {
    if (!can("products.view")) return;
    load();
  }, [can, load]);

  if (!can("products.view")) return <Navigate to="/admin" replace />;

  async function onDelete(row) {
    if (!canManage) return;
    if (!window.confirm(`Delete “${row.name}”? Linked enquiries will keep their history.`)) return;
    try {
      await ensureCsrf();
      await api(`/products/${row.id}/`, { method: "DELETE" });
      load();
    } catch (err) {
      setError(err.message || "Delete failed");
    }
  }

  async function toggleActive(row) {
    if (!canManage) return;
    try {
      await ensureCsrf();
      await api(`/products/${row.id}/`, {
        method: "PATCH",
        body: { is_active: !row.is_active },
      });
      load();
    } catch (err) {
      setError(err.message || "Update failed");
    }
  }

  return (
    <div className="admin-panel">
      <div className="admin-panel__head">
        <h2>Products · {rows.length}</h2>
        {canManage && (
          <Link to="/admin/products/new" className="admin-btn admin-btn--primary">
            New product
          </Link>
        )}
      </div>
      {loading && <p className="p-6 text-[var(--admin-muted)]">Loading…</p>}
      {error && <p className="p-6 text-[var(--admin-danger)]">{error}</p>}
      {!loading && (
        <div className="overflow-x-auto">
          <table className="admin-table">
            <thead>
              <tr>
                <th>Order</th>
                <th>Product</th>
                <th>Status</th>
                <th />
              </tr>
            </thead>
            <tbody>
              {rows.map((row) => (
                <tr key={row.id}>
                  <td className="text-[var(--admin-muted)]">{row.order}</td>
                  <td>
                    <Link to={`/admin/products/${row.id}/edit`} className="admin-linkish">
                      {row.name}
                    </Link>
                    <div className="text-xs text-[var(--admin-muted)]">{row.slug}</div>
                  </td>
                  <td>
                    <span
                      className={`admin-status ${
                        row.is_active ? "admin-status--ok" : "admin-status--warn"
                      }`}
                    >
                      {row.is_active ? "active" : "inactive"}
                      {row.is_featured ? " · featured" : ""}
                    </span>
                  </td>
                  <td className="space-x-2 whitespace-nowrap">
                    <Link to={`/products/${row.slug}`} className="admin-linkish" target="_blank" rel="noreferrer">
                      View
                    </Link>
                    {canManage && (
                      <>
                        <Link to={`/admin/products/${row.id}/edit`} className="admin-linkish">
                          Edit
                        </Link>
                        <button type="button" className="admin-linkish" onClick={() => toggleActive(row)}>
                          {row.is_active ? "Deactivate" : "Activate"}
                        </button>
                        <button type="button" className="admin-linkish text-red-700" onClick={() => onDelete(row)}>
                          Delete
                        </button>
                      </>
                    )}
                  </td>
                </tr>
              ))}
              {!rows.length && (
                <tr>
                  <td colSpan={4} className="text-[var(--admin-muted)]">
                    No products yet. Run <code>seed_products</code> or{" "}
                    {canManage ? (
                      <Link to="/admin/products/new" className="admin-linkish">
                        create one
                      </Link>
                    ) : (
                      "create one"
                    )}
                    .
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
