import React from "react";

/** Compact product/dashboard visual for the public hero — no glass/neon. */
export default function HeroVisual() {
  return (
    <div className="mkt-hero-visual" aria-hidden="true">
      <div className="mkt-hero-visual__laptop">
        <div className="mkt-hero-visual__bezel">
          <div className="mkt-hero-visual__screen">
            <div className="mkt-hero-visual__chrome">
              <span />
              <span />
              <span />
            </div>
            <div className="mkt-hero-visual__grid">
              <div className="mkt-hero-visual__panel mkt-hero-visual__panel--wide">
                <div className="mkt-hero-visual__bar" />
                <div className="mkt-hero-visual__chart">
                  <span style={{ height: "42%" }} />
                  <span style={{ height: "68%" }} />
                  <span style={{ height: "54%" }} />
                  <span style={{ height: "82%" }} />
                  <span style={{ height: "60%" }} />
                </div>
              </div>
              <div className="mkt-hero-visual__stack">
                <div className="mkt-hero-visual__panel">
                  <div className="mkt-hero-visual__bar mkt-hero-visual__bar--sm" />
                  <strong>128</strong>
                </div>
                <div className="mkt-hero-visual__panel mkt-hero-visual__panel--center">
                  <span className="mkt-hero-visual__ring" />
                </div>
              </div>
            </div>
          </div>
        </div>
        <div className="mkt-hero-visual__base" />
      </div>
    </div>
  );
}
