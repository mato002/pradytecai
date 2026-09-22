import React, { useMemo, useState } from "react";
import {
  CAMPAIGNS,
  PRODUCTS,
  getMockComposerDefaults,
  getMockSocialAccounts,
} from "../../../data/socialMockData";
import {
  ComposerAccountSelector,
  PlatformTabs,
  PostPreview,
  ScheduleModal,
} from "../../../components/social/ComposerParts";
import { SocialPageHeader, SocialToast } from "../../../components/social/SocialShared";

const PREVIEW_PLATFORMS = ["instagram", "facebook", "linkedin", "x"];
const OVERRIDE_TABS = ["original", "instagram", "facebook", "linkedin", "x", "tiktok"];

export default function SocialComposer() {
  const accounts = useMemo(
    () => getMockSocialAccounts().filter((a) => a.status === "Connected" || a.status === "Attention required"),
    []
  );
  const defaults = getMockComposerDefaults();

  const [selectedIds, setSelectedIds] = useState(["acc-ig", "acc-fb", "acc-li"]);
  const [caption, setCaption] = useState(defaults.caption);
  const [hashtags, setHashtags] = useState(defaults.hashtags);
  const [media, setMedia] = useState([{ id: "m1", name: "mfi-hero.jpg", type: "image" }]);
  const [url, setUrl] = useState(defaults.destinationUrl);
  const [cta, setCta] = useState(defaults.cta);
  const [campaignId, setCampaignId] = useState(defaults.campaignId);
  const [productId, setProductId] = useState(defaults.productId);
  const [note, setNote] = useState("");
  const [overrideTab, setOverrideTab] = useState("original");
  const [overrides, setOverrides] = useState(defaults.overrides);
  const [previewPlatform, setPreviewPlatform] = useState("instagram");
  const [scheduleOpen, setScheduleOpen] = useState(false);
  const [toast, setToast] = useState("");

  function toastMsg(msg) {
    setToast(msg);
    setTimeout(() => setToast(""), 2600);
  }

  function toggleAccount(id) {
    setSelectedIds((prev) => (prev.includes(id) ? prev.filter((x) => x !== id) : [...prev, id]));
  }

  function removeMedia(id) {
    setMedia((prev) => prev.filter((m) => m.id !== id));
  }

  function addMedia() {
    setMedia((prev) => [...prev, { id: `m${Date.now()}`, name: `upload-${prev.length + 1}.jpg`, type: "image" }]);
    toastMsg("Media added (mock)");
  }

  const activeCaption =
    overrideTab === "original" ? `${caption}\n\n${hashtags}` : overrides[overrideTab] || caption;

  const previewCaption =
    previewPlatform && overrides[previewPlatform]
      ? overrides[previewPlatform]
      : `${caption}\n\n${hashtags}`;

  return (
    <div className="social-workspace">
      <SocialPageHeader
        title="Create Post"
        description="Compose once, customize per platform, then schedule or publish."
        showCreate={false}
      />

      <div className="social-composer">
        <div className="social-composer__editor admin-panel">
          <div className="admin-panel__head">
            <h2>Composer</h2>
          </div>
          <div className="social-composer__body">
            <ComposerAccountSelector accounts={accounts} selectedIds={selectedIds} onToggle={toggleAccount} />

            <label className="social-field">
              <span className="social-field__label">
                Caption <em>{caption.length} characters</em>
              </span>
              <textarea
                rows={5}
                value={caption}
                onChange={(e) => setCaption(e.target.value)}
                placeholder="Write your post…"
              />
            </label>

            <div className="social-field-row">
              <label className="social-field">
                <span className="social-field__label">Hashtags</span>
                <input value={hashtags} onChange={(e) => setHashtags(e.target.value)} />
              </label>
              <button type="button" className="btn-ghost" onClick={() => toastMsg("Emoji picker coming soon")}>
                ☺ Emoji
              </button>
            </div>

            <div className="social-media-uploader">
              <div className="social-media-uploader__head">
                <span>Media</span>
                <button type="button" className="btn-ghost" onClick={addMedia}>
                  + Add media
                </button>
              </div>
              <div className="social-media-uploader__thumbs">
                {media.map((m) => (
                  <div key={m.id} className="social-media-thumb">
                    <span>{m.type === "video" ? "▶" : "IMG"}</span>
                    <p>{m.name}</p>
                    <button type="button" aria-label={`Remove ${m.name}`} onClick={() => removeMedia(m.id)}>
                      ✕
                    </button>
                  </div>
                ))}
                {!media.length && <p className="social-muted">No media attached</p>}
              </div>
            </div>

            <div className="social-field-grid">
              <label className="social-field">
                <span className="social-field__label">Destination URL</span>
                <input value={url} onChange={(e) => setUrl(e.target.value)} />
              </label>
              <label className="social-field">
                <span className="social-field__label">CTA</span>
                <input value={cta} onChange={(e) => setCta(e.target.value)} />
              </label>
              <label className="social-field">
                <span className="social-field__label">Campaign</span>
                <select value={campaignId} onChange={(e) => setCampaignId(e.target.value)}>
                  {CAMPAIGNS.map((c) => (
                    <option key={c.id} value={c.id}>
                      {c.name}
                    </option>
                  ))}
                </select>
              </label>
              <label className="social-field">
                <span className="social-field__label">Product</span>
                <select value={productId} onChange={(e) => setProductId(e.target.value)}>
                  {PRODUCTS.map((p) => (
                    <option key={p.id} value={p.id}>
                      {p.name}
                    </option>
                  ))}
                </select>
              </label>
            </div>

            <label className="social-field">
              <span className="social-field__label">Internal note (optional)</span>
              <textarea rows={2} value={note} onChange={(e) => setNote(e.target.value)} placeholder="Note for reviewers…" />
            </label>

            <div className="social-overrides">
              <h3>Customize for each platform</h3>
              <PlatformTabs
                platforms={OVERRIDE_TABS.map((id) =>
                  id === "original" ? { id: "original", label: "Original" } : id
                )}
                active={overrideTab}
                onChange={setOverrideTab}
                label="Platform overrides"
              />
              {overrideTab === "original" ? (
                <p className="social-muted social-overrides__hint">
                  Original caption is used unless a platform override is set ({activeCaption.slice(0, 80)}…).
                </p>
              ) : (
                <label className="social-field">
                  <span className="social-field__label">
                    {overrideTab.charAt(0).toUpperCase() + overrideTab.slice(1)} override
                  </span>
                  <textarea
                    rows={4}
                    value={overrides[overrideTab] || ""}
                    onChange={(e) => setOverrides((prev) => ({ ...prev, [overrideTab]: e.target.value }))}
                  />
                </label>
              )}
            </div>
          </div>

          <div className="social-composer__actions">
            <button type="button" className="btn-ghost" onClick={() => toastMsg("Draft saved (mock)")}>
              Save draft
            </button>
            <button type="button" className="btn-ghost" onClick={() => toastMsg("Sent for approval (mock)")}>
              Send for approval
            </button>
            <button type="button" className="btn-ghost" onClick={() => toastMsg("Added to queue (mock)")}>
              Add to queue
            </button>
            <button type="button" className="btn-ghost" onClick={() => setScheduleOpen(true)}>
              Schedule
            </button>
            <button type="button" className="btn-primary" onClick={() => toastMsg("Published now (mock — no provider call)")}>
              Publish now
            </button>
          </div>
        </div>

        <aside className="social-composer__preview admin-panel">
          <div className="admin-panel__head">
            <h2>Platform preview</h2>
          </div>
          <div className="social-composer__preview-body">
            <PlatformTabs platforms={PREVIEW_PLATFORMS} active={previewPlatform} onChange={setPreviewPlatform} />
            <PostPreview platform={previewPlatform} caption={previewCaption} />
          </div>
        </aside>
      </div>

      <ScheduleModal
        open={scheduleOpen}
        onClose={() => setScheduleOpen(false)}
        onSchedule={(payload) => {
          setScheduleOpen(false);
          toastMsg(`Scheduled for ${payload.date} ${payload.time} (${payload.tz}) — mock`);
        }}
        onQueue={() => {
          setScheduleOpen(false);
          toastMsg("Added to next queue slot (mock)");
        }}
      />

      <SocialToast message={toast} onDismiss={() => setToast("")} />
    </div>
  );
}
