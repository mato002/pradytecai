import React, { useEffect, useMemo, useState } from "react";
import { useLocation } from "react-router-dom";
import { api } from "../../api/client";
import { portfolio } from "../../data/portfolio";
import ProductCard from "../../components/marketing/ProductCard";

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
        {loading && <p className="mkt-empty-hint">Loading products…</p>}
        {error && <p className="mkt-empty-hint mkt-empty-hint--error">{error}</p>}
        {!loading && !error && !products.length && (
          <p className="mkt-empty-hint">Products will appear here once published.</p>
        )}
        {grouped.map((group) => (
          <div key={group.key} className="mkt-product-group">
            <h2 className="mkt-product-group__title">{group.title}</h2>
            <div className="mkt-products-grid">
              {group.items.map((p, index) => (
                <ProductCard
                  key={p.id || p.slug}
                  product={p}
                  lazyPoster={index > 0 || grouped.indexOf(group) > 0}
                />
              ))}
            </div>
          </div>
        ))}
      </div>
    </div>
  );
}
