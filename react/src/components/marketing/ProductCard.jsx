import React from "react";
import { Link } from "react-router-dom";
import ProductPoster from "./ProductPoster";

/**
 * Compact product card — poster, name, short description, one Explore action.
 */
export default function ProductCard({ product, lazyPoster = true, asLink = true }) {
  const detailTo = `/products/${product.slug}`;
  const description = product.short_description || product.short || "";
  const posterAlt = `${product.name} poster`;

  const body = (
    <>
      <ProductPoster
        src={product.poster_url || null}
        alt={posterAlt}
        variant="card"
        lazy={lazyPoster}
      />
      <h3 className="mkt-product-card__title">{product.name}</h3>
      {description ? <p className="mkt-product-card__text">{description}</p> : null}
      <span className="mkt-product-card__link">
        Explore <span aria-hidden="true">→</span>
      </span>
    </>
  );

  if (asLink) {
    return (
      <Link to={detailTo} className="mkt-product-card" id={product.slug}>
        {body}
      </Link>
    );
  }

  return (
    <article className="mkt-product-card" id={product.slug}>
      {body}
    </article>
  );
}
