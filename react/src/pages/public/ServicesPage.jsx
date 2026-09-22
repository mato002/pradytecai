import React, { useEffect } from "react";
import { Link } from "react-router-dom";
import { portfolio } from "../../data/portfolio";
import PradyIcon from "../../components/marketing/PradyIcon";

export default function ServicesPage() {
  useEffect(() => {
    document.title = "Industries & Solutions | Prady Technologies";
  }, []);
  return (
    <>
      <section className="mkt-section mkt-section--light">
        <div className="mkt-container">
          <div className="mkt-section__header">
            <h1 className="mkt-section__title">Industries We Serve</h1>
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
              </Link>
            ))}
          </div>
        </div>
      </section>
      <section className="mkt-section mkt-section--white">
        <div className="mkt-container">
          <div className="mkt-section__header">
            <h2 className="mkt-section__title">Solutions &amp; Capabilities</h2>
          </div>
          <div className="mkt-products-grid">
            {portfolio.capabilities.map((item) => (
              <Link key={item.name} to={item.href} className="mkt-product-card">
                <span className="mkt-product-card__icon">
                  <PradyIcon name={item.icon} className="w-7 h-7" />
                </span>
                <h3 className="mkt-product-card__title">{item.name}</h3>
                <p className="mkt-product-card__text">{item.short}</p>
              </Link>
            ))}
          </div>
        </div>
      </section>
    </>
  );
}
