import React, { useEffect, useMemo, useState } from "react";
import { Link, useLocation } from "react-router-dom";
import { api } from "../../api/client";
import { portfolio } from "../../data/portfolio";
import PradyIcon from "../../components/marketing/PradyIcon";

function ProductCard({ product }) {
  const cta = product.cta_label || "Request demo";
  const detailTo = `/products/${product.slug}`;
  const demoTo =
    product.cta_type === "external" && product.cta_url
      ? product.cta_url
      : `/contact?product=${encodeURIComponent(product.name)}&product_slug=${encodeURIComponent(
          product.slug || ""
        )}&request_type=demo`;

  return (
    <article id={product.slug} className="mkt-product-card">
      {product.poster_url ? (
        <img
          src={product.poster_url}
          alt=""
          className="mkt-product-card__poster"
          loading="lazy"
        />
      ) : (
        <span className="mkt-product-card__icon" aria-hidden="true">
          <PradyIcon name={product.icon || "cog"} className="w-7 h-7" />
        </span>
      )}
      <h3 className="mkt-product-card__title">{product.name}</h3>
      <p className="mkt-product-card__text">
        {product.short_description || product.short || product.description}
      </p>
      {product.market && <p className="mkt-product-card__market">{product.market}</p>}
      <div className="mkt-product-card__actions">
        <Link to={detailTo} className="mkt-product-card__link">
          Learn more →
        </Link>
        {product.cta_type === "external" && product.cta_url ? (
          <a href={demoTo} className="mkt-product-card__demo" target="_blank" rel="noreferrer">
            {cta}
          </a>
        ) : (
          <Link to={demoTo} className="mkt-product-card__demo">
            {cta}
          </Link>
        )}
      </div>
    </article>
  );
}

export default function ProductsPage() {
  const location = useLocation();
  const [products, setProducts] = useState([]);
  const [error, setError] = useState("");
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    document.title = "Products | Prady Technologies";
    api("/public/products/")
      .then((data) => setProducts(Array.isArray(data) ? data : []))
      .catch((err) => setError(err.message || "Failed to load products"))
      .finally(() => setLoading(false));
  }, []);

  useEffect(() => {
    if (location.hash && products.length) {
      const el = document.querySelector(location.hash);
      if (el) setTimeout(() => el.scrollIntoView({ behavior: "smooth" }), 50);
    }
  }, [location, products]);

  const bySlug = useMemo(
    () => Object.fromEntries(products.map((p) => [p.slug, p])),
    [products]
  );

  const grouped = useMemo(() => {
    const used = new Set();
    const groups = portfolio.product_groups
      .map((group) => {
        const items = group.slugs.map((slug) => bySlug[slug]).filter(Boolean);
        items.forEach((p) => used.add(p.slug));
        return { ...group, items };
      })
      .filter((g) => g.items.length);

    const leftovers = products.filter((p) => !used.has(p.slug));
    if (leftovers.length) {
      groups.push({ key: "more", title: "More Products", items: leftovers });
    }
    return groups;
  }, [products, bySlug]);

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
        {loading && <p className="text-slate-600">Loading products…</p>}
        {error && <p className="text-red-700">{error}</p>}
        {!loading && !error && !products.length && (
          <p className="text-slate-600">Products will appear here once published.</p>
        )}
        {grouped.map((group) => (
          <div key={group.key} className="mkt-product-group">
            <h2 className="mkt-product-group__title">{group.title}</h2>
            <div className="mkt-products-grid">
              {group.items.map((p) => (
                <ProductCard key={p.id || p.slug} product={p} />
              ))}
            </div>
          </div>
        ))}
      </div>
    </div>
  );
}
