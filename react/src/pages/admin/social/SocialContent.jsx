import React, { useEffect, useMemo, useState } from "react";
import { getMockContentItems } from "../../../data/socialMockData";
import { PostCard } from "../../../components/social/SocialCards";
import {
  SocialEmptyState,
  SocialFilterBar,
  SocialPageHeader,
  SocialSelect,
  SocialSkeleton,
  SocialToast,
} from "../../../components/social/SocialShared";

const TABS = ["All", "Drafts", "Scheduled", "Published", "Failed"];

const TAB_STATUS = {
  All: null,
  Drafts: "Draft",
  Scheduled: "Scheduled",
  Published: "Published",
  Failed: "Failed",
};

export default function SocialContent() {
  const [loading, setLoading] = useState(true);
  const [items, setItems] = useState([]);
  const [tab, setTab] = useState("All");
  const [view, setView] = useState("list");
  const [search, setSearch] = useState("");
  const [toast, setToast] = useState("");
  const [filters, setFilters] = useState({
    platform: "all",
    product: "all",
    campaign: "all",
    author: "all",
  });

  useEffect(() => {
    const t = setTimeout(() => {
      setItems(getMockContentItems());
      setLoading(false);
    }, 400);
    return () => clearTimeout(t);
  }, []);

  const filtered = useMemo(() => {
    const status = TAB_STATUS[tab];
    return items.filter((item) => {
      if (status && item.status !== status) return false;
      if (tab === "Drafts" && item.status !== "Draft" && item.status !== "In review") {
        if (item.status !== "Draft") return false;
      }
      if (filters.platform !== "all" && !item.platforms.includes(filters.platform)) return false;
      if (filters.product !== "all" && item.productId !== filters.product) return false;
      if (filters.campaign !== "all" && item.campaignId !== filters.campaign) return false;
      if (filters.author !== "all" && item.author !== filters.author) return false;
      if (search && !item.caption.toLowerCase().includes(search.toLowerCase())) return false;
      return true;
    });
  }, [items, tab, filters, search]);

  function act(msg) {
    setToast(msg);
    setTimeout(() => setToast(""), 2200);
  }

  return (
    <div className="social-workspace">
      <SocialPageHeader title="Content" description="Browse drafts, scheduled and published social content." />

      <div className="social-tabs" role="tablist" aria-label="Content status">
        {TABS.map((t) => (
          <button
            key={t}
            type="button"
            role="tab"
            aria-selected={tab === t}
            className={tab === t ? "is-active" : ""}
            onClick={() => setTab(t)}
          >
            {t}
          </button>
        ))}
      </div>

      <SocialFilterBar>
        <label className="social-search-field">
          <span className="sr-only">Search</span>
          <input
            type="search"
            placeholder="Search content…"
            value={search}
            onChange={(e) => setSearch(e.target.value)}
          />
        </label>
        <SocialSelect
          label="Platform"
          value={filters.platform}
          onChange={(v) => setFilters((f) => ({ ...f, platform: v }))}
          options={[
            { value: "all", label: "Platform" },
            { value: "instagram", label: "Instagram" },
            { value: "facebook", label: "Facebook" },
            { value: "linkedin", label: "LinkedIn" },
            { value: "tiktok", label: "TikTok" },
          ]}
        />
        <SocialSelect
          label="Product"
          value={filters.product}
          onChange={(v) => setFilters((f) => ({ ...f, product: v }))}
          options={[
            { value: "all", label: "Product" },
            { value: "mfi", label: "Microfinance" },
            { value: "fleet", label: "Fleet" },
            { value: "sms", label: "Bulk SMS" },
            { value: "crm", label: "CRM" },
          ]}
        />
        <SocialSelect
          label="Campaign"
          value={filters.campaign}
          onChange={(v) => setFilters((f) => ({ ...f, campaign: v }))}
          options={[
            { value: "all", label: "Campaign" },
            { value: "mfi-awareness", label: "MFI Awareness" },
            { value: "fleet-launch", label: "Fleet Launch" },
          ]}
        />
        <SocialSelect
          label="Author"
          value={filters.author}
          onChange={(v) => setFilters((f) => ({ ...f, author: v }))}
          options={[
            { value: "all", label: "Author" },
            { value: "James N.", label: "James N." },
            { value: "Amina K.", label: "Amina K." },
            { value: "Sarah W.", label: "Sarah W." },
          ]}
        />
        <div className="social-view-toggle" role="group" aria-label="View mode">
          <button type="button" className={view === "list" ? "is-active-toggle" : ""} onClick={() => setView("list")}>
            List
          </button>
          <button type="button" className={view === "grid" ? "is-active-toggle" : ""} onClick={() => setView("grid")}>
            Grid
          </button>
        </div>
      </SocialFilterBar>

      {loading ? (
        <SocialSkeleton variant="list" count={4} />
      ) : !filtered.length ? (
        <SocialEmptyState
          title="No content found"
          description="Try another filter or create a new post."
          actionLabel="Create post"
          to="/admin/social/create"
        />
      ) : (
        <div className={`social-content-list social-content-list--${view}`}>
          {filtered.map((item) => (
            <PostCard
              key={item.id}
              item={item}
              view={view}
              onPreview={() => act("Preview (mock)")}
              onEdit={() => act("Edit (mock)")}
              onDuplicate={() => act("Duplicated (mock)")}
              onDelete={() => act("Deleted (mock)")}
            />
          ))}
        </div>
      )}

      <SocialToast message={toast} onDismiss={() => setToast("")} />
    </div>
  );
}
