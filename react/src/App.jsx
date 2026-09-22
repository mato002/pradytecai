import React from "react";
import { Link, Navigate, Route, Routes } from "react-router-dom";
import { useAuth } from "./auth/AuthContext";
import PublicLayout from "./layouts/PublicLayout";
import AdminLayout from "./layouts/AdminLayout";
import HomePage from "./pages/public/HomePage";
import AboutPage from "./pages/public/AboutPage";
import ServicesPage from "./pages/public/ServicesPage";
import ProductsPage from "./pages/public/ProductsPage";
import BlogListPage from "./pages/public/BlogListPage";
import BlogDetailPage from "./pages/public/BlogDetailPage";
import ContactPage from "./pages/public/ContactPage";
import CareersPage from "./pages/public/CareersPage";
import CareerApplyPage from "./pages/public/CareerApplyPage";
import SearchPage from "./pages/public/SearchPage";
import FaqPage from "./pages/public/FaqPage";
import PoliciesPage from "./pages/public/PoliciesPage";
import LoginPage from "./pages/LoginPage";
import AdminDashboard from "./pages/admin/Dashboard";
import AdminModule from "./pages/admin/AdminModule";
import ProductsAdmin from "./pages/admin/ProductsAdmin";
import EnquiriesAdmin from "./pages/admin/EnquiriesAdmin";
import ProductDetailPage from "./pages/public/ProductDetailPage";
import SocialOverview from "./pages/admin/social/SocialOverview";
import SocialComposer from "./pages/admin/social/SocialComposer";
import SocialCalendar from "./pages/admin/social/SocialCalendar";
import SocialQueue from "./pages/admin/social/SocialQueue";
import SocialContent from "./pages/admin/social/SocialContent";
import SocialApprovals from "./pages/admin/social/SocialApprovals";
import SocialAccounts from "./pages/admin/social/SocialAccounts";
import SocialAnalytics from "./pages/admin/social/SocialAnalytics";
import SocialInbox from "./pages/admin/social/SocialInbox";
import SocialHealth from "./pages/admin/social/SocialHealth";

function RequireAuth({ children }) {
  const { user, loading } = useAuth();
  if (loading) {
    return <div className="flex min-h-screen items-center justify-center text-slate-600">Loading…</div>;
  }
  if (!user) return <Navigate to="/login" replace />;
  return children;
}

export default function App() {
  return (
    <Routes>
      <Route element={<PublicLayout />}>
        <Route path="/" element={<HomePage />} />
        <Route path="/about" element={<AboutPage />} />
        <Route path="/services" element={<ServicesPage />} />
        <Route path="/products" element={<ProductsPage />} />
        <Route path="/products/:slug" element={<ProductDetailPage />} />
        <Route path="/careers" element={<CareersPage />} />
        <Route path="/careers/:id/apply" element={<CareerApplyPage />} />
        <Route path="/contact" element={<ContactPage />} />
        <Route path="/blog" element={<BlogListPage />} />
        <Route path="/blog/:slug" element={<BlogDetailPage />} />
        <Route path="/faq" element={<FaqPage />} />
        <Route path="/policies" element={<PoliciesPage />} />
        <Route path="/search" element={<SearchPage />} />
      </Route>

      <Route path="/login" element={<LoginPage />} />

      <Route
        path="/admin"
        element={
          <RequireAuth>
            <AdminLayout />
          </RequireAuth>
        }
      >
        <Route index element={<AdminDashboard />} />
        <Route path="products" element={<ProductsAdmin />} />
        <Route path="enquiries" element={<EnquiriesAdmin />} />
        <Route path="users" element={<AdminModule resource="users" title="Users" perm="users.view" />} />
        <Route path="roles" element={<AdminModule resource="roles" title="Roles" perm="roles.view" />} />
        <Route path="blog" element={<AdminModule resource="blog" title="Blog" perm="blog.view" />} />
        <Route path="positions" element={<AdminModule resource="positions" title="Careers" perm="careers.view" />} />
        <Route path="applications" element={<AdminModule resource="applications" title="Applications" perm="careers.view" />} />
        <Route path="campaigns" element={<AdminModule resource="campaigns" title="Campaigns" perm="campaigns.view" />} />
        <Route path="content" element={<AdminModule resource="content" title="Content Library" perm="content.view" />} />
        <Route path="content/calendar" element={<AdminModule resource="content" title="Calendar" perm="content.view" />} />
        <Route path="content/approvals" element={<AdminModule resource="content" title="Approvals" perm="content.view" />} />
        <Route path="social-accounts" element={<AdminModule resource="social-accounts" title="Social Accounts" perm="social_accounts.view" />} />
        <Route path="social" element={<SocialOverview />} />
        <Route path="social/create" element={<SocialComposer />} />
        <Route path="social/calendar" element={<SocialCalendar />} />
        <Route path="social/queue" element={<SocialQueue />} />
        <Route path="social/content" element={<SocialContent />} />
        <Route path="social/approvals" element={<SocialApprovals />} />
        <Route path="social/accounts" element={<SocialAccounts />} />
        <Route path="social/analytics" element={<SocialAnalytics />} />
        <Route path="social/inbox" element={<SocialInbox />} />
        <Route path="social/health" element={<SocialHealth />} />
        <Route path="integrations" element={<AdminModule resource="integrations" title="Integrations" perm="integrations.view" />} />
        <Route path="analytics" element={<AdminModule resource="analytics/overview" title="Analytics" perm="analytics.view" single />} />
        <Route path="analytics/*" element={<AdminModule resource="analytics/overview" title="Analytics" perm="analytics.view" single />} />
        <Route path="demos" element={<AdminModule resource="demos" title="Demo Requests" perm="demo_requests.view" />} />
        <Route path="pulse" element={<AdminModule resource="pulse" title="Marketing Pulse" perm="pulse.view" />} />
        <Route path="tasks" element={<AdminModule resource="tasks" title="Tasks" perm="tasks.view" />} />
        <Route path="subscribers" element={<AdminModule resource="subscribers" title="Subscribers" perm="subscribers.view" />} />
        <Route path="settings" element={<AdminModule resource="settings" title="Settings" perm="settings.view" />} />
        <Route path="activity-logs" element={<AdminModule resource="activity-logs" title="Activity Logs" perm="audit.view" />} />
        <Route path="profile" element={<div className="rounded-2xl border bg-white p-6">Profile settings coming next.</div>} />
      </Route>

      <Route
        path="*"
        element={
          <div className="flex min-h-screen flex-col items-center justify-center gap-3">
            <p className="text-xl font-semibold">Page not found</p>
            <Link to="/" className="text-indigo-600">
              Back home
            </Link>
          </div>
        }
      />
    </Routes>
  );
}
