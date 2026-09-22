import React, { useEffect } from "react";

export default function PoliciesPage() {
  useEffect(() => {
    document.title = "Terms & Privacy | Prady Technologies";
  }, []);
  return (
    <section className="mkt-section mkt-section--white">
      <div className="mkt-container" style={{ maxWidth: 800 }}>
        <h1 className="mkt-section__title mkt-section__title--left">Terms &amp; Privacy</h1>
        <p className="mkt-about__text mt-6">
          Prady Technologies Ltd respects your privacy. Information submitted through our website forms
          is used to respond to enquiries, demos and career applications. We do not sell personal data.
        </p>
        <p className="mkt-about__text">
          By using this site you agree to communicate with us in good faith and not misuse our platforms
          or contact channels. For privacy requests email marketing@pradytecai.com.
        </p>
      </div>
    </section>
  );
}
