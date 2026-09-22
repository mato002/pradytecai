import React from "react";
import { Outlet } from "react-router-dom";
import SiteHeader from "../components/marketing/SiteHeader";
import SiteFooter from "../components/marketing/SiteFooter";
import ChatbotWidget from "../components/marketing/ChatbotWidget";

export default function PublicLayout() {
  return (
    <div className="prady-site antialiased">
      <SiteHeader />
      <main>
        <Outlet />
      </main>
      <SiteFooter />
      <ChatbotWidget />
    </div>
  );
}
