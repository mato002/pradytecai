import React, { useEffect, useMemo, useState } from "react";
import { api } from "../../api/client";
import { useAuth } from "../../auth/AuthContext";
import { AdminToast, AdminConfirmModal } from "../../components/common/AdminToast";
import PradyIcon from "../../components/marketing/PradyIcon";

export const CHANNEL_CONFIG = {
  phone: {
    label: "Phone / Call",
    icon: "phone",
    badge: "Direct Contact",
    emoji: "📞",
    placeholder: "+254 722 295 194",
    color: "#0284c7",
    bgColor: "#e0f2fe",
    help: "Customer care or office telephone line",
    isSocial: false,
  },
  whatsapp: {
    label: "WhatsApp",
    icon: "whatsapp",
    badge: "Direct Contact",
    emoji: "💬",
    placeholder: "+254 722 295 194",
    color: "#16a34a",
    bgColor: "#dcfce7",
    help: "WhatsApp chat number for direct support",
    isSocial: false,
  },
  email: {
    label: "Contact Email",
    icon: "email",
    badge: "Direct Contact",
    emoji: "✉️",
    placeholder: "marketing@pradytecai.com",
    color: "#ea580c",
    bgColor: "#ffedd5",
    help: "Primary inbox for public inquiries",
    isSocial: false,
  },
  linkedin: {
    label: "LinkedIn",
    icon: "linkedin",
    badge: "Social Media",
    emoji: "💼",
    placeholder: "https://www.linkedin.com/company/prady-technologies-ltd",
    color: "#0a66c2",
    bgColor: "#e8f0fe",
    help: "Company LinkedIn profile URL",
    isSocial: true,
  },
  twitter: {
    label: "X / Twitter",
    icon: "twitter",
    badge: "Social Media",
    emoji: "𝕏",
    placeholder: "https://x.com/pradytecai or @pradytecai",
    color: "#0f172a",
    bgColor: "#f1f5f9",
    help: "X / Twitter handle or profile URL",
    isSocial: true,
  },
  facebook: {
    label: "Facebook",
    icon: "facebook",
    badge: "Social Media",
    emoji: "📘",
    placeholder: "https://facebook.com/pradytechnologies",
    color: "#1877f2",
    bgColor: "#e7f0fd",
    help: "Facebook business page URL",
    isSocial: true,
  },
  instagram: {
    label: "Instagram",
    icon: "instagram",
    badge: "Social Media",
    emoji: "📷",
    placeholder: "https://instagram.com/pradytechnologies or @handle",
    color: "#e1306c",
    bgColor: "#fce7f3",
    help: "Instagram profile URL or handle",
    isSocial: true,
  },
  youtube: {
    label: "YouTube",
    icon: "youtube",
    badge: "Social Media",
    emoji: "▶️",
    placeholder: "https://youtube.com/@pradytecai",
    color: "#dc2626",
    bgColor: "#fee2e2",
    help: "YouTube channel URL",
    isSocial: true,
  },
  tiktok: {
    label: "TikTok",
    icon: "tiktok",
    badge: "Social Media",
    emoji: "🎵",
    placeholder: "@pradytecai or https://tiktok.com/@pradytecai",
    color: "#000000",
    bgColor: "#f3f4f6",
    help: "TikTok handle or account URL",
    isSocial: true,
  },
  telegram: {
    label: "Telegram",
    icon: "telegram",
    badge: "Social Media",
    emoji: "✈️",
    placeholder: "@pradytecai or https://t.me/pradytecai",
    color: "#229ed9",
    bgColor: "#e0f2fe",
    help: "Telegram group or handle",
    isSocial: true,
  },
  github: {
    label: "GitHub",
    icon: "github",
    badge: "Social Media",
    emoji: "🐙",
    placeholder: "https://github.com/pradytecai",
    color: "#24292e",
    bgColor: "#f3f4f6",
    help: "GitHub organization or repository URL",
    isSocial: true,
  },
  location: {
    label: "Physical Location",
    icon: "location",
    badge: "Direct Contact",
    emoji: "📍",
    placeholder: "Nairobi, Kenya",
    color: "#6366f1",
    bgColor: "#e0e7ff",
    help: "Headquarters or office address",
    isSocial: false,
  },
  hours: {
    label: "Business Hours",
    icon: "clock",
    badge: "Direct Contact",
    emoji: "🕒",
    placeholder: "Mon – Fri: 8:00 AM – 6:00 PM EAT",
    color: "#d97706",
    bgColor: "#fef3c7",
    help: "Office working hours for customer support",
    isSocial: false,
  },
  website: {
    label: "Website / Custom Link",
    icon: "globe",
    badge: "Custom Link",
    emoji: "🌐",
    placeholder: "https://app.pradytec.com",
    color: "#059669",
    bgColor: "#d1fae5",
    help: "Platform link, portal, or external link",
    isSocial: true,
  },
  other: {
    label: "Other Channel",
    icon: "support",
    badge: "Custom Link",
    emoji: "🔗",
    placeholder: "Value or link",
    color: "#475569",
    bgColor: "#f1f5f9",
    help: "Custom contact channel",
    isSocial: false,
  },
};

