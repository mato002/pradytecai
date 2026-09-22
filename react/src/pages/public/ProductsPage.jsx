import React, { useEffect } from "react";
import { Link, useLocation } from "react-router-dom";
import { portfolio } from "../../data/portfolio";
import PradyIcon from "../../components/marketing/PradyIcon";

export default function ProductsPage() {
  const location = useLocation();
  useEffect(() => {
    document.title = "Products | Prady Technologies";
    if (location.hash) {
      const el = document.querySelector(location.hash);
      if (el) setTimeout(() => el.scrollIntoView({ behavior: "smooth" }), 50);
    }
  }, [location]);

  return (
    <div className="mkt-section mkt-section--white">
      <div className="mkt-container">
        <div className="mkt-section__header">
          <h1 className="mkt-section__title">Our Products</h1>
          <p className="mkt-section__subtitle">
            Practical platforms built for African businesses across finance, mobility, property and
            commerce.
          </p>
        </div>
        {portfolio.product_groups.map((group) => (
          <div key={group.key} className="mkt-product-group">
            <h2 className="mkt-product-group__title">{group.title}</h2>
            <div className="mkt-products-grid">
              {group.slugs.map((slug) => {
                const p = portfolio.products.find((x) => x.slug === slug);
                if (!p) return null;
                return (
                  <article key={slug} id={slug} className="mkt-product-card">
                    <span className="mkt-product-card__icon">
                      <PradyIcon name={p.icon} className="w-7 h-7" />
                    </span>
                    <h3 className="mkt-product-card__title">{p.name}</h3>
                    <p className="mkt-product-card__text">{p.description}</p>
                    {p.market && <p className="mkt-product-card__market">{p.market}</p>}
                    <Link to={`/contact?product=${encodeURIComponent(p.name)}`} className="mkt-product-card__link">
                      Request demo →
                    </Link>
                  </article>
                );
              })}
            </div>
          </div>
        ))}
      </div>
    </div>
  );
}
