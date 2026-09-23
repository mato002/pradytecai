import React, { useState } from "react";
import { Link } from "react-router-dom";
import PradyIcon, { hasPradyIcon } from "./PradyIcon";
import ProductPoster from "./ProductPoster";
import ProductMediaGallery from "./ProductMediaGallery";
import {
  SECTION_LABELS,
  heroSources,
  productCta,
  resolveSections,
  sectionHeading,
  showOverview,
  text,
} from "../../lib/productDetailModel";

function CtaLink({ cta, variant = "primary" }) {
  if (!cta) return null;
  const className =
    {
      primary: "mkt-btn mkt-btn--primary",
      secondary: "mkt-btn mkt-btn--outline",
      light: "mkt-btn mkt-btn--light",
      ghost: "mkt-btn mkt-btn--ghost",
    }[variant] || "mkt-btn mkt-btn--primary";
  if (cta.external) {
    return (
      <a className={className} href={cta.href} target="_blank" rel="noreferrer">
        {cta.label}
      </a>
    );
  }
  return (
    <Link className={className} to={cta.to}>
      {cta.label}
    </Link>
  );
}

function IconMark({ name }) {
  if (!hasPradyIcon(name)) return null;
  return (
    <span className="mkt-pdp-icon" aria-hidden="true">
      <PradyIcon name={name} className="mkt-pdp-icon__svg" />
    </span>
  );
}

function SectionIntro({ title, subtitle, label }) {
  return (
    <>
      {title ? <h2 className="mkt-pdp-section__title">{title}</h2> : null}
      {subtitle ? <p className="mkt-pdp-section__intro">{subtitle}</p> : null}
      {!title ? <h2 className="mkt-sr-only">{label}</h2> : null}
    </>
  );
}

function Highlights({ product, title, subtitle }) {
  const items = (product.highlights || []).filter((item) => text(item.title));
  return (
    <section className="mkt-pdp-section mkt-pdp-section--highlights" aria-label={title || "Highlights"}>
      <SectionIntro title={title} subtitle={subtitle} label="Highlights" />
      <ul className="mkt-pdp-highlights">
        {items.map((item) => (
          <li key={item.id || item.title} className="mkt-pdp-highlight">
            <IconMark name={item.icon} />
            <div>
              <p className="mkt-pdp-highlight__title">{item.title}</p>
              {text(item.short_description) ? (
                <p className="mkt-pdp-highlight__text">{item.short_description}</p>
              ) : null}
            </div>
          </li>
        ))}
      </ul>
    </section>
  );
}

function Audiences({ product, title, subtitle }) {
  const items = (product.audiences || []).filter((item) => text(item.title));
  return (
    <section className="mkt-pdp-section" aria-labelledby="pdp-audience-heading">
      <h2 id="pdp-audience-heading" className="mkt-pdp-section__title">
        {title || "Who it is for"}
      </h2>
      {subtitle ? <p className="mkt-pdp-section__intro">{subtitle}</p> : null}
      <ul className="mkt-pdp-chips">
        {items.map((item) => (
          <li key={item.id || item.title} className="mkt-pdp-chip">
            <IconMark name={item.icon} />
            <span>{item.title}</span>
          </li>
        ))}
      </ul>
    </section>
  );
}

function Problems({ product, title, subtitle }) {
  const items = (product.problems || []).filter((item) => text(item.title));
  return (
    <section className="mkt-pdp-section">
      <h2 className="mkt-pdp-section__title">{title || "Problems solved"}</h2>
      {subtitle ? <p className="mkt-pdp-section__intro">{subtitle}</p> : null}
      <ul className="mkt-pdp-split">
        {items.map((item) => (
          <li key={item.id || item.title} className="mkt-pdp-note">
            <p className="mkt-pdp-note__title">{item.title}</p>
            {text(item.description) ? <p className="mkt-pdp-note__text">{item.description}</p> : null}
          </li>
        ))}
      </ul>
    </section>
  );
}

function Capabilities({ product, title, subtitle }) {
  const groups = (product.capability_groups || []).filter((group) => text(group.title));
  return (
    <section className="mkt-pdp-section">
      <h2 className="mkt-pdp-section__title">{title || "Core capabilities"}</h2>
      {subtitle ? <p className="mkt-pdp-section__intro">{subtitle}</p> : null}
      <div className="mkt-pdp-groups">
        {groups.map((group) => {
          const caps = (group.capabilities || []).filter((cap) => text(cap.title));
          return (
            <article key={group.id || group.title} className="mkt-pdp-group">
              <h3 className="mkt-pdp-group__title">
                <IconMark name={group.icon} />
                {group.title}
              </h3>
              {text(group.description) ? <p className="mkt-pdp-note__text">{group.description}</p> : null}
              {caps.length ? (
                <ul className="mkt-pdp-caps">
                  {caps.map((cap) => (
                    <li key={cap.id || cap.title}>
                      <span className="mkt-pdp-caps__title">{cap.title}</span>
                      {text(cap.description) ? <span className="mkt-pdp-caps__text">{cap.description}</span> : null}
                    </li>
                  ))}
                </ul>
              ) : null}
            </article>
          );
        })}
      </div>
    </section>
  );
}

