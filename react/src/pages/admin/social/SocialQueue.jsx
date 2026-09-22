import React, { useEffect, useState } from "react";
import { Link } from "react-router-dom";
import {
  getMockPostingSchedule,
  getMockQueuePosts,
  getMockSocialAccounts,
} from "../../../data/socialMockData";
import SocialPlatformIcon from "../../../components/social/SocialPlatformIcon";
import { PostStatusBadge, SocialPageHeader, SocialSkeleton, SocialToast } from "../../../components/social/SocialShared";

export default function SocialQueue() {
  const accounts = getMockSocialAccounts().filter((a) => a.status === "Connected").slice(0, 4);
  const [accountId, setAccountId] = useState("acc-ig");
  const [queue, setQueue] = useState(null);
  const [schedule, setSchedule] = useState(getMockPostingSchedule());
  const [loading, setLoading] = useState(true);
  const [toast, setToast] = useState("");
  const [dragId, setDragId] = useState(null);

  useEffect(() => {
    setLoading(true);
    const t = setTimeout(() => {
      setQueue(getMockQueuePosts(accountId));
      setLoading(false);
    }, 350);
    return () => clearTimeout(t);
  }, [accountId]);

  function act(msg) {
    setToast(msg);
    setTimeout(() => setToast(""), 2200);
  }

  function addTime(day) {
    setSchedule((prev) => ({
      ...prev,
      [day]: [...(prev[day] || []), "12:00"],
    }));
    act(`Added posting time on ${day} (mock)`);
  }

  return (
    <div className="social-workspace">
      <SocialPageHeader title="Queue" description="Fill future posting slots and manage your weekly rhythm." />

      <div className="social-queue-layout">
        <aside className="admin-panel social-queue-accounts">
          <div className="admin-panel__head">
            <h2>Accounts</h2>
          </div>
          <div className="social-queue-accounts__list">
            {accounts.map((a) => (
              <button
                key={a.id}
                type="button"
                className={`social-queue-accounts__btn ${accountId === a.id ? "is-active" : ""}`}
                onClick={() => setAccountId(a.id)}
              >
                <SocialPlatformIcon platform={a.platform} size={18} />
                <span>
                  {a.platform.charAt(0).toUpperCase() + a.platform.slice(1)} {a.name}
                </span>
              </button>
            ))}
          </div>
        </aside>

        <div className="social-queue-main">
          {loading || !queue ? (
            <SocialSkeleton variant="panel" count={1} />
          ) : (
            <section className="admin-panel">
              <div className="admin-panel__head">
                <h2>Queue</h2>
                <Link to="/admin/social/create" className="btn-ghost">
                  + Add post
                </Link>
              </div>
              <div className="social-queue-days">
                {Object.entries(queue).map(([day, slots]) => (
                  <div key={day} className="social-queue-day">
                    <h3>{day}</h3>
                    <ul>
                      {slots.map((slot) => (
                        <li
                          key={slot.id}
                          className={`social-queue-slot ${slot.status === "Available" ? "is-empty" : ""} ${
                            dragId === slot.id ? "is-dragging" : ""
                          }`}
                          draggable={slot.status !== "Available"}
                          onDragStart={() => setDragId(slot.id)}
                          onDragEnd={() => {
                            setDragId(null);
                            act("Reordered (visual mock only)");
                          }}
                        >
                          <span className="social-queue-slot__time">{slot.time}</span>
                          {slot.title ? (
                            <>
                              <span className="social-queue-slot__title">{slot.title}</span>
                              <SocialPlatformIcon platform={slot.platform} size={14} />
                              <PostStatusBadge status={slot.status} />
                            </>
                          ) : (
                            <span className="social-queue-slot__empty">Available slot</span>
                          )}
                          <div className="social-queue-slot__actions">
                            {slot.title ? (
                              <>
                                <button type="button" className="btn-ghost" onClick={() => act("Edit slot (mock)")}>
                                  Edit
                                </button>
                                <button type="button" className="btn-ghost" onClick={() => act("Move slot (mock)")}>
                                  Move
                                </button>
                                <button type="button" className="btn-ghost" onClick={() => act("Removed (mock)")}>
                                  Remove
                                </button>
                              </>
                            ) : (
                              <Link to="/admin/social/create" className="btn-ghost">
                                Add post
                              </Link>
                            )}
                          </div>
                        </li>
                      ))}
                    </ul>
                  </div>
                ))}
              </div>
            </section>
          )}

          <section className="admin-panel">
            <div className="admin-panel__head">
              <h2>Posting schedule</h2>
              <button type="button" className="btn-ghost" onClick={() => act("Schedule copied (mock)")}>
                Copy schedule
              </button>
            </div>
            <div className="social-posting-schedule">
              {Object.entries(schedule).map(([day, times]) => (
                <div key={day} className="social-posting-schedule__day">
                  <strong>{day}</strong>
                  <div className="social-posting-schedule__times">
                    {times.length ? times.map((t) => <span key={t}>{t}</span>) : <span className="social-muted">—</span>}
                  </div>
                  <button type="button" className="btn-ghost" onClick={() => addTime(day)}>
                    + Add posting time
                  </button>
                </div>
              ))}
            </div>
          </section>
        </div>
      </div>

      <SocialToast message={toast} onDismiss={() => setToast("")} />
    </div>
  );
}
