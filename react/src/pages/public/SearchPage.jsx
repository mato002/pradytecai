import React, { useEffect, useState } from "react";
import { Link, useSearchParams } from "react-router-dom";
import { api } from "../../api/client";

export default function SearchPage() {
  const [params, setParams] = useSearchParams();
  const q = params.get("q") || "";
  const [input, setInput] = useState(q);
  const [result, setResult] = useState({ products: [], posts: [] });

  useEffect(() => {
    document.title = "Search | Prady Technologies";
    api(`/public/search/?q=${encodeURIComponent(q)}`)
      .then(setResult)
      .catch(() => setResult({ products: [], posts: [] }));
  }, [q]);

  function onSubmit(e) {
    e.preventDefault();
    setParams(input ? { q: input } : {});
  }

  return (
    <section className="mkt-section mkt-section--light">
      <div className="mkt-container" style={{ maxWidth: 800 }}>
        <h1 className="mkt-section__title mkt-section__title--left">Search</h1>
        <form onSubmit={onSubmit} className="mt-6 flex gap-2">
          <input
            className="flex-1 rounded-lg border px-3 py-2.5"
            value={input}
            onChange={(e) => setInput(e.target.value)}
            placeholder="Search products and blog…"
          />
          <button type="submit" className="mkt-btn mkt-btn--primary">
            Search
          </button>
        </form>
        {q && (
          <>
            <h2 className="mt-10 font-semibold text-[var(--prady-navy)]">Products</h2>
            <ul className="mt-2 space-y-2">
              {result.products?.map((p) => (
                <li key={p.id}>
                  <Link to={`/products#${p.slug}`}>{p.name}</Link>
                </li>
              ))}
              {!result.products?.length && <li className="text-slate-500">No products.</li>}
            </ul>
            <h2 className="mt-8 font-semibold text-[var(--prady-navy)]">Blog</h2>
            <ul className="mt-2 space-y-2">
              {result.posts?.map((p) => (
                <li key={p.id}>
                  <Link to={`/blog/${p.slug}`}>{p.title}</Link>
                </li>
              ))}
              {!result.posts?.length && <li className="text-slate-500">No posts.</li>}
            </ul>
          </>
        )}
      </div>
    </section>
  );
}
