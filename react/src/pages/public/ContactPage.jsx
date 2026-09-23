import React, { useEffect, useState } from "react";
import { useSearchParams } from "react-router-dom";
import { api } from "../../api/client";
import PradyIcon, { hasPradyIcon } from "../../components/marketing/PradyIcon";
import { portfolio } from "../../data/portfolio";

const FALLBACK_CHANNELS = [
  {
    id: "phone",
    channel_type: "phone",
    label: "Call us",
    value: portfolio.contact.phone,
    href: portfolio.contact.phone_href,
    description: "Speak with the team during business hours.",
    is_primary: true,
    display_order: 10,
  },
  {
    id: "whatsapp",
    channel_type: "whatsapp",
    label: "WhatsApp",
    value: portfolio.contact.phone,
    href: `https://wa.me/${portfolio.contact.phone.replace(/\D/g, "")}`,
    description: "Message us for a quick reply.",
    is_primary: true,
    display_order: 20,
  },
  {
    id: "email",
    channel_type: "email",
    label: "Email",
    value: portfolio.contact.email,
    href: portfolio.contact.email_href,
    description: "Send a detailed request anytime.",
    is_primary: true,
    display_order: 30,
  },
  {
    id: "location",
    channel_type: "location",
    label: "Location",
    value: portfolio.contact.location,
    href: null,
    display_order: 40,
  },
  {
    id: "hours",
    channel_type: "hours",
    label: "Business hours",
    value: portfolio.contact.hours,
    href: null,
    display_order: 50,
  },
];

const ICON_BY_TYPE = {
  email: "email",
  phone: "phone",
  whatsapp: "whatsapp",
  facebook: "facebook",
  linkedin: "linkedin",
  twitter: "twitter",
  instagram: "instagram",
  youtube: "youtube",
  telegram: "telegram",
  location: "location",
  hours: "clock",
  other: "support",
};

const SOCIAL_TYPES = new Set([
  "facebook",
  "linkedin",
  "twitter",
  "instagram",
  "youtube",
  "telegram",
]);

function channelIcon(type) {
  const name = ICON_BY_TYPE[type] || "support";
  return hasPradyIcon(name) ? name : "support";
}

function ChannelCard({ channel }) {
  const icon = channelIcon(channel.channel_type);
  const href = channel.href || null;
  const isSocial = SOCIAL_TYPES.has(channel.channel_type);
  const className = [
    "mkt-contact-channel",
    channel.is_primary ? "mkt-contact-channel--primary" : "",
    isSocial ? "mkt-contact-channel--social" : "",
    href ? "mkt-contact-channel--link" : "",
  ]
    .filter(Boolean)
    .join(" ");

  const body = (
    <>
      <span className="mkt-contact-channel__icon" aria-hidden="true">
        <PradyIcon name={icon} className="mkt-contact-channel__svg" />
      </span>
      <span className="mkt-contact-channel__copy">
        <span className="mkt-contact-channel__label">{channel.label}</span>
        <span className="mkt-contact-channel__value">{channel.value}</span>
        {channel.description ? (
          <span className="mkt-contact-channel__desc">{channel.description}</span>
        ) : null}
      </span>
    </>
  );

  if (href) {
    const external = href.startsWith("http");
    return (
      <a
        className={className}
        href={href}
        {...(external ? { target: "_blank", rel: "noopener noreferrer" } : {})}
      >
        {body}
      </a>
    );
  }

  return <div className={className}>{body}</div>;
}