const BLANK_FORM = {
  channel_type: "phone",
  label: "",
  value: "",
  href: "",
  description: "",
  is_active: true,
  is_primary: false,
  display_order: 10,
};

export default function SettingsAdmin() {
  const { can } = useAuth();
  const [activeTab, setActiveTab] = useState("channels"); // 'channels' | 'site_settings'

  // Channels state
  const [channels, setChannels] = useState([]);
  const [filter, setFilter] = useState("all"); // 'all' | 'direct' | 'social'
  const [loading, setLoading] = useState(true);

  // Modal editor state
  const [modalOpen, setModalOpen] = useState(false);
  const [editingId, setEditingId] = useState(null);
  const [form, setForm] = useState(BLANK_FORM);
  const [submitting, setSubmitting] = useState(false);

  // Deletion modal state
  const [deleteTarget, setDeleteTarget] = useState(null);
  const [deleting, setDeleting] = useState(false);

  // Site settings state
  const [siteSettings, setSiteSettings] = useState([]);
  const [settingsLoading, setSettingsLoading] = useState(false);
  const [savingSettings, setSavingSettings] = useState(false);

  // Toast state
  const [toast, setToast] = useState({ open: false, type: "success", title: "", message: "" });

  function showToast(type, title, message) {
    setToast({ open: true, type, title, message });
  }

  function closeToast() {
    setToast((t) => ({ ...t, open: false }));
  }

  // Load channels
  async function loadChannels() {
    try {
      setLoading(true);
      const data = await api("/contact-channels/");
      const list = Array.isArray(data) ? data : data?.results || [];
      setChannels(list);
    } catch (err) {
      showToast("error", "Failed to Load", err.message || "Could not fetch contact channels.");
    } finally {
      setLoading(false);
    }
  }

  // Load site settings
  async function loadSiteSettings() {
    try {
      setSettingsLoading(true);
      const data = await api("/settings/");
      const list = Array.isArray(data) ? data : data?.results || [];
      setSiteSettings(list);
    } catch (err) {
      showToast("error", "Error", err.message || "Failed to load site settings.");
    } finally {
      setSettingsLoading(false);
    }
  }

  useEffect(() => {
    loadChannels();
  }, []);

  useEffect(() => {
    if (activeTab === "site_settings") {
      loadSiteSettings();
    }
  }, [activeTab]);

  // Open modal for Create
  function handleOpenCreate() {
    setEditingId(null);
    setForm({
      ...BLANK_FORM,
      display_order: (channels.length + 1) * 10,
    });
    setModalOpen(true);
  }

  // Open modal for Edit
  function handleOpenEdit(channel) {
    setEditingId(channel.id);
    setForm({
      channel_type: channel.channel_type || "phone",
      label: channel.label || "",
      value: channel.value || "",
      href: channel.href || "",
      description: channel.description || "",
      is_active: channel.is_active !== false,
      is_primary: Boolean(channel.is_primary),
      display_order: channel.display_order ?? 10,
    });
    setModalOpen(true);
  }

  // Save Channel (Create / Update)
  async function handleSaveChannel(e) {
    e.preventDefault();
    if (!form.label.trim()) {
      showToast("warning", "Missing Label", "Please provide a label for this channel (e.g. Sales, WhatsApp Chat).");
      return;
    }
    if (!form.value.trim()) {
      showToast("warning", "Missing Value", "Please enter the phone number, email address, or social URL.");
      return;
    }

    try {
      setSubmitting(true);
      const payload = {
        channel_type: form.channel_type,
        label: form.label.trim(),
        value: form.value.trim(),
        href: form.href?.trim() || null,
        description: form.description?.trim() || null,
        is_active: form.is_active,
        is_primary: form.is_primary,
        display_order: parseInt(form.display_order, 10) || 0,
      };

      if (editingId) {
        await api(`/contact-channels/${editingId}/`, {
          method: "PUT",
          body: JSON.stringify(payload),
        });
        showToast("success", "Channel Updated", `Successfully updated "${form.label}".`);
      } else {
        await api("/contact-channels/", {
          method: "POST",
          body: JSON.stringify(payload),
        });
        showToast("success", "Channel Created", `Added "${form.label}" to contact channels.`);
      }

      setModalOpen(false);
      await loadChannels();
    } catch (err) {
      showToast("error", "Save Failed", err.message || "An error occurred while saving.");
    } finally {
      setSubmitting(false);
    }
  }

  // Toggle Active status inline
  async function handleToggleActive(channel) {
    try {
      const nextActive = !channel.is_active;
      await api(`/contact-channels/${channel.id}/`, {
        method: "PATCH",
        body: JSON.stringify({ is_active: nextActive }),
      });
      setChannels((prev) =>
        prev.map((c) => (c.id === channel.id ? { ...c, is_active: nextActive } : c))
      );
      showToast(
        "success",
        nextActive ? "Channel Activated" : "Channel Hidden",
        `"${channel.label}" is now ${nextActive ? "visible" : "hidden"} on the public website.`
      );
    } catch (err) {
      showToast("error", "Update Failed", err.message || "Could not toggle channel status.");
    }
  }

  // Delete Channel confirmation
  async function handleConfirmDelete() {
    if (!deleteTarget) return;
    try {
      setDeleting(true);
      await api(`/contact-channels/${deleteTarget.id}/`, {
        method: "DELETE",
      });
      setChannels((prev) => prev.filter((c) => c.id !== deleteTarget.id));
      showToast("success", "Channel Deleted", `Deleted "${deleteTarget.label}".`);
      setDeleteTarget(null);
    } catch (err) {
      showToast("error", "Delete Failed", err.message || "Failed to delete channel.");
    } finally {
      setDeleting(false);
    }
  }

  // Filtered channels list
  const filteredChannels = useMemo(() => {
    if (filter === "direct") {
      return channels.filter((c) => !CHANNEL_CONFIG[c.channel_type]?.isSocial);
    }
    if (filter === "social") {
      return channels.filter((c) => CHANNEL_CONFIG[c.channel_type]?.isSocial);
    }
    return channels;
  }, [channels, filter]);

  const activeCount = channels.filter((c) => c.is_active).length;
  const socialCount = channels.filter((c) => CHANNEL_CONFIG[c.channel_type]?.isSocial).length;
  const directCount = channels.filter((c) => !CHANNEL_CONFIG[c.channel_type]?.isSocial).length;

  const currentCfg = CHANNEL_CONFIG[form.channel_type] || CHANNEL_CONFIG.other;

  return (
    <div className="admin-settings-workspace">
      {/* Toast Notification */}
      {toast.open && (
        <AdminToast
          type={toast.type}
          title={toast.title}
          message={toast.message}
          onClose={closeToast}
        />
      )}

      {/* Confirmation Modal for Delete */}
      <AdminConfirmModal
        open={Boolean(deleteTarget)}
        title="Delete Contact Channel"
        message={`Are you sure you want to delete "${deleteTarget?.label}" (${deleteTarget?.value})? This will immediately remove it from the public Contact page and Footer.`}
        confirmText="Delete Channel"
        confirmVariant="danger"
        loading={deleting}
        onConfirm={handleConfirmDelete}
        onCancel={() => setDeleteTarget(null)}
      />

      {/* Workspace Header */}
      <header className="admin-page-header">
        <div className="admin-page-header__main">
          <h1 className="admin-page-header__title">Settings & Contact Channels</h1>
          <p className="admin-page-header__desc">
            Configure phone numbers, WhatsApp, email addresses, and public social media handles
            displayed on the Contact page, Footer, and navigation.
          </p>
        </div>

        {activeTab === "channels" && (
          <button
            type="button"
            className="admin-btn admin-btn--primary"
            onClick={handleOpenCreate}
          >
            <span aria-hidden="true">+</span>
            <span>Add New Channel</span>
          </button>
        )}
      </header>

      {/* Tabs */}
      <div className="admin-tabs" role="tablist">
        <button
          type="button"
          role="tab"
          aria-selected={activeTab === "channels"}
          className={`admin-tab ${activeTab === "channels" ? "is-active" : ""}`}
          onClick={() => setActiveTab("channels")}
        >
          <span>Contact & Social Media</span>
          <span className="admin-tab__badge">{channels.length}</span>
        </button>
        <button
          type="button"
          role="tab"
          aria-selected={activeTab === "site_settings"}
          className={`admin-tab ${activeTab === "site_settings" ? "is-active" : ""}`}
          onClick={() => setActiveTab("site_settings")}
        >
          <span>General Site Settings</span>
        </button>
      </div>

      {/* TAB 1: Contact & Social Media Channels */}
      {activeTab === "channels" && (
        <div className="admin-channels-panel">
          {/* Quick Metrics Bar */}
          <div className="admin-metrics-row">
            <div className="admin-metric-card">
              <span className="admin-metric-card__num">{channels.length}</span>
              <span className="admin-metric-card__label">Total Channels</span>
            </div>
            <div className="admin-metric-card">
              <span className="admin-metric-card__num text-emerald-600">{activeCount}</span>
              <span className="admin-metric-card__label">Active on Website</span>
            </div>
            <div className="admin-metric-card">
              <span className="admin-metric-card__num text-blue-600">{directCount}</span>
              <span className="admin-metric-card__label">Direct Reach (Phone/Email/WhatsApp)</span>
            </div>
            <div className="admin-metric-card">
              <span className="admin-metric-card__num text-purple-600">{socialCount}</span>
              <span className="admin-metric-card__label">Social Media Handles</span>
            </div>
          </div>

          {/* Filter Bar */}
          <div className="admin-filter-strip">
            <div className="admin-filter-group" role="group" aria-label="Filter channels">
              <button
                type="button"
                className={`admin-filter-btn ${filter === "all" ? "is-active" : ""}`}
                onClick={() => setFilter("all")}
              >
                All Channels ({channels.length})
              </button>
              <button
                type="button"
                className={`admin-filter-btn ${filter === "direct" ? "is-active" : ""}`}
                onClick={() => setFilter("direct")}
              >
                📞 Direct Contact ({directCount})
              </button>
              <button
                type="button"
                className={`admin-filter-btn ${filter === "social" ? "is-active" : ""}`}
                onClick={() => setFilter("social")}
              >
                🌐 Social Media ({socialCount})
              </button>
            </div>
          </div>

          {/* Table / Cards List */}
          {loading ? (
            <div className="admin-panel p-8 text-center text-slate-500">
              <p>Loading contact channels…</p>
            </div>
          ) : filteredChannels.length === 0 ? (
            <div className="admin-panel p-10 text-center">
              <div className="mx-auto w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center text-2xl mb-3">
                📭
              </div>
              <h3 className="font-bold text-slate-800 text-lg mb-1">No channels found</h3>
              <p className="text-slate-500 text-sm max-w-md mx-auto mb-4">
                {filter !== "all"
                  ? `No channels match the "${filter}" filter. Switch back to All Channels or add a new one.`
                  : "No contact channels or social media handles configured yet."}
              </p>
              <button
                type="button"
                className="admin-btn admin-btn--primary"
                onClick={handleOpenCreate}
              >
                + Add First Channel
              </button>
            </div>
          ) : (
            <div className="admin-panel overflow-hidden">
              <div className="overflow-x-auto">
                <table className="admin-table">
                  <thead>
                    <tr>
                      <th style={{ width: "60px" }}>Order</th>
                      <th>Channel & Icon</th>
                      <th>Label & Description</th>
                      <th>Value / Handle / Number</th>
                      <th>Website Link</th>
                      <th>Status</th>
                      <th style={{ textAlign: "right", minWidth: "150px" }}>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    {filteredChannels.map((channel) => {
                      const cfg = CHANNEL_CONFIG[channel.channel_type] || CHANNEL_CONFIG.other;
                      return (
                        <tr key={channel.id} className={!channel.is_active ? "opacity-60" : ""}>
                          {/* Order */}
                          <td className="text-slate-400 font-mono text-xs">
                            {channel.display_order ?? "—"}
                          </td>

                          {/* Icon & Type */}
                          <td>
                            <div className="flex items-center gap-2.5">
                              <span
                                className="w-9 h-9 rounded-lg flex items-center justify-center text-lg flex-shrink-0"
                                style={{ background: cfg.bgColor, color: cfg.color }}
                                title={cfg.label}
                              >
                                {cfg.emoji}
                              </span>
                              <div>
                                <span className="font-semibold text-slate-900 block text-xs">
                                  {cfg.label}
                                </span>
                                <span className="text-[10px] uppercase font-bold tracking-wider text-slate-400">
                                  {cfg.badge}
                                </span>
                              </div>
                            </div>
                          </td>

                          {/* Label & Description */}
                          <td>
                            <div className="flex items-center gap-2">
                              <span className="font-bold text-slate-800 text-sm">
                                {channel.label}
                              </span>
                              {channel.is_primary && (
                                <span className="px-1.5 py-0.5 text-[10px] font-bold rounded bg-amber-100 text-amber-800 border border-amber-200">
                                  Primary
                                </span>
                              )}
                            </div>
                            {channel.description && (
                              <span className="text-xs text-slate-500 block mt-0.5">
                                {channel.description}
                              </span>
                            )}
                          </td>

                          {/* Value / Phone / URL */}
                          <td>
                            <code className="text-xs bg-slate-100 text-slate-800 px-2 py-1 rounded font-mono">
                              {channel.value}
                            </code>
                          </td>

                          {/* Link Preview */}
                          <td>
                            {channel.href ? (
                              <a
                                href={channel.href}
                                target="_blank"
                                rel="noreferrer"
                                className="inline-flex items-center gap-1 text-xs text-sky-600 hover:text-sky-800 font-medium hover:underline max-w-[200px] truncate"
                                title={channel.href}
                              >
                                <span className="truncate">{channel.href}</span>
                                <span aria-hidden="true">↗</span>
                              </a>
                            ) : (
                              <span className="text-xs text-slate-400">Auto-built</span>
                            )}
                          </td>

                          {/* Status toggle */}
                          <td>
                            <button
                              type="button"
                              className={`admin-status-pill ${
                                channel.is_active ? "is-active" : "is-inactive"
                              }`}
                              onClick={() => handleToggleActive(channel)}
                              title="Click to toggle visibility"
                            >
                              <span className="admin-status-pill__dot" />
                              <span>{channel.is_active ? "Active" : "Hidden"}</span>
                            </button>
                          </td>

                          {/* Actions */}
                          <td style={{ textAlign: "right" }}>
                            <div className="flex items-center justify-end gap-1.5">
                              <button
                                type="button"
                                className="admin-btn admin-btn--secondary text-xs py-1 px-2.5"
                                onClick={() => handleOpenEdit(channel)}
                              >
                                Edit
                              </button>
                              <button
                                type="button"
                                className="admin-btn admin-btn--danger text-xs py-1 px-2.5"
                                onClick={() => setDeleteTarget(channel)}
                              >
                                Delete
                              </button>
                            </div>
                          </td>
                        </tr>
                      );
                    })}
                  </tbody>
                </table>
              </div>
            </div>
          )}
        </div>
      )}

      {/* TAB 2: General Site Settings */}
      {activeTab === "site_settings" && (
        <div className="admin-panel p-6">
          <div className="admin-panel__head mb-4">
            <h2 className="text-base font-bold text-slate-900">Application Key-Value Settings</h2>
            <p className="text-xs text-slate-500">
              Settings stored in the database for site metadata and defaults.
            </p>
          </div>

          {settingsLoading ? (
            <p className="text-slate-500 text-sm">Loading settings…</p>
          ) : siteSettings.length === 0 ? (
            <p className="text-slate-500 text-sm">No site settings found.</p>
          ) : (
            <div className="overflow-x-auto">
              <table className="admin-table">
                <thead>
                  <tr>
                    <th>Key</th>
                    <th>Value</th>
                  </tr>
                </thead>
                <tbody>
                  {siteSettings.map((st) => (
                    <tr key={st.id || st.key}>
                      <td className="font-mono text-xs font-bold text-slate-700">{st.key}</td>
                      <td className="text-xs text-slate-800">{String(st.value ?? "—")}</td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          )}
        </div>
      )}

      {/* ADD / EDIT MODAL DIALOG */}
      {modalOpen && (
        <div
          className="admin-modal-backdrop"
          role="presentation"
          onClick={() => !submitting && setModalOpen(false)}
        >
          <div
            className="admin-modal-dialog max-w-xl"
            role="dialog"
            aria-modal="true"
            aria-labelledby="modal-channel-title"
            onClick={(e) => e.stopPropagation()}
          >
            <div className="admin-modal-dialog__head">
              <div className="flex items-center gap-3">
                <span
                  className="w-10 h-10 rounded-xl flex items-center justify-center text-xl"
                  style={{ background: currentCfg.bgColor, color: currentCfg.color }}
                >
                  {currentCfg.emoji}
                </span>
                <div>
                  <h3 id="modal-channel-title" className="text-lg font-bold text-slate-900">
                    {editingId ? "Edit Channel / Social Handle" : "Add New Channel / Handle"}
                  </h3>
                  <p className="text-xs text-slate-500">
                    Will appear across Contact Page, Site Footer, and Navigation.
                  </p>
                </div>
              </div>
              <button
                type="button"
                className="admin-modal-dialog__close"
                onClick={() => setModalOpen(false)}
                disabled={submitting}
                aria-label="Close modal"
              >
                ✕
              </button>
            </div>

            <form onSubmit={handleSaveChannel} className="admin-modal-dialog__body">
              {/* Channel Type & Icon Selector */}
              <div className="admin-form-group">
                <label className="admin-form-label" htmlFor="channel-type-select">
                  <span>Channel Icon & Platform Type</span>
                  <span className="text-red-500">*</span>
                </label>
                <select
                  id="channel-type-select"
                  className="admin-input font-medium"
                  value={form.channel_type}
                  onChange={(e) => {
                    const nextType = e.target.value;
                    const nextCfg = CHANNEL_CONFIG[nextType] || CHANNEL_CONFIG.other;
                    setForm((f) => ({
                      ...f,
                      channel_type: nextType,
                      // Pre-fill label if empty or previously matching default
                      label: !f.label || Object.values(CHANNEL_CONFIG).some((c) => c.label === f.label)
                        ? nextCfg.label
                        : f.label,
                    }));
                  }}
                >
                  <optgroup label="📞 Direct Contact Methods">
                    <option value="phone">📞 Phone / Call Line</option>
                    <option value="whatsapp">💬 WhatsApp Chat / Number</option>
                    <option value="email">✉️ Contact Email</option>
                    <option value="location">📍 Physical Location / Address</option>
                    <option value="hours">🕒 Business Working Hours</option>
                  </optgroup>
                  <optgroup label="🌐 Social Media Channels">
                    <option value="linkedin">💼 LinkedIn Profile / Company Page</option>
                    <option value="twitter">𝕏 X (formerly Twitter)</option>
                    <option value="facebook">📘 Facebook Business Page</option>
                    <option value="instagram">📷 Instagram Profile / Handle</option>
                    <option value="youtube">▶️ YouTube Channel</option>
                    <option value="tiktok">🎵 TikTok Account</option>
                    <option value="telegram">✈️ Telegram Handle / Group</option>
                    <option value="github">🐙 GitHub Profile / Org</option>
                  </optgroup>
                  <optgroup label="🔗 Other / Custom">
                    <option value="website">🌐 Custom Website / Platform Link</option>
                    <option value="other">⚙️ Other Custom Link</option>
                  </optgroup>
                </select>
                <p className="admin-form-hint">{currentCfg.help}</p>
              </div>

              {/* Label */}
              <div className="admin-form-group">
                <label className="admin-form-label" htmlFor="channel-label-input">
                  <span>Display Label</span>
                  <span className="text-red-500">*</span>
                </label>
                <input
                  id="channel-label-input"
                  type="text"
                  className="admin-input"
                  placeholder="e.g. Sales Inquiries, WhatsApp Support, Follow on X"
                  value={form.label}
                  onChange={(e) => setForm({ ...form, label: e.target.value })}
                  required
                />
              </div>

              {/* Value / Phone / Handle */}
              <div className="admin-form-group">
                <label className="admin-form-label" htmlFor="channel-value-input">
                  <span>Value / Handle / Phone Number / URL</span>
                  <span className="text-red-500">*</span>
                </label>
                <input
                  id="channel-value-input"
                  type="text"
                  className="admin-input font-mono"
                  placeholder={currentCfg.placeholder}
                  value={form.value}
                  onChange={(e) => setForm({ ...form, value: e.target.value })}
                  required
                />
                <p className="admin-form-hint">
                  For phone/WhatsApp enter full digits with country code (e.g. +254...). For social media enter full URL or @handle.
                </p>
              </div>

              {/* Optional Custom Link Override */}
              <div className="admin-form-group">
                <label className="admin-form-label" htmlFor="channel-href-input">
                  <span>Custom Link URL (Optional Override)</span>
                </label>
                <input
                  id="channel-href-input"
                  type="text"
                  className="admin-input font-mono text-xs"
                  placeholder="Leave empty to auto-build link (e.g. tel:, mailto:, https://wa.me/...)"
                  value={form.href}
                  onChange={(e) => setForm({ ...form, href: e.target.value })}
                />
              </div>

              {/* Description */}
              <div className="admin-form-group">
                <label className="admin-form-label" htmlFor="channel-desc-input">
                  <span>Description / Helper Subtitle (Optional)</span>
                </label>
                <input
                  id="channel-desc-input"
                  type="text"
                  className="admin-input"
                  placeholder="e.g. Available Mon - Fri 8am - 6pm, Fast response"
                  value={form.description}
                  onChange={(e) => setForm({ ...form, description: e.target.value })}
                />
              </div>

              {/* Display Order & Toggles Row */}
              <div className="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-2">
                <div className="admin-form-group">
                  <label className="admin-form-label" htmlFor="channel-order-input">
                    Display Order
                  </label>
                  <input
                    id="channel-order-input"
                    type="number"
                    className="admin-input"
                    value={form.display_order}
                    onChange={(e) => setForm({ ...form, display_order: e.target.value })}
                  />
                </div>

                <div className="admin-form-group flex flex-col justify-end">
                  <label className="flex items-center gap-2 cursor-pointer pb-2.5">
                    <input
                      type="checkbox"
                      className="rounded border-slate-300 text-sky-600 focus:ring-sky-500 w-4 h-4"
                      checked={form.is_primary}
                      onChange={(e) => setForm({ ...form, is_primary: e.target.checked })}
                    />
                    <span className="text-xs font-semibold text-slate-800">Primary Channel</span>
                  </label>
                </div>

                <div className="admin-form-group flex flex-col justify-end">
                  <label className="flex items-center gap-2 cursor-pointer pb-2.5">
                    <input
                      type="checkbox"
                      className="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500 w-4 h-4"
                      checked={form.is_active}
                      onChange={(e) => setForm({ ...form, is_active: e.target.checked })}
                    />
                    <span className="text-xs font-semibold text-slate-800">Active / Published</span>
                  </label>
                </div>
              </div>

              {/* Modal Footer Actions */}
              <div className="admin-modal-dialog__footer">
                <button
                  type="button"
                  className="admin-btn admin-btn--secondary"
                  onClick={() => setModalOpen(false)}
                  disabled={submitting}
                >
                  Cancel
                </button>
                <button
                  type="submit"
                  className="admin-btn admin-btn--primary"
                  disabled={submitting}
                >
                  {submitting ? "Saving…" : editingId ? "Update Channel" : "Create Channel"}
                </button>
              </div>
            </form>
          </div>
        </div>
      )}
    </div>
  );
}
