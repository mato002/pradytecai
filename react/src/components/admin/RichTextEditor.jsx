import React, { useRef, useState } from "react";
import RichContent from "../common/RichContent";

export default function RichTextEditor({
  value = "",
  onChange,
  siteUrl = "",
  disabled = false,
  placeholder = "Describe product capabilities, benefits, and key features...",
  rows = 7,
}) {
  const [mode, setMode] = useState("write"); // 'write' | 'preview'
  const textareaRef = useRef(null);

  function insertFormatting(prefix, suffix = "", defaultText = "") {
    if (disabled || !textareaRef.current) return;
    const el = textareaRef.current;
    const start = el.selectionStart;
    const end = el.selectionEnd;
    const text = el.value;
    const selected = text.substring(start, end) || defaultText;

    const replacement = `${prefix}${selected}${suffix}`;
    const newText = text.substring(0, start) + replacement + text.substring(end);

    onChange(newText);

    setTimeout(() => {
      el.focus();
      const cursorTarget = start + prefix.length + selected.length;
      el.setSelectionRange(cursorTarget, cursorTarget);
    }, 0);
  }

  function insertList(isNumbered = false) {
    if (disabled || !textareaRef.current) return;
    const el = textareaRef.current;
    const start = el.selectionStart;
    const end = el.selectionEnd;
    const text = el.value;
    const selected = text.substring(start, end);

    let replacement = "";
    if (selected) {
      const lines = selected.split("\n");
      replacement = lines
        .map((l, idx) => (isNumbered ? `${idx + 1}. ${l}` : `- ${l}`))
        .join("\n");
    } else {
      replacement = isNumbered
        ? "\n1. Step or feature one\n2. Step or feature two\n3. Step or feature three\n"
        : "\n- Key feature or bullet point\n- Second benefit or item\n- Third item\n";
    }

    const newText = text.substring(0, start) + replacement + text.substring(end);
    onChange(newText);

    setTimeout(() => {
      el.focus();
      const cursorTarget = start + replacement.length;
      el.setSelectionRange(cursorTarget, cursorTarget);
    }, 0);
  }

  function insertSiteLink() {
    if (disabled || !textareaRef.current) return;
    const el = textareaRef.current;
    const start = el.selectionStart;
    const end = el.selectionEnd;
    const text = el.value;
    const selected = text.substring(start, end);

    const targetUrl = (siteUrl || "").trim() || "https://";
    const label = selected || "Visit Website";
    const replacement = `[${label}](${targetUrl})`;

    const newText = text.substring(0, start) + replacement + text.substring(end);
    onChange(newText);

    setTimeout(() => {
      el.focus();
      const cursorTarget = start + replacement.length;
      el.setSelectionRange(cursorTarget, cursorTarget);
    }, 0);
  }

  return (
    <div className="admin-rich-editor">
      <div className="admin-rich-editor__header">
        <div className="admin-rich-editor__toolbar" role="toolbar" aria-label="Formatting tools">
          <button
            type="button"
            className="admin-rich-editor__tool"
            title="Subtitle (H2)"
            onClick={() => insertFormatting("\n## ", "\n", "Section Subtitle")}
            disabled={disabled}
          >
            <span>Subtitle</span>
          </button>

          <button
            type="button"
            className="admin-rich-editor__tool"
            title="Heading 3"
            onClick={() => insertFormatting("\n### ", "\n", "Feature Heading")}
            disabled={disabled}
          >
            <strong>H3</strong>
          </button>

          <button
            type="button"
            className="admin-rich-editor__tool"
            title="Heading 4"
            onClick={() => insertFormatting("\n#### ", "\n", "Minor Heading")}
            disabled={disabled}
          >
            <span style={{ fontSize: "0.85em", fontWeight: 700 }}>H4</span>
          </button>

          <span className="admin-rich-editor__divider" />

          <button
            type="button"
            className="admin-rich-editor__tool"
            title="Bullet list"
            onClick={() => insertList(false)}
            disabled={disabled}
          >
            <span>• List</span>
          </button>

          <button
            type="button"
            className="admin-rich-editor__tool"
            title="Numbered list"
            onClick={() => insertList(true)}
            disabled={disabled}
          >
            <span>1. List</span>
          </button>

          <span className="admin-rich-editor__divider" />

          <button
            type="button"
            className="admin-rich-editor__tool"
            title="Bold"
            onClick={() => insertFormatting("**", "**", "bold text")}
            disabled={disabled}
          >
            <b>B</b>
          </button>

          <button
            type="button"
            className="admin-rich-editor__tool"
            title="Italic"
            onClick={() => insertFormatting("*", "*", "italic text")}
            disabled={disabled}
          >
            <i>I</i>
          </button>

          <button
            type="button"
            className="admin-rich-editor__tool"
            title="Quote / Callout"
            onClick={() => insertFormatting("\n> ", "\n", "Important callout note")}
            disabled={disabled}
          >
            <span>❝ Quote</span>
          </button>

          <span className="admin-rich-editor__divider" />

          <button
            type="button"
            className="admin-rich-editor__tool admin-rich-editor__tool--accent"
            title={siteUrl ? `Insert link to: ${siteUrl}` : "Insert site link"}
            onClick={insertSiteLink}
            disabled={disabled}
          >
            <span>🔗 Link to Site ↗</span>
          </button>
        </div>

        <div className="admin-rich-editor__tabs">
          <button
            type="button"
            className={`admin-rich-editor__tab ${mode === "write" ? "is-active" : ""}`}
            onClick={() => setMode("write")}
          >
            Write
          </button>
          <button
            type="button"
            className={`admin-rich-editor__tab ${mode === "preview" ? "is-active" : ""}`}
            onClick={() => setMode("preview")}
          >
            Live Preview
          </button>
        </div>
      </div>

      {mode === "write" ? (
        <div className="admin-rich-editor__body">
          <textarea
            ref={textareaRef}
            rows={rows}
            placeholder={placeholder}
            value={value}
            onChange={(e) => onChange(e.target.value)}
            disabled={disabled}
            className="admin-rich-editor__textarea"
          />
          <div className="admin-rich-editor__hint">
            <span>
              Supports: <b>## Subtitle</b>, <b>### H3</b>, <b>#### H4</b>, <b>- Bullet list</b>, <b>1. Numbered list</b>, <b>[Link](url)</b>, and HTML tags.
            </span>
            {siteUrl && (
              <span className="admin-rich-editor__site-badge">
                Site link configured: <a href={siteUrl} target="_blank" rel="noreferrer">{siteUrl}</a>
              </span>
            )}
          </div>
        </div>
      ) : (
        <div className="admin-rich-editor__preview">
          {value.trim() ? (
            <RichContent content={value} />
          ) : (
            <p className="admin-rich-editor__empty">Nothing to preview yet. Switch back to Write mode to type description.</p>
          )}
        </div>
      )}
    </div>
  );
}
