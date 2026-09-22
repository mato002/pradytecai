import React from "react";

/** Decorative hero visual matching Blade x-marketing.hero-visual feel */
export default function HeroVisual() {
  return (
    <div className="mkt-hero-visual" aria-hidden="true">
      <div className="mkt-hero-visual__glow" />
      <div className="mkt-hero-visual__panel">
        <div className="mkt-hero-visual__dots" />
        <div className="mkt-hero-visual__card mkt-hero-visual__card--a" />
        <div className="mkt-hero-visual__card mkt-hero-visual__card--b" />
        <div className="mkt-hero-visual__card mkt-hero-visual__card--c" />
      </div>
    </div>
  );
}
