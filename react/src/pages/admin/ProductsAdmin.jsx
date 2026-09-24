import React, { useCallback, useEffect, useState } from "react";
import { Link, Navigate } from "react-router-dom";
import { api, ensureCsrf } from "../../api/client";
import { useAuth } from "../../auth/AuthContext";
import { AdminToast, AdminConfirmModal } from "../../components/common/AdminToast";

export default function ProductsAdmin() {
  const { can } = useAuth();
  const canManage = can("products.manage");
  const [rows, setRows] = useState([]);
  const [searchQuery, setSearchQuery] = useState("");
  const [loading, setLoading] = useState(true);
  const [toast, setToast] = useState(null);
  const [confirmModal, setConfirmModal] = useState({
    open: false,
    title: "",
    message: "",
    confirmText: "Delete",
    confirmVariant: "danger",
    loading: false,
    onConfirm: null,
  });

  const showSuccess = useCallback((message, title = "Success") => {
    setToast({ type: "success", title, message });
  }, []);

  const showError = useCallback((message, title = "Error") => {
    setToast({ type: "error", title, message });
  }, []);

  const load = useCallback(() => {
    setLoading(true);
    api("/products/?page_size=100")
      .then((data) => {
        if (Array.isArray(data)) setRows(data);
        else if (data?.results) setRows(data.results);
        else setRows([]);
      })
      .catch((err) => showError(err.message || "Failed to load products"))
      .finally(() => setLoading(false));
  }, [showError]);

  useEffect(() => {
    if (!can("products.view")) return;
    load();
  }, [can, load]);

  if (!can("products.view")) return <Navigate to="/admin" replace />;

  function onDelete(row) {
    if (!canManage) return;
    setConfirmModal({
      open: true,
      title: "Delete Product",
      message: `Are you sure you want to delete “${row.name}”? All gallery images, videos, and settings will be permanently removed. Linked enquiries will keep their history.`,
      confirmText: "Delete Product",
      confirmVariant: "danger",
      loading: false,
      onConfirm: async () => {
        setConfirmModal((prev) => ({ ...prev, loading: true }));
        try {
          await ensureCsrf();
          await api(`/products/${row.id}/`, { method: "DELETE" });
          setConfirmModal((prev) => ({ ...prev, open: false, loading: false }));
          showSuccess(`Product “${row.name}” was deleted successfully.`);
          load();
        } catch (err) {
          setConfirmModal((prev) => ({ ...prev, loading: false }));
          showError(err.message || "Failed to delete product.");
        }
      },
    });
  }

  async function toggleActive(row) {
    if (!canManage) return;
    const nextStatus = !row.is_active;
    try {
      await ensureCsrf();
      await api(`/products/${row.id}/`, {
        method: "PATCH",
        body: { is_active: nextStatus },
      });
      showSuccess(`Product “${row.name}” is now ${nextStatus ? "Active" : "Inactive"}.`);
      load();
    } catch (err) {
      showError(err.message || "Update status failed.");
    }
  }

  const filteredRows = rows.filter((r) => {
    const q = searchQuery.toLowerCase();
    return (
      (r.name && r.name.toLowerCase().includes(q)) ||
      (r.slug && r.slug.toLowerCase().includes(q)) ||
      (r.market && r.market.toLowerCase().includes(q)) ||
      (r.group_key && r.group_key.toLowerCase().includes(q))
    );
  });

  return (
    <div className="admin-editor-wrap">
      <header className="admin-editor-header">
        <div className="admin-editor-header__main">
          <span className="text-xs font-semibold uppercase tracking-wider text-[var(--admin-forest-mid)]">
            Catalog Management
          </span>
          <h1 className="admin-editor-header__title">
            Products Directory
            <span className="admin-status admin-status--ok font-normal text-xs">
              {rows.length} total
            </span>
          </h1>
        </div>
        {canManage && (
          <Link to="/admin/products/new" className="admin-btn admin-btn--primary">
            + Create New Product
          </Link>
        )}
      </header>

      <div className="admin-card-section">
        <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
          <div className="relative flex-1 max-w-md">
            <input
              type="text"
              placeholder="Search products by name, slug, or market..."
              value={searchQuery}
              onChange={(e) => setSearchQuery(e.target.value)}
              className="w-full pl-9 pr-4 py-2 text-sm border border-[var(--admin-border)] rounded-lg outline-none focus:border-[var(--admin-forest)]"
            />
            <span className="absolute left-3 top-2.5 text-gray-400 text-sm">🔍</span>
          </div>

          <span className="text-xs text-[var(--admin-muted)]">
            Showing {filteredRows.length} of {rows.length} items
          </span>
        </div>

        {loading && (
          <div className="text-center py-12">
            <p className="text-[var(--admin-muted)]">Loading products catalog…</p>
          </div>
        )}

        {!loading && (
          <div className="overflow-x-auto rounded-lg border border-[var(--admin-border)]">
            <table className="admin-table">
              <thead>
                <tr>
                  <th className="w-16">Priority</th>
                  <th>Product Details</th>
                  <th>Group</th>
                  <th>Status</th>
                  <th className="text-right">Actions</th>
                </tr>
              </thead>
              <tbody>
                {filteredRows.map((row) => (
                  <tr key={row.id}>
                    <td className="font-semibold text-center text-[var(--admin-muted)]">#{row.order}</td>
                    <td>
                      <Link to={`/admin/products/${row.id}/edit`} className="font-bold text-[var(--admin-forest-deep)] hover:underline">
                        {row.name}
                      </Link>
                      <div className="text-xs text-[var(--admin-muted)] font-mono">/products/{row.slug}</div>
                    </td>
                    <td>
                      <span className="text-xs font-medium px-2 py-1 rounded background-[#f0f4f2] text-[var(--admin-forest)] uppercase tracking-wider">
                        {row.group_key || "General"}
                      </span>
                    </td>
                    <td>
                      <span
                        className={`admin-status ${
                          row.is_active ? "admin-status--ok" : "admin-status--warn"
                        }`}
                      >
                        {row.is_active ? "Active" : "Draft"}
                        {row.is_featured ? " · Featured" : ""}
                      </span>
                    </td>
                    <td className="text-right space-x-3 whitespace-nowrap">
                      <Link to={`/products/${row.slug}`} className="admin-linkish text-xs" target="_blank" rel="noreferrer">
                        View ↗
                      </Link>
                      {canManage && (
                        <>
                          <Link to={`/admin/products/${row.id}/edit`} className="admin-btn text-xs py-1 px-2">
                            Edit
                          </Link>
                          <button type="button" className="admin-linkish text-xs" onClick={() => toggleActive(row)}>
                            {row.is_active ? "Deactivate" : "Activate"}
                          </button>
                          <button type="button" className="admin-linkish text-xs text-red-700 hover:text-red-900" onClick={() => onDelete(row)}>
                            Delete
                          </button>
                        </>
                      )}
                    </td>
                  </tr>
                ))}

                {!filteredRows.length && (
                  <tr>
                    <td colSpan={5} className="text-center py-8 text-[var(--admin-muted)]">
                      {searchQuery ? (
                        <span>No products found matching “{searchQuery}”.</span>
                      ) : (
                        <span>
                          No products in database.{" "}
                          {canManage && (
                            <Link to="/admin/products/new" className="admin-linkish">
                              Create your first product
                            </Link>
                          )}
                        </span>
                      )}
                    </td>
                  </tr>
                )}
              </tbody>
            </table>
          </div>
        )}
      </div>

      {toast && (
        <AdminToast
          type={toast.type}
          title={toast.title}
          message={toast.message}
          onClose={() => setToast(null)}
        />
      )}

      <AdminConfirmModal
        open={confirmModal.open}
        title={confirmModal.title}
        message={confirmModal.message}
        confirmText={confirmModal.confirmText}
        cancelText={confirmModal.cancelText}
        confirmVariant={confirmModal.confirmVariant}
        loading={confirmModal.loading}
        onConfirm={confirmModal.onConfirm}
        onCancel={() => setConfirmModal((prev) => ({ ...prev, open: false }))}
      />
    </div>
  );
}

