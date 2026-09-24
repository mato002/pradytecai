import React, { useEffect, useRef } from "react";

/**
 * Custom designed floating Alert / Toast notification.
 * Appears floating without shifting the page layout.
 *
 * @param {'success' | 'error' | 'info' | 'warning'} type
 * @param {string} title
 * @param {string} message
 * @param {Function} onClose
 * @param {number} [duration=4000] auto-dismiss duration in ms (0 to disable)
 */
export function AdminToast({
  type = "success",
  title,
  message,
  onClose,
  duration = 4500,
}) {
  useEffect(() => {
    if (!message && !title) return undefined;
    if (!duration || duration <= 0) return undefined;

    const timer = setTimeout(() => {
      onClose?.();
    }, duration);

    return () => clearTimeout(timer);
  }, [message, title, duration, onClose]);

  if (!message && !title) return null;

  const isSuccess = type === "success";
  const isError = type === "error";
  const isWarning = type === "warning";

  const defaultTitle = isSuccess
    ? "Success"
    : isError
    ? "Error"
    : isWarning
    ? "Warning"
    : "Information";

  return (
    <aside
      className="admin-toast-portal"
      aria-live="polite"
      aria-atomic="true"
      role="status"
    >
      <div
        className={`admin-toast admin-toast--${type}`}
        role={isError ? "alert" : "status"}
      >
        <div className="admin-toast__icon-wrap">
          {isSuccess && (
            <svg
              className="admin-toast__icon admin-toast__icon--success"
              viewBox="0 0 20 20"
              fill="currentColor"
              aria-hidden="true"
            >
              <path
                fillRule="evenodd"
                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z"
                clipRule="evenodd"
              />
            </svg>
          )}
          {isError && (
            <svg
              className="admin-toast__icon admin-toast__icon--error"
              viewBox="0 0 20 20"
              fill="currentColor"
              aria-hidden="true"
            >
              <path
                fillRule="evenodd"
                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-5a.75.75 0 01.75.75v4.5a.75.75 0 01-1.5 0v-4.5A.75.75 0 0110 5zm0 10a1 1 0 100-2 1 1 0 000 2z"
                clipRule="evenodd"
              />
            </svg>
          )}
          {(isWarning || (!isSuccess && !isError)) && (
            <svg
              className="admin-toast__icon admin-toast__icon--info"
              viewBox="0 0 20 20"
              fill="currentColor"
              aria-hidden="true"
            >
              <path
                fillRule="evenodd"
                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a.75.75 0 000 1.5h.253a.25.25 0 01.244.304l-.459 2.066A1.75 1.75 0 0010.747 15H11a.75.75 0 000-1.5h-.253a.25.25 0 01-.244-.304l.459-2.066A1.75 1.75 0 009.253 9H9z"
                clipRule="evenodd"
              />
            </svg>
          )}
        </div>

        <div className="admin-toast__content">
          <h4 className="admin-toast__title">{title || defaultTitle}</h4>
          <p className="admin-toast__message">{message}</p>
        </div>

        <button
          type="button"
          className="admin-toast__close"
          onClick={onClose}
          aria-label="Dismiss notification"
        >
          <svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
            <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
          </svg>
        </button>
      </div>
    </aside>
  );
}

/**
 * Custom Confirmation Modal for Deletions and Dangerous Actions.
 * Replaces window.confirm with a modern, beautiful modal.
 */
export function AdminConfirmModal({
  open,
  title = "Confirm Deletion",
  message = "Are you sure you want to proceed? This action cannot be undone.",
  confirmText = "Delete",
  cancelText = "Cancel",
  confirmVariant = "danger",
  onConfirm,
  onCancel,
  loading = false,
}) {
  const cancelBtnRef = useRef(null);

  useEffect(() => {
    if (!open) return undefined;
    const handleKeyDown = (e) => {
      if (e.key === "Escape") onCancel?.();
    };
    document.addEventListener("keydown", handleKeyDown);
    cancelBtnRef.current?.focus();
    return () => document.removeEventListener("keydown", handleKeyDown);
  }, [open, onCancel]);

  if (!open) return null;

  return (
    <div className="admin-modal-backdrop" role="presentation" onClick={onCancel}>
      <div
        className="admin-confirm-modal"
        role="dialog"
        aria-modal="true"
        aria-labelledby="confirm-modal-title"
        onClick={(e) => e.stopPropagation()}
      >
        <div className="admin-confirm-modal__header">
          <div className={`admin-confirm-modal__icon admin-confirm-modal__icon--${confirmVariant}`}>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
              <path
                strokeLinecap="round"
                strokeLinejoin="round"
                d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"
              />
            </svg>
          </div>
          <div className="admin-confirm-modal__text">
            <h3 id="confirm-modal-title" className="admin-confirm-modal__title">
              {title}
            </h3>
            <p className="admin-confirm-modal__message">{message}</p>
          </div>
        </div>

        <div className="admin-confirm-modal__actions">
          <button
            ref={cancelBtnRef}
            type="button"
            className="admin-btn admin-btn--secondary"
            onClick={onCancel}
            disabled={loading}
          >
            {cancelText}
          </button>
          <button
            type="button"
            className={`admin-btn ${
              confirmVariant === "danger"
                ? "admin-btn--danger"
                : "admin-btn--primary"
            }`}
            onClick={onConfirm}
            disabled={loading}
          >
            {loading ? "Processing…" : confirmText}
          </button>
        </div>
      </div>
    </div>
  );
}
