import React, { useEffect, useState } from "react";
import { Link } from "react-router-dom";
import { api } from "../../api/client";
import { portfolio } from "../../data/portfolio";
import PradyIcon from "./PradyIcon";
import PradyLogo from "./PradyLogo";

const SOCIAL_TYPES = new Set([
  "linkedin",
  "twitter",
  "facebook",
  "instagram",
  "youtube",
  "tiktok",
  "telegram",
  "github",
  "website",
]);

export default function SiteFooter() {
  const [footerProducts, setFooterProducts] = useState([]);
  const [channels, setChannels] = useState([]);
  const { contact } = portfolio;

  useEffect(() => {
    api("/public/products/")
      .then((data) => setFooterProducts(Array.isArray(data) ? data.slice(0, 6) : []))
      .catch(() => setFooterProducts([]));

    api("/public/contact-info/")
      .then((data) => {
        const rows = Array.isArray(data?.channels) ? data.channels : [];
        if (rows.length) setChannels(rows);
      })
      .catch(() => {
        // Fall back to portfolio.contact
      });
  }, []);

  const socialChannels = channels.filter(
    (c) => c.is_active !== false && SOCIAL_TYPES.has(c.channel_type)
  );
  const phoneChannel = channels.find(
    (c) => c.is_active !== false && c.channel_type === "phone"
  );
  const whatsappChannel = channels.find(
    (c) => c.is_active !== false && c.channel_type === "whatsapp"
  );
  const emailChannel = channels.find(
    (c) => c.is_active !== false && c.channel_type === "email"
  );
  const locationChannel = channels.find(
    (c) => c.is_active !== false && c.channel_type === "location"
  );

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

            {/* Dynamic Social Media Handles */}
            {socialChannels.length > 0 && (
              <div className="mkt-footer__social">
                <span className="mkt-footer__social-label">Follow Us</span>
                <div className="mkt-footer__social-links">
                  {socialChannels.map((soc) => (
                    <a
                      key={soc.id || soc.channel_type}
                      href={soc.href}
                      target="_blank"
                      rel="noopener noreferrer"
                      className="mkt-footer__social-btn"
                      title={soc.label || soc.channel_type}
                      aria-label={soc.label || soc.channel_type}
                    >
                      <PradyIcon name={soc.channel_type === "website" ? "globe" : soc.channel_type} className="w-4 h-4" />
                    </a>
                  ))}
                </div>
              </div>
            )}
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
                <a href={phoneChannel?.href || contact.phone_href}>
                  {phoneChannel?.value || contact.phone}
                </a>
              </li>
              {whatsappChannel && (
                <li>
                  WhatsApp{" "}
                  <a
                    href={whatsappChannel.href}
                    target="_blank"
                    rel="noopener noreferrer"
                    className="mkt-footer__whatsapp-link"
                  >
                    {whatsappChannel.value}
                  </a>
                </li>
              )}
              <li>
                Email{" "}
                <a href={emailChannel?.href || contact.email_href}>
                  {emailChannel?.value || contact.email}
                </a>
              </li>
              <li>Location {locationChannel?.value || contact.location}</li>
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
