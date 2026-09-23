import React, { useEffect, useState } from "react";
import { Link, useParams } from "react-router-dom";
import { api } from "../../api/client";
import { getProductBySlug } from "../../data/products";
import ProductPoster from "../../components/marketing/ProductPoster";

export default function ProductDetailPage() {
  const { slug } = useParams();
  const hardcoded = getProductBySlug(slug);
  const [product, setProduct] = useState(hardcoded);
  const [error, setError] = useState("");
  const [loading, setLoading] = useState(!hardcoded);

  useEffect(() => {
    const local = getProductBySlug(slug);
    if (local) {
      setProduct(local);
      setLoading(false);
      setError("");
      document.title = `${local.name} | Prady Technologies`;
    } else {
      setLoading(true);
    }

    api(`/public/products/${encodeURIComponent(slug)}/`)
      .then((data) => {
        // Prefer hardcoded poster when API has none
        const merged = {
          ...data,
          poster_url: data.poster_url || local?.poster_url || null,
          short_description: data.short_description || data.short || local?.short_description,
        };
        setProduct(merged);
        document.title = `${merged.name} | Prady Technologies`;
        setError("");
      })
      .catch((err) => {
        if (local) return;
        setProduct(null);
        setError(err.status === 404 ? "Product not found." : err.message || "Failed to load");
        document.title = "Product | Prady Technologies";
      })
      .finally(() => setLoading(false));
  }, [slug]);

  if (loading) {
    return (
      <div className="mkt-section mkt-section--white">
        <div className="mkt-container">
          <p className="mkt-empty-hint">Loading…</p>
        </div>
      </div>
    );
  }

  if (error || !product) {
    return (
      <div className="mkt-section mkt-section--white">
        <div className="mkt-container space-y-4">
          <h1 className="mkt-section__title mkt-section__title--left">{error || "Not found"}</h1>
          <Link to="/products" className="mkt-btn mkt-btn--primary">
            Back to products
          </Link>
        </div>
      </div>
    );
  }

  const cta = product.cta_label || "Request demo";
  const contactTo = `/contact?product=${encodeURIComponent(product.name)}&product_slug=${encodeURIComponent(
    product.slug || ""
  )}&request_type=demo`;

  return (
    <div className="mkt-section mkt-section--white">
      <div className="mkt-container mkt-product-detail-wrap">
        <p className="mb-4">
          <Link to="/products" className="mkt-product-card__link" style={{ marginTop: 0 }}>
            ← All products
          </Link>
        </p>
        <div className="mkt-product-detail">
          <ProductPoster
            src={product.poster_url || null}
            alt={`${product.name} poster`}
            variant="detail"
            lazy={false}
          />
          <h1 className="mkt-section__title mkt-section__title--left mkt-product-detail__name">
            {product.name}
          </h1>
          {(product.short_description || product.short) && (
            <p className="mkt-section__subtitle mkt-section__subtitle--left">
              {product.short_description || product.short}
            </p>
          )}
          {product.market && <p className="mkt-product-card__market">{product.market}</p>}
          {product.description && (
            <div className="mkt-about__text mt-6 whitespace-pre-line">{product.description}</div>
          )}
          <div className="mkt-featured__actions mt-8">
            {product.cta_type === "external" && product.cta_url ? (
              <a href={product.cta_url} className="mkt-btn mkt-btn--primary" target="_blank" rel="noreferrer">
                {cta}
              </a>
            ) : (
              <Link to={contactTo} className="mkt-btn mkt-btn--primary">
                {cta}
              </Link>
            )}
            <Link to="/contact" className="mkt-btn mkt-btn--outline">
              Contact us
            </Link>
          </div>
        </div>
      </div>
    </div>
  );
}
