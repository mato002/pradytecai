import React, { useEffect, useMemo, useState } from "react";
import { Link, NavLink } from "react-router-dom";
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
  const [scrolled, setScrolled] = useState(false);
  const [mobileOpen, setMobileOpen] = useState(false);
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
              className="mkt-header__burger"
              aria-label="Menu"
              onClick={() => setMobileOpen((v) => !v)}
            >
              <span />
              <span />
              <span />
            </button>
          </div>
        </div>
      </div>
      {mobileOpen && (
        <div className="mkt-mobile" id="mobile-menu">
          <Link to="/" onClick={() => setMobileOpen(false)}>
            Home
          </Link>
          <Link to="/products" onClick={() => setMobileOpen(false)}>
            Products
          </Link>
          <Link to="/services" onClick={() => setMobileOpen(false)}>
            Solutions / Industries
          </Link>
          <Link to="/#about" onClick={() => setMobileOpen(false)}>
            About
          </Link>
          <Link to="/careers" onClick={() => setMobileOpen(false)}>
            Careers
          </Link>
          <Link to="/blog" onClick={() => setMobileOpen(false)}>
            Blog
          </Link>
          <Link to="/#contact" onClick={() => setMobileOpen(false)}>
            Contact
          </Link>
          <Link to="/contact" className="mkt-btn mkt-btn--primary" onClick={() => setMobileOpen(false)}>
            Get Demo
          </Link>
        </div>
      )}
    </header>
  );
}
