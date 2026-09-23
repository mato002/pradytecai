import React, { useEffect, useState } from "react";
import { Link, useLocation } from "react-router-dom";
import { api } from "../../api/client";
import { portfolio } from "../../data/portfolio";
import PradyIcon from "../../components/marketing/PradyIcon";
import HeroVisual from "../../components/marketing/HeroVisual";
import ProductCard from "../../components/marketing/ProductCard";

export default function HomePage() {
  const location = useLocation();
  const [products, setProducts] = useState([]);

  useEffect(() => {
    api("/public/home/")
      .then((data) => {
        const list = Array.isArray(data.products) ? data.products : [];
        setProducts(list);
      })
      .catch(() => setProducts([]));
  }, []);

  useEffect(() => {
    if (!location.hash) return;
    const id = location.hash.replace("#", "");
    const el = document.getElementById(id) || document.querySelector(location.hash);
    if (el) {
      const t = window.setTimeout(() => el.scrollIntoView({ behavior: "smooth", block: "start" }), 50);
      return () => window.clearTimeout(t);
    }
  }, [location, products]);

  useEffect(() => {
    document.title = "Prady Technologies | Smart Technology Solutions for African Businesses";
  }, []);

  const homeProducts = products.slice(0, 8);

  return (
    <>
      <section className="mkt-hero" aria-labelledby="home-hero-heading">
        <div className="mkt-container mkt-hero__inner">
          <div className="mkt-hero__copy">
            <p className="mkt-hero__eyebrow">Doing IT differently</p>
            <h1 id="home-hero-heading" className="mkt-hero__title">
              <span className="mkt-hero__title-line">Smart Technology Solutions</span>
              <span className="mkt-hero__title-line">for Ambitious Businesses</span>
            </h1>
            <p className="mkt-hero__lead">
              Secure business software for finance, operations, mobility and commerce.
            </p>
            <div className="mkt-hero__actions">
              <Link to="/contact" className="mkt-btn mkt-btn--light mkt-btn--hero">
                Get Demo <span aria-hidden="true">→</span>
              </Link>
              <Link to="/products" className="mkt-btn mkt-btn--ghost-hero mkt-btn--hero">
                Our Products
              </Link>
            </div>
          </div>
          <div className="mkt-hero__media">
            <HeroVisual />
          </div>
        </div>
      </section>

      <section className="mkt-trust" aria-label="Why Prady">
        <div className="mkt-container">
          <div className="mkt-trust__grid mkt-trust__grid--three">
            {portfolio.trust.map((t) => (
              <div key={t.label} className="mkt-trust__item mkt-trust__item--center">
                <span className="mkt-trust__icon">
                  <PradyIcon name={t.icon} className="w-4 h-4 sm:w-5 sm:h-5" />
                </span>
                <p className="mkt-trust__label">{t.label}</p>
              </div>
            ))}
          </div>
        </div>
      </section>

      <section id="solutions" className="mkt-section mkt-section--solutions">
        <div className="mkt-container">
          <div className="mkt-section__header mkt-section__header--solutions">
            <h2 className="mkt-section__title">Our Solutions</h2>
            <p className="mkt-section__subtitle mkt-section__subtitle--desktop">
              Connected systems designed to streamline operations and scale your business.
            </p>
          </div>
          <div className="mkt-solutions-grid">
            {portfolio.solutions.map((s) => (
              <Link key={s.name} to={s.href} className="mkt-solution-card">
                <span className="mkt-solution-card__icon">
                  <PradyIcon name={s.icon} className="mkt-solution-card__svg" />
                </span>
                <h3 className="mkt-solution-card__title">{s.name}</h3>
                <p className="mkt-solution-card__text">{s.short}</p>
              </Link>
            ))}
          </div>
        </div>
      </section>

      <section id="products" className="mkt-section mkt-section--products">
        <div className="mkt-container">
          <div className="mkt-section__header">
            <h2 className="mkt-section__title">Our Products</h2>
            <p className="mkt-section__subtitle">
              Practical platforms for finance, mobility, property and commerce.
            </p>
          </div>
          {homeProducts.length > 0 ? (
            <div className="mkt-products-grid">
              {homeProducts.map((p, index) => (
                <ProductCard key={p.slug || p.id} product={p} lazyPoster={index > 1} />
              ))}
            </div>
          ) : (
            <p className="mkt-empty-hint">Products will appear here once published.</p>
          )}
          <div className="mkt-section__more">
            <Link to="/products" className="mkt-btn mkt-btn--outline">
              View All Products
            </Link>
          </div>
        </div>
      </section>

      <section id="about" className="mkt-section mkt-section--white">
        <div className="mkt-container">
          <div className="mkt-home-split">
            <div>
              <h2 className="mkt-section__title mkt-section__title--left">About Prady</h2>
              <p className="mkt-about__text">
                {portfolio.company} builds secure, efficient software for finance, operations,
                mobility and digital commerce across Africa.
              </p>
              <p className="mkt-about__text">
                Our approach — <strong>{portfolio.tagline}</strong> — means platforms that fit real
                institutional workflows, payments and growth.
              </p>
              <Link to="/about" className="mkt-btn mkt-btn--outline">
                Learn more about us
              </Link>
            </div>
            <div className="mkt-home-about-points">
              {portfolio.trust.map((t) => (
                <div key={t.label} className="mkt-about__point">
                  <span className="mkt-about__point-icon">
                    <PradyIcon name={t.icon} className="w-5 h-5" />
                  </span>
                  <div>
                    <h3>{t.label}</h3>
                    <p>
                      {t.label === "Secure by Design"
                        ? "Security-minded platforms built for sensitive financial and business data."
                        : t.label === "Built for African Businesses"
                          ? "Designed around African markets, payments and institutional workflows."
                          : "Responsive support to help your teams adopt and operate confidently."}
                    </p>
                  </div>
                </div>
              ))}
            </div>
          </div>
        </div>
      </section>

      <section id="contact" className="mkt-section mkt-section--light">
        <div className="mkt-container">
          <div className="mkt-home-split mkt-home-split--contact">
            <div>
              <h2 className="mkt-section__title mkt-section__title--left">Contact</h2>
              <p className="mkt-section__subtitle mkt-section__subtitle--left">
                Tell us what you need and we&apos;ll route it to the right team.
              </p>
              <ul className="mkt-home-contact-list">
                <li>
                  <strong>Phone</strong>
                  <a href={portfolio.contact.phone_href}>{portfolio.contact.phone}</a>
                </li>
                <li>
                  <strong>Email</strong>
                  <a href={portfolio.contact.email_href}>{portfolio.contact.email}</a>
                </li>
                <li>
                  <strong>Location</strong>
                  <span>{portfolio.contact.location}</span>
                </li>
                <li>
                  <strong>Hours</strong>
                  <span>{portfolio.contact.hours}</span>
                </li>
              </ul>
            </div>
            <HomeContactForm />
          </div>
        </div>
      </section>

      <section className="mkt-cta">
        <div className="mkt-container mkt-cta__inner">
          <h2 className="mkt-cta__title">Tell us what you&apos;re trying to improve.</h2>
          <p className="mkt-cta__text">
            We&apos;ll help you find the right Prady platform for your business.
          </p>
          <div className="mkt-cta__actions">
            <Link to="/contact" className="mkt-btn mkt-btn--light">
              Get Demo
            </Link>
            <a href="#contact" className="mkt-btn mkt-btn--ghost">
              Talk to Us
            </a>
          </div>
        </div>
      </section>
    </>
  );
}

