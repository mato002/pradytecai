import React, { useState } from "react";
import ProductPoster from "./ProductPoster";

function cleanHostname(rawUrl, fallbackSlug) {
  if (!rawUrl) {
    return fallbackSlug ? `pradytec.com/products/${fallbackSlug}` : "app.pradytec.com";
  }
  try {
    const parsed = new URL(rawUrl.startsWith("http") ? rawUrl : `https://${rawUrl}`);
    return parsed.hostname + (parsed.pathname !== "/" ? parsed.pathname : "");
  } catch {
    return rawUrl.replace(/^https?:\/\//, "");
  }
}

export default function ProductDeviceShowcase({
  desktopSrc,
  mobileSrc,
  name = "Product",
  url = "",
  slug = "",
}) {
  const [activeModal, setActiveModal] = useState(null); // 'desktop' | 'mobile' | null
  const [activeView, setActiveView] = useState("both"); // 'both' | 'desktop' | 'mobile'

  const hasDesktop = Boolean(desktopSrc);
  const hasMobile = Boolean(mobileSrc);

  const displayUrl = cleanHostname(url, slug);
  const siteHref = url ? (url.startsWith("http") ? url : `https://${url}`) : null;

  // Case 1: Both Desktop and Mobile exist
  if (hasDesktop && hasMobile) {
    return (
      <div className="pdp-showcase pdp-showcase--dual" aria-label={`${name} interactive device views`}>
        {/* Ambient background glow for visual depth */}
        <div className="pdp-showcase__aura" aria-hidden="true" />

        {/* View Switcher Controls (Dual, Desktop, Mobile) */}
        <div className="pdp-showcase__header-bar">
          <div className="pdp-showcase__tabs" role="tablist" aria-label="Device view options">
            <button
              type="button"
              role="tab"
              aria-selected={activeView === "both"}
              className={`pdp-showcase__tab-btn ${activeView === "both" ? "is-active" : ""}`}
              onClick={() => setActiveView("both")}
            >
              <span>Dual View</span>
            </button>
            <button
              type="button"
              role="tab"
              aria-selected={activeView === "desktop"}
              className={`pdp-showcase__tab-btn ${activeView === "desktop" ? "is-active" : ""}`}
              onClick={() => setActiveView("desktop")}
            >
              <span>Desktop Web</span>
            </button>
            <button
              type="button"
              role="tab"
              aria-selected={activeView === "mobile"}
              className={`pdp-showcase__tab-btn ${activeView === "mobile" ? "is-active" : ""}`}
              onClick={() => setActiveView("mobile")}
            >
              <span>Mobile App</span>
            </button>
          </div>
        </div>

        {/* Multi-device stage */}
        <div className={`pdp-showcase__stage pdp-showcase__stage--view-${activeView}`}>
          {/* Desktop Browser Window Mockup */}
          {(activeView === "both" || activeView === "desktop") && (
            <div
              className={`pdp-device pdp-device--desktop ${activeView === "desktop" ? "pdp-device--desktop-solo" : ""}`}
              role="region"
              aria-label={`${name} desktop view`}
              tabIndex={0}
              onClick={() => setActiveModal("desktop")}
              onKeyDown={(e) => (e.key === "Enter" || e.key === " ") && setActiveModal("desktop")}
              title="Click to view desktop screenshot in full size"
            >
              {/* Desktop Chrome Bar */}
              <div className="pdp-device__desktop-header">
                <div className="pdp-device__dots" aria-hidden="true">
                  <span className="pdp-device__dot pdp-device__dot--red" />
                  <span className="pdp-device__dot pdp-device__dot--yellow" />
                  <span className="pdp-device__dot pdp-device__dot--green" />
                </div>

                <div className="pdp-device__address-bar">
                  <span className="pdp-device__lock-icon" aria-hidden="true">🔒</span>
                  {siteHref ? (
                    <a
                      href={siteHref}
                      target="_blank"
                      rel="noreferrer"
                      className="pdp-device__address-link"
                      onClick={(e) => e.stopPropagation()}
                      title={`Open ${siteHref} in new tab`}
                    >
                      <span>{displayUrl}</span>
                      <span className="pdp-device__external-arrow" aria-hidden="true"> ↗</span>
                    </a>
                  ) : (
                    <span className="pdp-device__address-text">{displayUrl}</span>
                  )}
                </div>

                <div className="pdp-device__tag pdp-device__tag--desktop">
                  Desktop Web
                </div>
              </div>

              {/* Desktop Screen Content */}
              <div className="pdp-device__desktop-screen">
                <img
                  src={desktopSrc}
                  alt={`${name} desktop dashboard screenshot`}
                  className="pdp-device__img"
                  loading="eager"
                />
                <div className="pdp-device__zoom-hint" aria-hidden="true">
                  <span>🔍 Click to expand</span>
                </div>
              </div>
            </div>
          )}

          {/* Floating Mobile Smartphone Mockup */}
          {(activeView === "both" || activeView === "mobile") && (
            <div
              className={`pdp-device pdp-device--mobile ${activeView === "mobile" ? "pdp-device--mobile-solo" : ""}`}
              role="region"
              aria-label={`${name} mobile view`}
              tabIndex={0}
              onClick={() => setActiveModal("mobile")}
              onKeyDown={(e) => (e.key === "Enter" || e.key === " ") && setActiveModal("mobile")}
              title="Click to view mobile screenshot in full size"
            >
              {/* Phone Bezel Top / Dynamic Island */}
              <div className="pdp-device__phone-top" aria-hidden="true">
                <span className="pdp-device__phone-time">9:41</span>
                <div className="pdp-device__phone-island">
                  <span className="pdp-device__phone-camera" />
                </div>
                <div className="pdp-device__phone-signals">
                  <span className="pdp-device__phone-bar" />
                  <span className="pdp-device__phone-battery" />
                </div>
              </div>

              {/* Phone Screen Content */}
              <div className="pdp-device__phone-screen">
                <img
                  src={mobileSrc}
                  alt={`${name} mobile app screenshot`}
                  className="pdp-device__img"
                  loading="eager"
                />
                <div className="pdp-device__zoom-hint" aria-hidden="true">
                  <span>🔍 Expand</span>
                </div>
              </div>

              {/* Phone Home Indicator Bar */}
              <div className="pdp-device__phone-bottom" aria-hidden="true">
                <span className="pdp-device__phone-home-indicator" />
              </div>

              <div className="pdp-device__tag pdp-device__tag--mobile">
                Mobile App
              </div>
            </div>
          )}
        </div>

        {/* View Indicator Strip */}
        <div className="pdp-showcase__indicators">
          <span className="pdp-showcase__pill">
            <span className="pdp-showcase__pill-dot" /> Responsive Web & Mobile
          </span>
          {siteHref && (
            <a
              href={siteHref}
              target="_blank"
              rel="noreferrer"
              className="pdp-showcase__site-btn"
            >
              <span>Visit Live Platform</span>
              <span aria-hidden="true"> ↗</span>
            </a>
          )}
        </div>

        {/* Zoom Lightbox Modal */}
        {activeModal && (
          <div
            className="pdp-modal-overlay"
            role="dialog"
            aria-modal="true"
            aria-label={`${name} ${activeModal} screenshot full size`}
            onClick={() => setActiveModal(null)}
          >
            <div className="pdp-modal-dialog" onClick={(e) => e.stopPropagation()}>
              <div className="pdp-modal-header">
                <span className="pdp-modal-title">
                  {name} — {activeModal === "desktop" ? "Desktop Web View" : "Mobile App View"}
                </span>
                <button
                  type="button"
                  className="pdp-modal-close"
                  onClick={() => setActiveModal(null)}
                  aria-label="Close full size view"
                >
                  ✕
                </button>
              </div>
              <div className="pdp-modal-body">
                <img
                  src={activeModal === "desktop" ? desktopSrc : mobileSrc}
                  alt={`${name} ${activeModal} full preview`}
                  className="pdp-modal-img"
                />
              </div>
            </div>
          </div>
        )}
      </div>
    );
  }

  // Case 2: Only Mobile exists
  if (!hasDesktop && hasMobile) {
    return (
      <div className="pdp-showcase pdp-showcase--mobile-only" aria-label={`${name} mobile view`}>
        <div className="pdp-showcase__aura" aria-hidden="true" />
        <div className="pdp-showcase__solo-wrap">
          <div
            className="pdp-device pdp-device--mobile pdp-device--mobile-solo"
            tabIndex={0}
            onClick={() => setActiveModal("mobile")}
            onKeyDown={(e) => (e.key === "Enter" || e.key === " ") && setActiveModal("mobile")}
            title="Click to view full size"
          >
            <div className="pdp-device__phone-top" aria-hidden="true">
              <span className="pdp-device__phone-time">9:41</span>
              <div className="pdp-device__phone-island">
                <span className="pdp-device__phone-camera" />
              </div>
              <div className="pdp-device__phone-signals">
                <span className="pdp-device__phone-bar" />
                <span className="pdp-device__phone-battery" />
              </div>
            </div>
            <div className="pdp-device__phone-screen">
              <img
                src={mobileSrc}
                alt={`${name} mobile app screenshot`}
                className="pdp-device__img"
                loading="eager"
              />
              <div className="pdp-device__zoom-hint" aria-hidden="true">
                <span>🔍 Click to expand</span>
              </div>
            </div>
            <div className="pdp-device__phone-bottom" aria-hidden="true">
              <span className="pdp-device__phone-home-indicator" />
            </div>
            <div className="pdp-device__tag pdp-device__tag--mobile">Mobile App</div>
          </div>
        </div>

        <div className="pdp-showcase__indicators">
          <span className="pdp-showcase__pill">
            <span className="pdp-showcase__pill-dot" /> Mobile Application
          </span>
          {siteHref && (
            <a
              href={siteHref}
              target="_blank"
              rel="noreferrer"
              className="pdp-showcase__site-btn"
            >
              <span>Visit App Page</span>
              <span aria-hidden="true"> ↗</span>
            </a>
          )}
        </div>

        {activeModal && (
          <div
            className="pdp-modal-overlay"
            role="dialog"
            aria-modal="true"
            onClick={() => setActiveModal(null)}
          >
            <div className="pdp-modal-dialog" onClick={(e) => e.stopPropagation()}>
              <div className="pdp-modal-header">
                <span className="pdp-modal-title">{name} — Mobile App View</span>
                <button
                  type="button"
                  className="pdp-modal-close"
                  onClick={() => setActiveModal(null)}
                  aria-label="Close"
                >
                  ✕
                </button>
              </div>
              <div className="pdp-modal-body">
                <img src={mobileSrc} alt={`${name} mobile full preview`} className="pdp-modal-img" />
              </div>
            </div>
          </div>
        )}
      </div>
    );
  }

  // Case 3: Only Desktop exists
  if (hasDesktop && !hasMobile) {
    return (
      <div className="pdp-showcase pdp-showcase--desktop-only" aria-label={`${name} desktop view`}>
        <div className="pdp-showcase__aura" aria-hidden="true" />
        <div className="pdp-showcase__solo-wrap">
          <div
            className="pdp-device pdp-device--desktop pdp-device--desktop-solo"
            tabIndex={0}
            onClick={() => setActiveModal("desktop")}
            onKeyDown={(e) => (e.key === "Enter" || e.key === " ") && setActiveModal("desktop")}
            title="Click to view full size"
          >
            <div className="pdp-device__desktop-header">
              <div className="pdp-device__dots" aria-hidden="true">
                <span className="pdp-device__dot pdp-device__dot--red" />
                <span className="pdp-device__dot pdp-device__dot--yellow" />
                <span className="pdp-device__dot pdp-device__dot--green" />
              </div>
              <div className="pdp-device__address-bar">
                <span className="pdp-device__lock-icon" aria-hidden="true">🔒</span>
                {siteHref ? (
                  <a
                    href={siteHref}
                    target="_blank"
                    rel="noreferrer"
                    className="pdp-device__address-link"
                    onClick={(e) => e.stopPropagation()}
                  >
                    <span>{displayUrl}</span>
                    <span className="pdp-device__external-arrow" aria-hidden="true"> ↗</span>
                  </a>
                ) : (
                  <span className="pdp-device__address-text">{displayUrl}</span>
                )}
              </div>
              <div className="pdp-device__tag pdp-device__tag--desktop">Web Platform</div>
            </div>
            <div className="pdp-device__desktop-screen">
              <img
                src={desktopSrc}
                alt={`${name} desktop preview`}
                className="pdp-device__img"
                loading="eager"
              />
              <div className="pdp-device__zoom-hint" aria-hidden="true">
                <span>🔍 Click to expand</span>
              </div>
            </div>
          </div>
        </div>

        <div className="pdp-showcase__indicators">
          <span className="pdp-showcase__pill">
            <span className="pdp-showcase__pill-dot" /> Web Application
          </span>
          {siteHref && (
            <a
              href={siteHref}
              target="_blank"
              rel="noreferrer"
              className="pdp-showcase__site-btn"
            >
              <span>Visit Platform</span>
              <span aria-hidden="true"> ↗</span>
            </a>
          )}
        </div>

        {activeModal && (
          <div
            className="pdp-modal-overlay"
            role="dialog"
            aria-modal="true"
            onClick={() => setActiveModal(null)}
          >
            <div className="pdp-modal-dialog" onClick={(e) => e.stopPropagation()}>
              <div className="pdp-modal-header">
                <span className="pdp-modal-title">{name} — Web Platform View</span>
                <button
                  type="button"
                  className="pdp-modal-close"
                  onClick={() => setActiveModal(null)}
                  aria-label="Close"
                >
                  ✕
                </button>
              </div>
              <div className="pdp-modal-body">
                <img src={desktopSrc} alt={`${name} desktop full preview`} className="pdp-modal-img" />
              </div>
            </div>
          </div>
        )}
      </div>
    );
  }

  // Case 4: Neither (fallback poster or placeholder)
  return (
    <div className="pdp-showcase pdp-showcase--fallback">
      <ProductPoster src={null} alt={`${name} poster`} variant="hero" lazy={false} />
    </div>
  );
}
