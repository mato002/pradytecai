import React, { useEffect, useState } from "react";
import { Link } from "react-router-dom";
import { api } from "../../api/client";

export default function BlogListPage() {
  const [posts, setPosts] = useState([]);
  useEffect(() => {
    document.title = "Blog | Prady Technologies";
    api("/public/blog/").then(setPosts).catch(() => setPosts([]));
  }, []);
  return (
    <section className="mkt-section mkt-section--white">
      <div className="mkt-container" style={{ maxWidth: 800 }}>
        <h1 className="mkt-section__title mkt-section__title--left">Blog</h1>
        <ul className="mt-10 space-y-6">
          {posts.map((p) => (
            <li key={p.id} className="border-b border-[var(--border-light)] pb-6">
              <Link to={`/blog/${p.slug}`} className="text-2xl font-semibold text-[var(--prady-navy)]">
                {p.title}
              </Link>
              <p className="mt-2 text-slate-600">{p.excerpt}</p>
            </li>
          ))}
          {!posts.length && <p className="text-slate-500">No posts published yet.</p>}
        </ul>
      </div>
    </section>
  );
}