function HomeContactForm() {
  const [form, setForm] = useState({
    name: "",
    company: "",
    email: "",
    phone: "",
    subject: "",
    message: "",
  });
  const [loading, setLoading] = useState(false);
  const [feedback, setFeedback] = useState(null);

  function set(k, v) {
    setForm((f) => ({ ...f, [k]: v }));
  }

  async function onSubmit(e) {
    e.preventDefault();
    setLoading(true);
    setFeedback(null);
    try {
      await api("/public/contact/", {
        method: "POST",
        body: {
          name: form.name,
          company: form.company,
          email: form.email,
          phone: form.phone,
          subject: form.subject,
          message: form.message,
          request_type: "contact",
          source: "home_page",
          landing_page: window.location.pathname,
        },
      });
      setFeedback({ type: "success", message: "Thank you — we received your message." });
      setForm({ name: "", company: "", email: "", phone: "", subject: "", message: "" });
    } catch (err) {
      setFeedback({ type: "error", message: err.message || "Failed to send." });
    } finally {
      setLoading(false);
    }
  }

  return (
    <form onSubmit={onSubmit} className="mkt-home-contact-form">
      {["name", "company", "email", "phone", "subject"].map((k) => (
        <input
          key={k}
          required={["name", "email", "subject"].includes(k)}
          className="mkt-input"
          placeholder={k.charAt(0).toUpperCase() + k.slice(1)}
          value={form[k]}
          onChange={(e) => set(k, e.target.value)}
        />
      ))}
      <textarea
        required
        className="mkt-textarea"
        rows={4}
        placeholder="Message"
        value={form.message}
        onChange={(e) => set("message", e.target.value)}
      />
      <button type="submit" className="mkt-btn mkt-btn--primary" disabled={loading}>
        {loading ? "Sending…" : "Send message"}
      </button>
      {feedback && (
        <p
          className={`mkt-home-contact-feedback ${
            feedback.type === "success" ? "is-success" : "is-error"
          }`}
        >
          {feedback.message}
        </p>
      )}
    </form>
  );
}
