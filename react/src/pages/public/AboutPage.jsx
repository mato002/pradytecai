import React, { useEffect } from "react";
import { Link } from "react-router-dom";
import { portfolio } from "../../data/portfolio";

export default function AboutPage() {
  useEffect(() => {
    document.title = "About | Prady Technologies";
  }, []);
  return (
    <section className="mkt-section mkt-section--white">
      <div className="mkt-container" style={{ maxWidth: 860 }}>
        <h1 className="mkt-section__title mkt-section__title--left">About Prady Technologies</h1>
        <p className="mkt-about__text mt-6">
          {portfolio.company} builds secure, efficient software solutions for lenders, SACCOs,
          businesses, property managers, mobility and digital commerce across Africa.
        </p>
        <p className="mkt-about__text">
          Our tagline — <strong>{portfolio.tagline}</strong> — reflects how we design platforms that
          fit real institutional workflows, payments, and growth.
        </p>
        <p className="mkt-about__text">
          Based in {portfolio.contact.location}. Reach us at{" "}
          <a href={portfolio.contact.email_href}>{portfolio.contact.email}</a> or{" "}
          <a href={portfolio.contact.phone_href}>{portfolio.contact.phone}</a>.
        </p>
        <div className="mt-8 flex gap-3">
          <Link to="/contact" className="mkt-btn mkt-btn--primary">
            Contact us
          </Link>
          <Link to="/products" className="mkt-btn mkt-btn--outline">
            View products
          </Link>
        </div>
      </div>
    </section>
  );
}
