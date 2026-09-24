import React, { useEffect, useMemo, useState } from "react";
import { Link, NavLink, useLocation } from "react-router-dom";
import { api } from "../../api/client";
import { portfolio } from "../../data/portfolio";
import PradyLogo from "./PradyLogo";

function MegaColumn({ title, items }) {
  return (
    <div>
      <p className="mkt-mega__heading">{title}</p>
      <ul className="mkt-mega__list">
        {items.map((item) => (
          <li key={item.href + item.name}>
            <Link to={item.href}>{item.name}</Link>
            {item.short && <span className="mkt-mega__desc">{item.short}</span>}
          </li>
        ))}
      </ul>
    </div>
  );
}

export default function SiteHeader() {
  const location = useLocation();
  const [scrolled, setScrolled] = useState(false);
  const [mobileOpen, setMobileOpen] = useState(false);
  const [expandedSection, setExpandedSection] = useState(null); // 'products' | 'solutions' | 'industries' | null
  const [openMega, setOpenMega] = useState(null);
  const [products, setProducts] = useState([]);

  useEffect(() => {
    const onScroll = () => setScrolled(window.scrollY > 8);
    window.addEventListener("scroll", onScroll, { passive: true });
    return () => window.removeEventListener("scroll", onScroll);
  }, []);

  useEffect(() => {
    api("/public/products/")
      .then((data) => setProducts(Array.isArray(data) ? data : []))
      .catch(() => setProducts([]));
  }, []);

  // Close mobile drawer on route navigation
  useEffect(() => {
    setMobileOpen(false);
  }, [location.pathname, location.hash]);

  // Lock body scroll when mobile drawer is open
  useEffect(() => {
    if (mobileOpen) {
      document.body.style.overflow = "hidden";
    } else {
      document.body.style.overflow = "";
    }
    return () => {
      document.body.style.overflow = "";
    };
  }, [mobileOpen]);

  // Close on Escape key
  useEffect(() => {
    const onKeyDown = (e) => {
      if (e.key === "Escape") setMobileOpen(false);
    };
    window.addEventListener("keydown", onKeyDown);
    return () => window.removeEventListener("keydown", onKeyDown);
  }, []);

  const bySlug = useMemo(
    () => Object.fromEntries(products.map((p) => [p.slug, p])),
    [products]
  );

  const productMega = portfolio.product_groups
    .map((g) => ({
      title: g.title,
      items: g.slugs
        .map((slug) => bySlug[slug])
        .filter(Boolean)
        .map((p) => ({
          name: p.name,
          short: p.short_description || p.short,
          href: `/products/${p.slug}`,
        })),
    }))
    .filter((col) => col.items.length);

  return (
    <header className={`mkt-header ${scrolled ? "is-scrolled" : ""}`}>
      <div className="mkt-container">
        <div className="mkt-header__bar">
          <div className="mkt-header__logo">
            <PradyLogo variant="nav" />
          </div>
          <nav className="mkt-nav" aria-label="Primary">
            <NavLink to="/" end className="mkt-nav__link">
              Home
            </NavLink>
            <div
              className="mkt-nav__item"
              onMouseEnter={() => setOpenMega("solutions")}
              onMouseLeave={() => setOpenMega(null)}
            >
              <button type="button" className="mkt-nav__link mkt-nav__link--btn">
                Solutions
              </button>
              {openMega === "solutions" && (
                <div className="mkt-mega">
                  <div className="mkt-mega__grid mkt-mega__grid--2">
                    <MegaColumn
                      title="Capabilities"
                      items={portfolio.capabilities.map((c) => ({
                        name: c.name,
                        short: c.short,
                        href: c.href,
                      }))}
                    />
                  </div>
                </div>
              )}
            </div>
            <div
              className="mkt-nav__item"
              onMouseEnter={() => setOpenMega("products")}
              onMouseLeave={() => setOpenMega(null)}
            >
              <button type="button" className="mkt-nav__link mkt-nav__link--btn">
                Products
              </button>
              {openMega === "products" && (
                <div className="mkt-mega">
                  <div className="mkt-mega__grid">
                    {productMega.map((col) => (
                      <MegaColumn key={col.title} title={col.title} items={col.items} />
                    ))}
                  </div>
                </div>
              )}
            </div>
            <div
              className="mkt-nav__item"
              onMouseEnter={() => setOpenMega("industries")}
              onMouseLeave={() => setOpenMega(null)}
            >
              <button type="button" className="mkt-nav__link mkt-nav__link--btn">
                Industries
              </button>
              {openMega === "industries" && (
                <div className="mkt-mega">
                  <div className="mkt-mega__grid mkt-mega__grid--2">
                    <MegaColumn
                      title="Industries"
                      items={portfolio.industries.map((c) => ({
                        name: c.name,
                        short: c.short,
                        href: c.href,
                      }))}
                    />
                  </div>
                </div>
              )}
            </div>
            <NavLink to="/#about" className="mkt-nav__link">
              About
            </NavLink>
            <NavLink to="/#contact" className="mkt-nav__link">
              Contact
            </NavLink>
          </nav>
          <div className="mkt-header__actions">
            <Link to="/contact" className="mkt-btn mkt-btn--primary mkt-btn--sm">
              Get Demo
            </Link>
            <button
              type="button"
              className={`mkt-header__burger ${mobileOpen ? "is-active" : ""}`}
              aria-label={mobileOpen ? "Close navigation menu" : "Open navigation menu"}
              aria-expanded={mobileOpen}
              aria-controls="mkt-mobile-drawer"
              onClick={() => setMobileOpen((v) => !v)}
            >
              <span className="mkt-burger-line mkt-burger-line--1" />
              <span className="mkt-burger-line mkt-burger-line--2" />
              <span className="mkt-burger-line mkt-burger-line--3" />
            </button>
          </div>
        </div>
      </div>

      {/* Backdrop overlay */}
      <div
        className={`mkt-mobile-backdrop ${mobileOpen ? "is-open" : ""}`}
        onClick={() => setMobileOpen(false)}
        aria-hidden="true"
      />

      {/* Modern High-End Mobile Navigation Drawer */}
      <div
        id="mkt-mobile-drawer"
        className={`mkt-mobile-drawer ${mobileOpen ? "is-open" : ""}`}
        role="dialog"
        aria-modal="true"
        aria-label="Mobile navigation"
      >
        <div className="mkt-mobile-drawer__scroll">
          {/* Drawer Head */}
          <div className="mkt-mobile-drawer__head">
            <PradyLogo variant="nav" />
            <button
              type="button"
              className="mkt-mobile-drawer__close"
              aria-label="Close menu"
              onClick={() => setMobileOpen(false)}
            >
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.2" strokeLinecap="round" strokeLinejoin="round">
                <line x1="18" y1="6" x2="6" y2="18" />
                <line x1="6" y1="6" x2="18" y2="18" />
              </svg>
            </button>
          </div>

          {/* Quick CTA Card */}
          <div className="mkt-mobile-drawer__cta-card">
            <div className="mkt-mobile-drawer__cta-badge">
              <span className="mkt-mobile-drawer__cta-dot" />
              <span>Enterprise Software & AI</span>
            </div>
            <p className="mkt-mobile-drawer__cta-desc">
              Transforming business workflows, payments, and tracking across Africa.
            </p>
            <Link
              to="/contact"
              className="mkt-btn mkt-btn--primary mkt-mobile-drawer__primary-btn"
              onClick={() => setMobileOpen(false)}
            >
              <span>Schedule a Demo</span>
              <span aria-hidden="true"> →</span>
            </Link>
          </div>

          {/* Main Navigation with Expandable Accordions */}
          <nav className="mkt-mobile-nav" aria-label="Mobile Primary">
            {/* Home */}
            <NavLink
              to="/"
              end
              className={({ isActive }) => `mkt-mobile-nav__item ${isActive ? "is-active" : ""}`}
              onClick={() => setMobileOpen(false)}
            >
              <span className="mkt-mobile-nav__icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                  <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                  <polyline points="9 22 9 12 15 12 15 22" />
                </svg>
              </span>
              <span className="mkt-mobile-nav__label">Home</span>
            </NavLink>

            {/* Products Accordion */}
            <div className={`mkt-mobile-accordion ${expandedSection === "products" ? "is-expanded" : ""}`}>
              <button
                type="button"
                className="mkt-mobile-accordion__header"
                onClick={() => setExpandedSection((s) => (s === "products" ? null : "products"))}
                aria-expanded={expandedSection === "products"}
              >
                <span className="mkt-mobile-nav__icon">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                    <rect x="2" y="3" width="20" height="14" rx="2" ry="2" />
                    <line x1="8" y1="21" x2="16" y2="21" />
                    <line x1="12" y1="17" x2="12" y2="21" />
                  </svg>
                </span>
                <span className="mkt-mobile-nav__label">Products & Platforms</span>
                {products.length > 0 && (
                  <span className="mkt-mobile-nav__count-badge">{products.length}</span>
                )}
                <span className="mkt-mobile-accordion__chevron" aria-hidden="true">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.5" strokeLinecap="round" strokeLinejoin="round">
                    <polyline points="6 9 12 15 18 9" />
                  </svg>
                </span>
              </button>

              <div className="mkt-mobile-accordion__body">
                <Link
                  to="/products"
                  className="mkt-mobile-sublink mkt-mobile-sublink--all"
                  onClick={() => setMobileOpen(false)}
                >
                  <span>All Products & Catalog</span>
                  <span className="mkt-mobile-sublink__arrow">→</span>
                </Link>
                {products.map((p) => (
                  <Link
                    key={p.slug}
                    to={`/products/${p.slug}`}
                    className="mkt-mobile-sublink"
                    onClick={() => setMobileOpen(false)}
                  >
                    <div className="mkt-mobile-sublink__main">
                      <span className="mkt-mobile-sublink__title">{p.name}</span>
                      {(p.short_description || p.short) && (
                        <span className="mkt-mobile-sublink__desc">
                          {p.short_description || p.short}
                        </span>
                      )}
                    </div>
                  </Link>
                ))}
              </div>
            </div>

            {/* Solutions Accordion */}
            <div className={`mkt-mobile-accordion ${expandedSection === "solutions" ? "is-expanded" : ""}`}>
              <button
                type="button"
                className="mkt-mobile-accordion__header"
                onClick={() => setExpandedSection((s) => (s === "solutions" ? null : "solutions"))}
                aria-expanded={expandedSection === "solutions"}
              >
                <span className="mkt-mobile-nav__icon">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                    <polygon points="12 2 2 7 12 12 22 7 12 2" />
                    <polyline points="2 17 12 22 22 17" />
                    <polyline points="2 12 12 17 22 12" />
                  </svg>
                </span>
                <span className="mkt-mobile-nav__label">Solutions & Capabilities</span>
                <span className="mkt-mobile-accordion__chevron" aria-hidden="true">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.5" strokeLinecap="round" strokeLinejoin="round">
                    <polyline points="6 9 12 15 18 9" />
                  </svg>
                </span>
              </button>

              <div className="mkt-mobile-accordion__body">
                <Link
                  to="/services"
                  className="mkt-mobile-sublink mkt-mobile-sublink--all"
                  onClick={() => setMobileOpen(false)}
                >
                  <span>Services & Tech Overview</span>
                  <span className="mkt-mobile-sublink__arrow">→</span>
                </Link>
                {portfolio.capabilities.map((c) => (
                  <Link
                    key={c.name}
                    to={c.href}
                    className="mkt-mobile-sublink"
                    onClick={() => setMobileOpen(false)}
                  >
                    <div className="mkt-mobile-sublink__main">
                      <span className="mkt-mobile-sublink__title">{c.name}</span>
                      {c.short && <span className="mkt-mobile-sublink__desc">{c.short}</span>}
                    </div>
                  </Link>
                ))}
              </div>
            </div>

            {/* Industries Accordion */}
            <div className={`mkt-mobile-accordion ${expandedSection === "industries" ? "is-expanded" : ""}`}>
              <button
                type="button"
                className="mkt-mobile-accordion__header"
                onClick={() => setExpandedSection((s) => (s === "industries" ? null : "industries"))}
                aria-expanded={expandedSection === "industries"}
              >
                <span className="mkt-mobile-nav__icon">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                    <path d="M3 21h18M3 7v14M21 7v14M6 11h4M6 15h4M14 11h4M14 15h4M10 3h4v4h-4z" />
                  </svg>
                </span>
                <span className="mkt-mobile-nav__label">Industries</span>
                <span className="mkt-mobile-accordion__chevron" aria-hidden="true">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.5" strokeLinecap="round" strokeLinejoin="round">
                    <polyline points="6 9 12 15 18 9" />
                  </svg>
                </span>
              </button>

              <div className="mkt-mobile-accordion__body">
                {portfolio.industries.map((ind) => (
                  <Link
                    key={ind.name}
                    to={ind.href}
                    className="mkt-mobile-sublink"
                    onClick={() => setMobileOpen(false)}
                  >
                    <div className="mkt-mobile-sublink__main">
                      <span className="mkt-mobile-sublink__title">{ind.name}</span>
                      {ind.short && <span className="mkt-mobile-sublink__desc">{ind.short}</span>}
                    </div>
                  </Link>
                ))}
              </div>
            </div>

            {/* About Prady */}
            <NavLink
              to="/#about"
              className="mkt-mobile-nav__item"
              onClick={() => setMobileOpen(false)}
            >
              <span className="mkt-mobile-nav__icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                  <circle cx="12" cy="12" r="10" />
                  <line x1="12" y1="16" x2="12" y2="12" />
                  <line x1="12" y1="8" x2="12.01" y2="8" />
                </svg>
              </span>
              <span className="mkt-mobile-nav__label">About Prady</span>
            </NavLink>

            {/* Careers */}
            <NavLink
              to="/careers"
              className={({ isActive }) => `mkt-mobile-nav__item ${isActive ? "is-active" : ""}`}
              onClick={() => setMobileOpen(false)}
            >
              <span className="mkt-mobile-nav__icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                  <rect x="2" y="7" width="20" height="14" rx="2" ry="2" />
                  <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16" />
                </svg>
              </span>
              <span className="mkt-mobile-nav__label">Careers</span>
              <span className="mkt-mobile-nav__hiring-pill">We're Hiring</span>
            </NavLink>

            {/* Blog */}
            <NavLink
              to="/blog"
              className={({ isActive }) => `mkt-mobile-nav__item ${isActive ? "is-active" : ""}`}
              onClick={() => setMobileOpen(false)}
            >
              <span className="mkt-mobile-nav__icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                  <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20" />
                  <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z" />
                </svg>
              </span>
              <span className="mkt-mobile-nav__label">Blog & Insights</span>
            </NavLink>

            {/* Contact */}
            <NavLink
              to="/contact"
              className={({ isActive }) => `mkt-mobile-nav__item ${isActive ? "is-active" : ""}`}
              onClick={() => setMobileOpen(false)}
            >
              <span className="mkt-mobile-nav__icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                  <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />
                </svg>
              </span>
              <span className="mkt-mobile-nav__label">Contact & Support</span>
            </NavLink>
          </nav>

          {/* Quick Connect & Direct Contact Footer */}
          <div className="mkt-mobile-drawer__footer">
            <p className="mkt-mobile-drawer__footer-heading">Direct Contact</p>
            <div className="mkt-mobile-drawer__contact-grid">
              {portfolio.contact?.phone && (
                <a
                  href={portfolio.contact.phone_href}
                  className="mkt-mobile-drawer__contact-item"
                >
                  <span className="mkt-mobile-drawer__contact-icon" aria-hidden="true">📞</span>
                  <div className="mkt-mobile-drawer__contact-info">
                    <span className="mkt-mobile-drawer__contact-label">Call Office</span>
                    <span className="mkt-mobile-drawer__contact-val">{portfolio.contact.phone}</span>
                  </div>
                </a>
              )}
              {portfolio.contact?.email && (
                <a
                  href={portfolio.contact.email_href}
                  className="mkt-mobile-drawer__contact-item"
                >
                  <span className="mkt-mobile-drawer__contact-icon" aria-hidden="true">✉️</span>
                  <div className="mkt-mobile-drawer__contact-info">
                    <span className="mkt-mobile-drawer__contact-label">Email Us</span>
                    <span className="mkt-mobile-drawer__contact-val">{portfolio.contact.email}</span>
                  </div>
                </a>
              )}
            </div>
            {portfolio.contact?.location && (
              <div className="mkt-mobile-drawer__meta">
                <span>📍 {portfolio.contact.location}</span>
                <span>•</span>
                <span>{portfolio.contact.hours}</span>
              </div>
            )}
          </div>
        </div>
      </div>
    </header>
  );
}
