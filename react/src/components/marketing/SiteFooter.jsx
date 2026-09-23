import React, { useEffect, useState } from "react";
import { Link } from "react-router-dom";
import { api } from "../../api/client";
import { portfolio } from "../../data/portfolio";
import PradyLogo from "./PradyLogo";

export default function SiteFooter() {
  const [footerProducts, setFooterProducts] = useState([]);
  const { contact } = portfolio;

  useEffect(() => {
    api("/public/products/")
      .then((data) => setFooterProducts(Array.isArray(data) ? data.slice(0, 6) : []))
      .catch(() => setFooterProducts([]));
  }, []);

  return (
    <footer className="mkt-footer">
      <div className="mkt-container">
        <div className="mkt-footer__grid">
          <div className="mkt-footer__brand">
            <PradyLogo variant="footer" theme="dark" />
            <p className="mkt-footer__statement">
              Smart technology solutions for ambitious businesses. We build secure, efficient software
              that drives growth.
            </p>
          </div>
          <div>
            <h3 className="mkt-footer__heading">Company</h3>
            <ul className="mkt-footer__list">
              <li>
                <Link to="/about">About</Link>
              </li>
              <li>
                <Link to="/contact">Contact</Link>
              </li>
              <li>
                <Link to="/services">Industries</Link>
              </li>
              <li>
                <Link to="/careers">Careers</Link>
              </li>
              <li>
                <Link to="/blog">Blog</Link>
              </li>
            </ul>
          </div>
          <div>
            <h3 className="mkt-footer__heading">Solutions / Products</h3>
            <ul className="mkt-footer__list">
              {footerProducts.map((p) => (
                <li key={p.slug}>
                  <Link to={`/products/${p.slug}`}>{p.name}</Link>
                </li>
              ))}
            </ul>
          </div>
          <div>
            <h3 className="mkt-footer__heading">Contact</h3>
            <ul className="mkt-footer__list">
              <li>
                Phone{" "}
                <a href={contact.phone_href}>{contact.phone}</a>
              </li>
              <li>
                Email{" "}
                <a href={contact.email_href}>{contact.email}</a>
              </li>
              <li>Location {contact.location}</li>
              <li>
                <Link to="/policies">Terms &amp; Privacy</Link>
              </li>
            </ul>
          </div>
        </div>
        <p className="mkt-footer__copy">
          © {new Date().getFullYear()} Prady Technologies Ltd. All rights reserved.
        </p>
      </div>
    </footer>
  );
}
