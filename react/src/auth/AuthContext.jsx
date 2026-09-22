import React, { createContext, useContext, useEffect, useState } from "react";
import { api, ensureCsrf } from "../api/client";

const AuthContext = createContext(null);

export function AuthProvider({ children }) {
  const [user, setUser] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    (async () => {
      try {
        await ensureCsrf();
        const data = await api("/auth/me/");
        setUser(data.user);
      } catch {
        setUser(null);
      } finally {
        setLoading(false);
      }
    })();
  }, []);

  async function login(email, password) {
    await ensureCsrf();
    const data = await api("/auth/login/", { method: "POST", body: { email, password } });
    setUser(data.user);
    return data.user;
  }

  async function logout() {
    await api("/auth/logout/", { method: "POST", body: {} });
    setUser(null);
  }

  function can(perm) {
    if (!user) return false;
    if (user.is_super_admin) return true;
    return (user.permissions || []).includes(perm);
  }

  return (
    <AuthContext.Provider value={{ user, loading, login, logout, can }}>
      {children}
    </AuthContext.Provider>
  );
}

export function useAuth() {
  return useContext(AuthContext);
}