function Workflow({ product, title, subtitle }) {
  const steps = (product.workflow_steps || []).filter((step) => text(step.title));
  return (
    <section className="mkt-pdp-section">
      <h2 className="mkt-pdp-section__title">{title || "How it works"}</h2>
      {subtitle ? <p className="mkt-pdp-section__intro">{subtitle}</p> : null}
      <ol className="mkt-pdp-flow">
        {steps.map((step, index) => (
          <li key={step.id || step.title} className="mkt-pdp-flow__step">
            <span className="mkt-pdp-flow__num">{step.step_number || index + 1}</span>
            <div>
              <p className="mkt-pdp-note__title">{step.title}</p>
              {text(step.description) ? <p className="mkt-pdp-note__text">{step.description}</p> : null}
            </div>
          </li>
        ))}
      </ol>
    </section>
  );
}

function Integrations({ product, title, subtitle }) {
  const items = (product.integrations || []).filter((item) => text(item.name));
  return (
    <section className="mkt-pdp-section">
      <h2 className="mkt-pdp-section__title">{title || "Integrations"}</h2>
      {subtitle ? <p className="mkt-pdp-section__intro">{subtitle}</p> : null}
      <ul className="mkt-pdp-integrations">
        {items.map((item) => (
          <li key={item.id || item.name} className="mkt-pdp-integration">
            {text(item.logo_url) ? (
              <ProductPoster src={item.logo_url} alt="" variant="logo" lazy />
            ) : null}
            <div>
              <p className="mkt-pdp-note__title">{item.name}</p>
              {text(item.description) ? <p className="mkt-pdp-note__text">{item.description}</p> : null}
            </div>
          </li>
        ))}
      </ul>
    </section>
  );
}

function Controls({ product, title, subtitle }) {
  const items = (product.controls || []).filter((item) => text(item.title));
  return (
    <section className="mkt-pdp-section">
      <h2 className="mkt-pdp-section__title">{title || "Security and controls"}</h2>
      {subtitle ? <p className="mkt-pdp-section__intro">{subtitle}</p> : null}
      <ul className="mkt-pdp-split">
        {items.map((item) => (
          <li key={item.id || item.title} className="mkt-pdp-note">
            <p className="mkt-pdp-note__title">
              <IconMark name={item.icon} />
              {item.title}
            </p>
            {text(item.description) ? <p className="mkt-pdp-note__text">{item.description}</p> : null}
          </li>
        ))}
      </ul>
    </section>
  );
}

function Outcomes({ product, title, subtitle }) {
  const items = (product.outcomes || []).filter((item) => text(item.title));
  return (
    <section className="mkt-pdp-section">
      <h2 className="mkt-pdp-section__title">{title || "Outcomes"}</h2>
      {subtitle ? <p className="mkt-pdp-section__intro">{subtitle}</p> : null}
      <ul className="mkt-pdp-split">
        {items.map((item) => (
          <li key={item.id || item.title} className="mkt-pdp-note">
            <p className="mkt-pdp-note__title">{item.title}</p>
            {text(item.description) ? <p className="mkt-pdp-note__text">{item.description}</p> : null}
          </li>
        ))}
      </ul>
    </section>
  );
}

function Implementation({ product, title, subtitle }) {
  const steps = (product.implementation_steps || []).filter((step) => text(step.title));
  return (
    <section className="mkt-pdp-section">
      <h2 className="mkt-pdp-section__title">{title || "Getting started"}</h2>
      {subtitle ? <p className="mkt-pdp-section__intro">{subtitle}</p> : null}
      <ol className="mkt-pdp-steps">
        {steps.map((step, index) => (
          <li key={step.id || step.title}>
            <span className="mkt-pdp-flow__num">{step.step_number || index + 1}</span>
            <div>
              <p className="mkt-pdp-note__title">{step.title}</p>
              {text(step.description) ? <p className="mkt-pdp-note__text">{step.description}</p> : null}
            </div>
          </li>
        ))}
      </ol>
    </section>
  );
}

