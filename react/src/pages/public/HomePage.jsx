import React, { useEffect, useMemo, useState } from "react";
import { Link, useLocation } from "react-router-dom";
import { api } from "../../api/client";
import { portfolio } from "../../data/portfolio";
import PradyIcon from "../../components/marketing/PradyIcon";
import HeroVisual from "../../components/marketing/HeroVisual";

export default function HomePage() {
  const location = useLocation();
  const [products, setProducts] = useState([]);
  const [featured, setFeatured] = useState(null);

  useEffect(() => {
    api("/public/home/")
      .then((data) => {
        const list = Array.isArray(data.products) ? data.products : [];
        setProducts(list);
        setFeatured(data.featured || list.find((p) => p.is_featured) || null);
      })
      .catch(() => {
        setProducts([]);
        setFeatured(null);
      });
  }, []);

  const productsBySlug = useMemo(
    () => Object.fromEntries(products.map((p) => [p.slug, p])),
    [products]
  );

  useEffect(() => {
    if (location.hash) {
      const el = document.querySelector(location.hash);
      if (el) el.scrollIntoView({ behavior: "smooth", block: "start" });
    }
  }, [location, products]);

  useEffect(() => {
    document.title = "Prady Technologies | Smart Technology Solutions for African Businesses";
  }, []);

  return (
    <>
      <section className="mkt-hero" aria-labelledby="home-hero-heading" style={{ backgroundColor: '#0A4E99' }}>
        <div className="mkt-container mkt-hero__inner relative z-10 pt-16">
          <div className="mkt-hero__copy relative">
            <h1 id="home-hero-heading" className="mkt-hero__title" style={{ color: '#ffffff', fontWeight: '800', fontSize: '3rem', lineHeight: '1.2' }}>
              <span className="mkt-hero__title-line block">Smart Technology Solutions</span>
              <span className="mkt-hero__title-line block">for Ambitious Businesses</span>
            </h1>
            <p className="mkt-hero__lead mt-4 text-xl font-medium" style={{ color: '#ffffff' }}>We build secure, efficient software that drives growth.</p>
            <div className="mkt-hero__actions mt-8 flex gap-4">
              <Link to="/contact" className="mkt-btn bg-white font-bold transition-colors mkt-btn--hero shadow-md" style={{ color: '#0A4E99', borderRadius: '8px' }}>
                Get Demo →
              </Link>
              <Link to="/products" className="mkt-btn border-2 border-white text-white font-bold transition-colors mkt-btn--hero hover:bg-white/10" style={{ borderRadius: '8px' }}>
                Our Products
              </Link>
            </div>
          </div>
          <div className="mkt-hero__media" aria-hidden="true">
            <HeroVisual />
          </div>
        </div>
      </section>

      <section id="solutions" className="mkt-section mkt-section--solutions py-16 bg-white relative z-10" style={{ backgroundColor: '#ffffff' }}>
        <div className="mkt-container">
          <div className="mkt-section__header mkt-section__header--solutions mb-12 text-center max-w-4xl mx-auto">
            <h2 className="text-4xl md:text-5xl font-bold mb-4" style={{ color: '#0A4E99' }}>Our Solutions</h2>
            <p className="text-lg md:text-xl font-medium" style={{ color: '#535B67' }}>
              Tailored systems designed to streamline operations and scale your business
            </p>
          </div>
          <div className="mkt-solutions-grid grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            {portfolio.solutions.map((s) => (
              <Link key={s.name} to={s.href} className="mkt-solution-card group bg-white rounded-xl p-8 shadow-[0_4px_16px_rgba(0,0,0,0.08)] hover:shadow-[0_12px_24px_rgba(0,0,0,0.12)] transition-shadow duration-300 flex flex-col items-center text-center" style={{ border: 'none', backgroundColor: '#ffffff' }}>
                <span className="mkt-solution-card__icon inline-flex items-center justify-center w-20 h-20 rounded-full mb-6" style={{ backgroundColor: '#EDF6FC', color: '#0A4E99' }}>
                  <PradyIcon name={s.icon} className="w-10 h-10" />
                </span>
                <h3 className="mkt-solution-card__title text-xl font-bold mb-3" style={{ color: '#053171' }}>{s.name}</h3>
                <p className="mkt-solution-card__text text-sm leading-relaxed" style={{ color: '#535B67' }}>{s.short}</p>
              </Link>
            ))}
          </div>
        </div>
      </section>

      <section className="mkt-trust py-6" aria-label="Why Prady" style={{ backgroundColor: '#F0F2F5', borderTop: '1px solid #E5EAF0' }}>
        <div className="mkt-container">
          <div className="mkt-trust__grid flex flex-col md:flex-row justify-center gap-12 md:gap-24">
            {portfolio.trust.map((t) => (
              <div key={t.label} className="mkt-trust__item flex items-center gap-3 font-bold" style={{ color: '#053171' }}>
                <span className="mkt-trust__icon" style={{ color: '#0A4E99' }}>
                  <PradyIcon name={t.icon} className="w-6 h-6" />
                </span>
                <p className="mkt-trust__label text-sm tracking-wide">{t.label}</p>
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
          {portfolio.product_groups.map((group) => {
            const items = group.slugs.map((slug) => productsBySlug[slug]).filter(Boolean);
            if (!items.length) return null;
            return (
              <div key={group.key} className="mkt-product-group">
                <h3 className="mkt-product-group__title">{group.title}</h3>
                <div className="mkt-products-grid">
                  {items.map((p) => (
                    <Link
                      key={p.slug}
                      to={`/products/${p.slug}`}
                      className="mkt-product-card"
                      id={p.slug}
                    >
                      {p.poster_url ? (
                        <img
                          src={p.poster_url}
                          alt=""
                          className="mkt-product-card__poster"
                          loading="lazy"
                        />
                      ) : (
                        <span className="mkt-product-card__icon">
                          <PradyIcon name={p.icon || "cog"} className="w-7 h-7" />
                        </span>
                      )}
                      <h3 className="mkt-product-card__title">{p.name}</h3>
                      <p className="mkt-product-card__text">{p.short_description || p.short}</p>
                      {p.market && <p className="mkt-product-card__market">{p.market}</p>}
                      <span className="mkt-product-card__link">
                        Learn more <span aria-hidden="true">→</span>
                      </span>
                    </Link>
                  ))}
                </div>
              </div>
            );
          })}
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
                {featured.poster_url ? (
                  <img src={featured.poster_url} alt="" className="mkt-featured__poster" />
                ) : (
                  <HeroVisual />
                )}
              </div>
              <div className="mkt-featured__copy">
                <p className="mkt-featured__eyebrow">Featured product</p>
                <h2 className="mkt-section__title mkt-section__title--left">{featured.name}</h2>
                <p className="mkt-about__text">
                  {featured.description || featured.short_description || featured.short}
                </p>
                <div className="mkt-featured__actions">
                  <Link to={`/products/${featured.slug}`} className="mkt-btn mkt-btn--primary">
                    Explore Product
                  </Link>
                  <Link
                    to={`/contact?product=${encodeURIComponent(featured.name)}&product_slug=${encodeURIComponent(
                      featured.slug || ""
                    )}&request_type=demo`}
                    className="mkt-btn mkt-btn--outline"
                  >
                    {featured.cta_label || "Request Demo"}
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
