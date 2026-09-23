import React, { useEffect, useState } from "react";
import { Link, useParams } from "react-router-dom";
import { api } from "../../api/client";
import ProductDetailView from "../../components/marketing/ProductDetailView";
import { applyProductSeo, clearProductSeo } from "../../lib/productDetailModel";

export default function ProductDetailPage() {
  const { slug } = useParams();
  const [product, setProduct] = useState(null);
  const [status, setStatus] = useState("loading");

  useEffect(() => {
    let cancelled = false;
    setStatus("loading");
    setProduct(null);
    api(`/public/products/${encodeURIComponent(slug)}/`)
      .then((data) => {
        if (cancelled) return;
        setProduct(data);
        applyProductSeo(data);
        setStatus("ready");
      })
      .catch((err) => {
        if (cancelled) return;
        setProduct(null);
        setStatus(err.status === 404 ? "missing" : "error");
        document.title = "Product | Prady Technologies";
      });
    return () => {
      cancelled = true;
      clearProductSeo();
    };
  }, [slug]);

  if (status === "loading") {
    return (
      <div className="mkt-section mkt-section--white">
        <div className="mkt-container">
          <p className="mkt-empty-hint">Loading product…</p>
        </div>
      </div>
    );
  }

  if (status === "missing" || status === "error" || !product) {
    const missing = status === "missing";
    return (
      <div className="mkt-section mkt-section--white">
        <div className="mkt-container mkt-pdp-state">
          <h1 className="mkt-section__title mkt-section__title--left">
            {missing ? "Product not found" : "We couldn’t load this product"}
          </h1>
          <p className="mkt-section__subtitle mkt-section__subtitle--left">
            {missing
              ? "That product is not published, or the link is out of date."
              : "The product page didn’t load. Check the connection and try again."}
          </p>
          <div className="mkt-featured__actions">
            <Link to="/products" className="mkt-btn mkt-btn--primary">
              Back to products
            </Link>
            {status === "error" ? (
              <button type="button" className="mkt-btn mkt-btn--outline" onClick={() => window.location.reload()}>
                Try again
              </button>
            ) : null}
          </div>
        </div>
      </div>
    );
  }

  return <ProductDetailView key={product.slug || slug} product={product} />;
}