function Faqs({ product, title, subtitle }) {
  const items = (product.faqs || []).filter((item) => text(item.question) && text(item.answer));
  const [openId, setOpenId] = useState(null);
  return (
    <section className="mkt-pdp-section">
      <h2 className="mkt-pdp-section__title">{title || "FAQ"}</h2>
      {subtitle ? <p className="mkt-pdp-section__intro">{subtitle}</p> : null}
      <div className="mkt-pdp-faq">
        {items.map((item) => {
          const panelId = `pdp-faq-${item.id || item.question}`;
          const open = openId === (item.id ?? item.question);
          return (
            <div key={item.id || item.question} className="mkt-pdp-faq__item">
              <h3 className="mkt-pdp-faq__heading">
                <button
                  type="button"
                  className="mkt-pdp-faq__button"
                  aria-expanded={open}
                  aria-controls={panelId}
                  onClick={() => setOpenId(open ? null : item.id ?? item.question)}
                >
                  {item.question}
                  <span aria-hidden="true">{open ? "–" : "+"}</span>
                </button>
              </h3>
              <div id={panelId} role="region" hidden={!open} className="mkt-pdp-faq__panel">
                {item.answer}
              </div>
            </div>
          );
        })}
      </div>
    </section>
  );
}

function CustomBlocks({ product, title, subtitle }) {
  const items = (product.custom_sections || []).filter((item) => text(item.title) || text(item.body));
  return (
    <section className="mkt-pdp-section">
      {title ? <h2 className="mkt-pdp-section__title">{title}</h2> : null}
      {subtitle ? <p className="mkt-pdp-section__intro">{subtitle}</p> : null}
      {items.map((item) => {
        const layout = item.layout_type || "text";
        return (
          <article key={item.id || item.title} className={`mkt-pdp-custom mkt-pdp-custom--${layout}`}>
            <div>
              {text(item.title) ? <h3 className="mkt-pdp-custom__title">{item.title}</h3> : null}
              {text(item.subtitle) ? <p className="mkt-pdp-section__intro">{item.subtitle}</p> : null}
              {text(item.body) ? <p className="mkt-pdp-note__text">{item.body}</p> : null}
            </div>
            {text(item.image_url) ? (
              <ProductPoster
                src={item.image_url}
                alt={text(item.title) || `${product.name} illustration`}
                variant="gallery"
                lazy
              />
            ) : null}
          </article>
        );
      })}
    </section>
  );
}

const RENDERERS = {
  highlights: Highlights,
  audience: Audiences,
  problems: Problems,
  capabilities: Capabilities,
  workflow: Workflow,
  media: ProductMediaGallery,
  integrations: Integrations,
  controls: Controls,
  outcomes: Outcomes,
  implementation: Implementation,
  faq: Faqs,
  custom: CustomBlocks,
};

export default function ProductDetailView({ product }) {
  const sections = resolveSections(product);
  const sources = heroSources(product);
  const primary = productCta(product, "primary");
  const secondary = productCta(product, "secondary");
  const overview = showOverview(product);
  const name = text(product.name) || "Product";
  const hasMobile = Boolean(sources.mobile);
  const heroAlt = `${name} poster`;

  return (
    <article className="mkt-pdp">
      <header className={`mkt-pdp-hero${hasMobile ? " mkt-pdp-hero--has-mobile" : ""}`}>
        <div className="mkt-container mkt-pdp-hero__grid">
          <div className="mkt-pdp-hero__copy">
            <p className="mkt-pdp-hero__back">
              <Link to="/products">All products</Link>
            </p>
            <h1 className="mkt-pdp-hero__title">{name}</h1>
            {text(product.tagline) ? <p className="mkt-pdp-hero__tagline">{product.tagline}</p> : null}
            {text(product.short_description || product.short) ? (
              <p className="mkt-pdp-hero__lead">{product.short_description || product.short}</p>
            ) : null}
            <div className="mkt-pdp-hero__actions">
              <CtaLink cta={primary} variant="primary" />
              <CtaLink cta={secondary} variant="secondary" />
            </div>
          </div>
          <div className="mkt-pdp-hero__visual">
            <div className="mkt-pdp-hero__desktop">
              <ProductPoster src={sources.desktop || null} alt={heroAlt} variant="hero" lazy={false} />
            </div>
            {hasMobile ? (
              <div className="mkt-pdp-hero__mobile">
                <ProductPoster src={sources.mobile} alt={`${name} on mobile`} variant="hero" lazy={false} />
              </div>
            ) : null}
          </div>
        </div>
      </header>

      <div className="mkt-pdp-body">
        <div className="mkt-container">
          {overview ? <p className="mkt-pdp-overview">{overview}</p> : null}
          {sections.map((section) => {
            const Renderer = RENDERERS[section.type];
            if (!Renderer) return null;
            const title = sectionHeading(section);
            return (
              <Renderer
                key={section.key}
                product={product}
                title={title}
                subtitle={section.subtitle}
                label={SECTION_LABELS[section.type]}
              />
            );
          })}
        </div>
      </div>

      <section className="mkt-cta" aria-label="Request a demo">
        <div className="mkt-container mkt-cta__inner">
          <h2 className="mkt-cta__title">See how {name} can fit your operation.</h2>
          <div className="mkt-cta__actions">
            <CtaLink cta={primary} variant="light" />
            <CtaLink cta={secondary} variant="ghost" />
          </div>
        </div>
      </section>
    </article>
  );
}
