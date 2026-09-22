import React, { useEffect, useState } from "react";
import { Link, useParams } from "react-router-dom";
import { api } from "../../api/client";

export default function BlogDetailPage() {
  const { slug } = useParams();
  const [post, setPost] = useState(null);
  useEffect(() => {
    api(`/public/blog/${slug}/`)
      .then((p) => {
        setPost(p);
        document.title = `${p.title} | Prady Technologies`;
      })
      .catch(() => setPost(null));
  }, [slug]);
  if (!post) {
    return (
      <div className="mkt-section">
        <div className="mkt-container">Loading…</div>
      </div>
    );
  }
  return (
    <article className="mkt-section mkt-section--white">
      <div className="mkt-container" style={{ maxWidth: 800 }}>
        <Link to="/blog" className="text-sm text-[var(--prady-sky)]">
          ← Blog
        </Link>
        <h1 className="mkt-section__title mkt-section__title--left mt-4">{post.title}</h1>
        <div className="mkt-about__text mt-6 whitespace-pre-wrap">{post.body}</div>
      </div>
    </article>
  );
}