export default function ContactPage() {
  const [params] = useSearchParams();
  const productName = params.get("product") || "";
  const productSlug = params.get("product_slug") || "";
  const requestType = params.get("request_type") || (productSlug || productName ? "demo" : "contact");

  const [channels, setChannels] = useState(FALLBACK_CHANNELS);
  const [form, setForm] = useState({
    name: "",
    company: "",
    email: "",
    phone: "",
    subject: productName ? `Demo: ${productName}` : "",
    message: "",
    topic: params.get("topic") || "",
    preferred_at: "",
    request_type: requestType,
    product_slug: productSlug,
  });
  const [loading, setLoading] = useState(false);
  const [feedback, setFeedback] = useState(null);

  useEffect(() => {
    document.title = "Contact | Prady Technologies";
    let cancelled = false;
    api("/public/contact-info/")
      .then((data) => {
        if (cancelled) return;
        const rows = Array.isArray(data?.channels) ? data.channels : [];
        if (rows.length) setChannels(rows);
      })
      .catch(() => {
        /* keep portfolio fallback */
      });
    return () => {
      cancelled = true;
    };
  }, []);

  async function onSubmit(e) {
    e.preventDefault();
    setLoading(true);
    setFeedback(null);
    try {
      const payload = {
        name: form.name,
        company: form.company,
        email: form.email,
        phone: form.phone,
        subject: form.subject,
        message: form.message,
        topic: form.topic,
        request_type: form.request_type || requestType,
        product_slug: form.product_slug || undefined,
        preferred_at: form.preferred_at || undefined,
        source: productSlug ? "product_page" : "contact_page",
        landing_page: window.location.pathname + window.location.search,
      };
      await new Promise((r) => setTimeout(r, 500));
      await api("/public/contact/", { method: "POST", body: payload });
      setFeedback({ type: "success", message: "Thank you — we received your message." });
      setForm((f) => ({
        ...f,
        name: "",
        company: "",
        email: "",
        phone: "",
        subject: productName ? `Demo: ${productName}` : "",
        message: "",
        preferred_at: "",
      }));
    } catch (err) {
      setFeedback({ type: "error", message: err.message || "Failed to send." });
    } finally {
      setLoading(false);
    }
  }

  function set(k, v) {
    setForm((f) => ({ ...f, [k]: v }));
  }

  const direct = channels.filter((c) => !SOCIAL_TYPES.has(c.channel_type));
  const social = channels.filter((c) => SOCIAL_TYPES.has(c.channel_type));

  return (
    <section className="mkt-section mkt-section--light mkt-contact">
      <div className="mkt-container">
        <header className="mkt-contact__intro">
          <h1 className="mkt-section__title mkt-section__title--left">Contact</h1>
          <p className="mkt-section__subtitle mkt-section__subtitle--left">
            Call, message, or write to us — or send a request with the form. We route every enquiry to
            the right team.
          </p>
          {(productName || productSlug) && (
            <p className="mkt-contact__product-hint">
              Request related to: <strong>{productName || productSlug}</strong>
            </p>
          )}
        </header>

        <div className="mkt-contact__grid">
          <div className="mkt-contact__channels">
            <h2 className="mkt-contact__heading">Ways to reach us</h2>
            <div className="mkt-contact-channel-grid">
              {direct.map((channel) => (
                <ChannelCard key={channel.id || `${channel.channel_type}-${channel.label}`} channel={channel} />
              ))}
            </div>

            {social.length ? (
              <div className="mkt-contact-social">
                <h2 className="mkt-contact__heading">Social</h2>
                <div className="mkt-contact-social__row">
                  {social.map((channel) => (
                    <ChannelCard
                      key={channel.id || `${channel.channel_type}-${channel.label}`}
                      channel={channel}
                    />
                  ))}
                </div>
              </div>
            ) : null}
          </div>

          <form onSubmit={onSubmit} className="mkt-contact-form" noValidate>
            <h2 className="mkt-contact__heading">Send a message</h2>
            <p className="mkt-contact-form__hint">The contact form is always available.</p>

            <label className="mkt-contact-form__field">
              <span>Name</span>
              <input
                required
                className="mkt-input"
                value={form.name}
                onChange={(e) => set("name", e.target.value)}
                autoComplete="name"
              />
            </label>
            <label className="mkt-contact-form__field">
              <span>Company</span>
              <input
                className="mkt-input"
                value={form.company}
                onChange={(e) => set("company", e.target.value)}
                autoComplete="organization"
              />
            </label>
            <label className="mkt-contact-form__field">
              <span>Email</span>
              <input
                required
                type="email"
                className="mkt-input"
                value={form.email}
                onChange={(e) => set("email", e.target.value)}
                autoComplete="email"
              />
            </label>
            <label className="mkt-contact-form__field">
              <span>Phone</span>
              <input
                className="mkt-input"
                value={form.phone}
                onChange={(e) => set("phone", e.target.value)}
                autoComplete="tel"
              />
            </label>
            <label className="mkt-contact-form__field">
              <span>Subject</span>
              <input
                required
                className="mkt-input"
                value={form.subject}
                onChange={(e) => set("subject", e.target.value)}
              />
            </label>

            {(form.request_type === "demo" || productSlug || productName) && (
              <label className="mkt-contact-form__field">
                <span>Preferred demo / contact date (optional)</span>
                <input
                  type="datetime-local"
                  className="mkt-input"
                  value={form.preferred_at}
                  onChange={(e) => set("preferred_at", e.target.value)}
                />
              </label>
            )}

            <label className="mkt-contact-form__field">
              <span>Message</span>
              <textarea
                required
                className="mkt-textarea"
                rows={5}
                value={form.message}
                onChange={(e) => set("message", e.target.value)}
              />
            </label>

            <button type="submit" className="mkt-btn mkt-btn--primary mkt-contact-form__submit" disabled={loading}>
              {loading ? "Sending..." : "Send message"}
            </button>

            {feedback ? (
              <div
                className={`mkt-contact-form__feedback ${
                  feedback.type === "success" ? "is-success" : "is-error"
                }`}
                role="status"
              >
                {feedback.message}
              </div>
            ) : null}
          </form>
        </div>
      </div>
    </section>
  );
}
