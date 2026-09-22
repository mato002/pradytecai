import React, { useEffect, useState } from "react";
import { Link, Navigate, useNavigate } from "react-router-dom";
import { useAuth } from "../auth/AuthContext";
import AdminBrandMark from "../components/admin/AdminBrandMark";

export default function LoginPage() {
  const { user, login, loading } = useAuth();
  const navigate = useNavigate();
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [remember, setRemember] = useState(true);
  const [error, setError] = useState("");

  useEffect(() => {
    document.title = "Admin Login | Prady Technologies";
  }, []);

  if (!loading && user) return <Navigate to="/admin" replace />;

  async function onSubmit(e) {
    e.preventDefault();
    setError("");
    try {
      await login(email, password);
      navigate("/admin");
    } catch (err) {
      setError(err.message || "Login failed");
    }
  }

  return (
    <div
      className="flex min-h-screen items-center justify-center px-4"
      style={{ background: "linear-gradient(160deg, #e8f5e9 0%, #f7f9f8 45%, #fff8e1 100%)" }}
    >
      <div className="w-full max-w-md">
        <div className="mb-8 text-center">
          <div className="mx-auto mb-3 flex justify-center">
            <AdminBrandMark />
          </div>
          <p className="text-xl font-bold uppercase tracking-wide text-[#004d40]">Prady Technologies</p>
          <div className="mx-auto mt-2 h-1 w-16 rounded" style={{ background: "#c9a227" }} />
          <h1 className="mt-6 text-2xl font-bold text-[#004d40]">Admin Login</h1>
          <p className="mt-1 text-sm text-[#5f6f6b]">Sign in to the Marketing Command Centre</p>
        </div>
        <form
          onSubmit={onSubmit}
          className="space-y-4 rounded-2xl border border-[#e2e8e6] bg-white p-8 shadow-[0_16px_40px_rgba(0,77,64,0.08)]"
        >
          <div>
            <label className="mb-1 block text-sm font-medium text-[#1b2b28]">Email</label>
            <input
              type="email"
              required
              className="w-full rounded-xl border border-[#e2e8e6] px-3 py-2.5 focus:border-[#43a047] focus:outline-none focus:ring-2 focus:ring-[#43a047]/30"
              value={email}
              onChange={(e) => setEmail(e.target.value)}
            />
          </div>
          <div>
            <label className="mb-1 block text-sm font-medium text-[#1b2b28]">Password</label>
            <input
              type="password"
              required
              className="w-full rounded-xl border border-[#e2e8e6] px-3 py-2.5 focus:border-[#43a047] focus:outline-none focus:ring-2 focus:ring-[#43a047]/30"
              value={password}
              onChange={(e) => setPassword(e.target.value)}
            />
          </div>
          <label className="flex items-center gap-2 text-sm text-[#5f6f6b]">
            <input type="checkbox" checked={remember} onChange={(e) => setRemember(e.target.checked)} />
            Remember me
          </label>
          {error && <p className="text-sm text-[#c62828]">{error}</p>}
          <button
            type="submit"
            className="w-full rounded-xl bg-[#004d40] py-2.5 font-semibold text-white shadow-md hover:bg-[#0a5c4a]"
          >
            Sign In
          </button>
        </form>
        <p className="mt-6 text-center text-sm">
          <Link to="/" className="font-medium text-[#004d40] hover:underline">
            ← Back to website
          </Link>
        </p>
      </div>
    </div>
  );
}
