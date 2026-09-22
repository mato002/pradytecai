import React, { useEffect, useState } from "react";
import {
  getMockAnalyticsMetrics,
  getMockAnalyticsSeries,
  getMockPerformanceByCampaign,
  getMockPerformanceByPlatform,
  getMockPerformanceByProduct,
  getMockTopPosts,
} from "../../../data/socialMockData";
import SocialPlatformIcon from "../../../components/social/SocialPlatformIcon";
import {
  AnalyticsMetricCard,
  MiniSparkline,
  PercentBars,
  SocialFilterBar,
  SocialPageHeader,
  SocialSelect,
  SocialSkeleton,
} from "../../../components/social/SocialShared";

export default function SocialAnalytics() {
  const [loading, setLoading] = useState(true);
  const [range, setRange] = useState("30");
  const [filters, setFilters] = useState({ platform: "all", account: "all", product: "all" });

  useEffect(() => {
    setLoading(true);
    const t = setTimeout(() => setLoading(false), 450);
    return () => clearTimeout(t);
  }, [range, filters]);

  const metrics = getMockAnalyticsMetrics();
  const series = getMockAnalyticsSeries();
  const byPlatform = getMockPerformanceByPlatform();
  const byProduct = getMockPerformanceByProduct();
  const byCampaign = getMockPerformanceByCampaign();
  const topPosts = getMockTopPosts();

  return (
    <div className="social-workspace">
      <SocialPageHeader
        title="Analytics"
        description="Performance across Pradytec social channels, products and campaigns."
      />

      <div className="social-tabs social-range-tabs" role="tablist" aria-label="Date range">
        {[
          ["7", "Last 7 days"],
          ["30", "Last 30 days"],
          ["90", "Last 90 days"],
          ["custom", "Custom"],
        ].map(([id, label]) => (
          <button
            key={id}
            type="button"
            role="tab"
            aria-selected={range === id}
            className={range === id ? "is-active" : ""}
            onClick={() => setRange(id)}
          >
            {label}
          </button>
        ))}
      </div>

      <SocialFilterBar>
        <SocialSelect
          label="All platforms"
          value={filters.platform}
          onChange={(v) => setFilters((f) => ({ ...f, platform: v }))}
          options={[
            { value: "all", label: "All platforms" },
            { value: "instagram", label: "Instagram" },
            { value: "linkedin", label: "LinkedIn" },
            { value: "facebook", label: "Facebook" },
          ]}
        />
        <SocialSelect
          label="All accounts"
          value={filters.account}
          onChange={(v) => setFilters((f) => ({ ...f, account: v }))}
          options={[
            { value: "all", label: "All accounts" },
            { value: "acc-ig", label: "@pradytec" },
            { value: "acc-li", label: "Prady Technologies" },
          ]}
        />
        <SocialSelect
          label="All products"
          value={filters.product}
          onChange={(v) => setFilters((f) => ({ ...f, product: v }))}
          options={[
            { value: "all", label: "All products" },
            { value: "mfi", label: "Microfinance" },
            { value: "fleet", label: "Fleet" },
          ]}
        />
      </SocialFilterBar>

      {loading ? (
        <>
          <SocialSkeleton variant="kpi" count={6} />
          <SocialSkeleton variant="panel" count={2} />
        </>
      ) : (
        <>
          <div className="admin-kpi-grid">
            {metrics.map((m) => (
              <AnalyticsMetricCard key={m.id} label={m.label} value={m.value} delta={m.delta} />
            ))}
          </div>

          <div className="social-analytics-grid">
            <section className="admin-panel">
              <div className="admin-panel__head">
                <h2>Performance over time</h2>
              </div>
              <div className="social-perf-panel">
                <div className="social-chart-legend">
                  <span>
                    <i style={{ background: "#004d40" }} /> Followers / Reach
                  </span>
                  <span>
                    <i style={{ background: "#c9a227" }} /> Engagement / Clicks
                  </span>
                </div>
                <MiniSparkline series={series} keys={["reach", "engagement"]} />
                <MiniSparkline series={series} keys={["followers", "clicks"]} />
              </div>
            </section>

            <section className="admin-panel">
              <div className="admin-panel__head">
                <h2>Performance by platform</h2>
              </div>
              <div className="p-4">
                <PercentBars
                  items={byPlatform}
                  renderIcon={(item) => <SocialPlatformIcon platform={item.platform} size={14} />}
                />
              </div>
            </section>

            <section className="admin-panel">
              <div className="admin-panel__head">
                <h2>Follower growth</h2>
              </div>
              <div className="social-perf-panel">
                <MiniSparkline series={series} keys={["followers"]} />
                <p className="social-muted" style={{ marginTop: "0.75rem" }}>
                  Mock series — replace with live analytics later.
                </p>
              </div>
            </section>

            <section className="admin-panel">
              <div className="admin-panel__head">
                <h2>Performance by product</h2>
              </div>
              <div className="p-4">
                <PercentBars items={byProduct} />
              </div>
            </section>

            <section className="admin-panel">
              <div className="admin-panel__head">
                <h2>Performance by campaign</h2>
              </div>
              <div className="p-4">
                <PercentBars items={byCampaign} />
              </div>
            </section>
          </div>

          <section className="admin-panel">
            <div className="admin-panel__head">
              <h2>Top performing posts</h2>
            </div>
            <div className="social-top-posts">
              {topPosts.map((post) => (
                <article key={post.id} className="social-top-post">
                  <div className="social-top-post__head">
                    <SocialPlatformIcon platform={post.platform} size={16} />
                    <strong>{post.product}</strong>
                  </div>
                  <p>&ldquo;{post.caption}&rdquo;</p>
                  <dl>
                    <div>
                      <dt>Reach</dt>
                      <dd>{post.reach}</dd>
                    </div>
                    <div>
                      <dt>Engagement</dt>
                      <dd>{post.engagement}</dd>
                    </div>
                    <div>
                      <dt>Clicks</dt>
                      <dd>{post.clicks}</dd>
                    </div>
                  </dl>
                </article>
              ))}
            </div>
          </section>
        </>
      )}
    </div>
  );
}
