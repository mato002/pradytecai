import React, { useEffect } from "react";
import { Link, useLocation } from "react-router-dom";
import { portfolio, productBySlug } from "../../data/portfolio";
import PradyIcon from "../../components/marketing/PradyIcon";
import HeroVisual from "../../components/marketing/HeroVisual";

export default function HomePage() {
  const location = useLocation();
  const featured = productBySlug(portfolio.featured.slug);
  const productsBySlug = Object.fromEntries(portfolio.products.map((p) => [p.slug, p]));

  useEffect(() => {
    if (location.hash) {
      const el = document.querySelector(location.hash);
      if (el) el.scrollIntoView({ behavior: "smooth", block: "start" });
    }
  }, [location]);

  useEffect(() => {
    document.title = "Prady Technologies | Smart Technology Solutions for African Businesses";
  }, []);

  return (
    <>
      <section className="mkt-hero" aria-labelledby="home-hero-heading">
        <div className="mkt-container mkt-hero__inner">
          <div className="mkt-hero__copy">
            <h1 id="home-hero-heading" className="mkt-hero__title">
              <span className="mkt-hero__title-line">Smart Technology Solutions</span>
              <span className="mkt-hero__title-line">for Ambitious Businesses</span>
            </h1>
            <p className="mkt-hero__lead">We build secure, efficient software that drives growth.</p>
            <div className="mkt-hero__actions">
              <Link to="/contact" className="mkt-btn mkt-btn--light mkt-btn--hero">
                Get Demo →
              </Link>
              <Link to="/products" className="mkt-btn mkt-btn--ghost mkt-btn--hero">
                Our Products
              </Link>
            </div>
          </div>
          <div className="mkt-hero__media" aria-hidden="true">
            <HeroVisual />
          </div>
        </div>
      </section>

      <section id="solutions" className="mkt-section mkt-section--solutions">
        <div className="mkt-container">
          <div className="mkt-section__header mkt-section__header--solutions">
            <h2 className="mkt-section__title">Our Solutions</h2>
            <p className="mkt-section__subtitle">
              Tailored systems designed to streamline operations and scale your business
            </p>
          </div>
          <div className="mkt-solutions-grid">
            {portfolio.solutions.map((s) => (
              <Link key={s.name} to={s.href} className="mkt-solution-card">
                <span className="mkt-solution-card__icon">
                  <PradyIcon name={s.icon} className="w-7 h-7" />
                </span>
                <h3 className="mkt-solution-card__title">{s.name}</h3>
                <p className="mkt-solution-card__text">{s.short}</p>
              </Link>
            ))}
          </div>
        </div>
      </section>

      <section className="mkt-trust" aria-label="Why Prady">
        <div className="mkt-container">
          <div className="mkt-trust__grid mkt-trust__grid--three">
            {portfolio.trust.map((t) => (
              <div key={t.label} className="mkt-trust__item mkt-trust__item--center">
                <span className="mkt-trust__icon">
                  <PradyIcon name={t.icon} className="w-5 h-5" />
                </span>
                <p className="mkt-trust__label">{t.label}</p>
              </div>
            ))}
          </div>
        </div>
      </section>

      <section id="manage" className="mkt-section mkt-section--light">
        <div className="mkt-container">
          <div className="mkt-section__header">
            <h2 className="mkt-section__title">What do you want to manage?</h2>
            <p className="mkt-section__subtitle">Guide visitors directly to the right Prady platform.</p>
          </div>
          <div className="mkt-manage-grid">
            {portfolio.manage.map((item) => (
              <Link key={item.label} to={item.href} className="mkt-manage-card">
                <span className="mkt-manage-card__icon">
                  <PradyIcon name={item.icon} className="w-6 h-6" />
                </span>
                <span className="mkt-manage-card__label">{item.label}</span>
              </Link>
            ))}
          </div>
        </div>
      </section>

      <section id="products" className="mkt-section mkt-section--white">
        <div className="mkt-container">
          <div className="mkt-section__header">
            <h2 className="mkt-section__title">Product Portfolio</h2>
            <p className="mkt-section__subtitle">
              Practical platforms built for African businesses across finance, mobility, property and
              commerce.
            </p>
          </div>
          {portfolio.product_groups.map((group) => (
            <div key={group.key} className="mkt-product-group">
              <h3 className="mkt-product-group__title">{group.title}</h3>
              <div className="mkt-products-grid">
                {group.slugs.map((slug) => {
                  const p = productsBySlug[slug];
                  if (!p) return null;
                  return (
                    <Link key={slug} to={`/products#${slug}`} className="mkt-product-card" id={slug}>
                      <span className="mkt-product-card__icon">
                        <PradyIcon name={p.icon} className="w-7 h-7" />
                      </span>
                      <h3 className="mkt-product-card__title">{p.name}</h3>
                      <p className="mkt-product-card__text">{p.short}</p>
                      {p.market && <p className="mkt-product-card__market">{p.market}</p>}
                      <span className="mkt-product-card__link">
                        Learn more <span aria-hidden="true">→</span>
                      </span>
                    </Link>
                  );
                })}
              </div>
            </div>
          ))}
        </div>
      </section>

      <section id="capabilities" className="mkt-section mkt-section--light">
        <div className="mkt-container">
          <div className="mkt-section__header">
            <h2 className="mkt-section__title">Solutions &amp; Capabilities</h2>
            <p className="mkt-section__subtitle">
              Services that support every Prady platform — separate from our named products.
            </p>
          </div>
          <div className="mkt-products-grid">
            {portfolio.capabilities.map((item) => (
              <Link key={item.name} to={item.href} className="mkt-product-card">
                <span className="mkt-product-card__icon">
                  <PradyIcon name={item.icon} className="w-7 h-7" />
                </span>
                <h3 className="mkt-product-card__title">{item.name}</h3>
                <p className="mkt-product-card__text">{item.short}</p>
                <span className="mkt-product-card__link">
                  Learn more <span aria-hidden="true">→</span>
                </span>
              </Link>
            ))}
          </div>
        </div>
      </section>

      <section id="industries" className="mkt-section mkt-section--white">
        <div className="mkt-container">
          <div className="mkt-section__header">
            <h2 className="mkt-section__title">Industries We Serve</h2>
            <p className="mkt-section__subtitle">
              Purpose-built platforms for the markets where Prady Technologies works deepest.
            </p>
          </div>
          <div className="mkt-products-grid">
            {portfolio.industries.map((item) => (
              <Link key={item.name} to={item.href} className="mkt-product-card">
                <span className="mkt-product-card__icon">
                  <PradyIcon name={item.icon} className="w-7 h-7" />
                </span>
                <h3 className="mkt-product-card__title">{item.name}</h3>
                <p className="mkt-product-card__text">{item.short}</p>
                <span className="mkt-product-card__link">
                  See products <span aria-hidden="true">→</span>
                </span>
              </Link>
            ))}
          </div>
        </div>
      </section>

      {featured && (
        <section id="featured" className="mkt-section mkt-section--light">
          <div className="mkt-container">
            <div className="mkt-featured">
              <div className="mkt-featured__visual" aria-hidden="true">
                <HeroVisual />
              </div>
              <div className="mkt-featured__copy">
                <p className="mkt-featured__eyebrow">Featured product</p>
                <h2 className="mkt-section__title mkt-section__title--left">{featured.name}</h2>
                <p className="mkt-about__text">{featured.description}</p>
                <ul className="mkt-featured__outcomes">
                  {portfolio.featured.outcomes.map((o) => (
                    <li key={o}>
                      <span aria-hidden="true">
                        <PradyIcon name="check" className="w-5 h-5" />
                      </span>
                      <span>{o}</span>
                    </li>
                  ))}
                </ul>
                <div className="mkt-featured__actions">
                  <Link to={`/products#${featured.slug}`} className="mkt-btn mkt-btn--primary">
                    Explore Product
                  </Link>
                  <Link
                    to={`/contact?product=${encodeURIComponent(featured.name)}`}
                    className="mkt-btn mkt-btn--outline"
                  >
                    Request Demo
                  </Link>
                </div>
              </div>
            </div>
          </div>
        </section>
      )}

      <section className="mkt-cta">
        <div className="mkt-container mkt-cta__inner">
          <h2 className="mkt-cta__title">Not sure which Prady platform fits your business?</h2>
          <p className="mkt-cta__text">
            Tell us what you&apos;re trying to manage and we&apos;ll guide you to the right solution.
          </p>
          <div className="mkt-cta__actions">
            <Link to="/contact" className="mkt-btn mkt-btn--light">
              Talk to Us
            </Link>
            <Link to="/contact" className="mkt-btn mkt-btn--ghost">
              Get Demo
            </Link>
          </div>
        </div>
      </section>
    </>
  );
}
