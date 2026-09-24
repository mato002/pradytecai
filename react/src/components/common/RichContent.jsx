import React from "react";

/**
 * Safely parses and renders text containing Markdown-style formatting or HTML-style tags:
 * - Subtitles: `## Subtitle`
 * - Heading 3: `### Heading` or `<h3>Heading</h3>`
 * - Heading 4: `#### Heading` or `<h4>Heading</h4>`
 * - Bulleted lists: `- item`, `* item`, `• item` or `<ul><li>...</li></ul>`
 * - Numbered lists: `1. item`, `2. item` or `<ol><li>...</li></ol>`
 * - Links: `[Text](url)` or `<a href="url">Text</a>`
 * - Bold / Italic: `**bold**`, `*italic*`
 * - Blockquotes: `> quote`
 * - Paragraph breaks
 */

function parseInlineFormatting(str) {
  if (!str) return [];
  // Tokenize links [text](url) and <a href="...">text</a> and bold/italic
  const tokens = [];
  let remaining = str;

  // Pattern matching links [label](url), HTML links <a href="url">label</a>, bold **text**, italic *text*
  const pattern = /\[([^\]]+)\]\(([^)]+)\)|<a\s+(?:[^>]*?\s+)?href=["']([^"']*)["'][^>]*>(.*?)<\/a>|\*\*([^*]+)\*\*|\*([^*]+)\*/i;

  let keyIndex = 0;
  while (remaining) {
    const match = remaining.match(pattern);
    if (!match) {
      tokens.push(remaining);
      break;
    }

    const matchIndex = match.index;
    if (matchIndex > 0) {
      tokens.push(remaining.substring(0, matchIndex));
    }

    if (match[1] && match[2]) {
      // Markdown link: [label](url)
      const label = match[1];
      const href = match[2].trim();
      const isExternal = href.startsWith("http://") || href.startsWith("https://") || href.startsWith("//");
      tokens.push(
        <a
          key={`link-${keyIndex++}`}
          href={href}
          target={isExternal ? "_blank" : undefined}
          rel={isExternal ? "noopener noreferrer" : undefined}
          className="mkt-rich-link"
        >
          {label}
          {isExternal && <span className="mkt-rich-link__icon" aria-hidden="true"> ↗</span>}
        </a>
      );
    } else if (match[3] !== undefined && match[4] !== undefined) {
      // HTML link: <a href="...">...</a>
      const href = match[3].trim();
      const label = match[4];
      const isExternal = href.startsWith("http://") || href.startsWith("https://") || href.startsWith("//");
      tokens.push(
        <a
          key={`link-${keyIndex++}`}
          href={href}
          target={isExternal ? "_blank" : undefined}
          rel={isExternal ? "noopener noreferrer" : undefined}
          className="mkt-rich-link"
        >
          {label}
          {isExternal && <span className="mkt-rich-link__icon" aria-hidden="true"> ↗</span>}
        </a>
      );
    } else if (match[5]) {
      // Bold: **text**
      tokens.push(<strong key={`b-${keyIndex++}`}>{match[5]}</strong>);
    } else if (match[6]) {
      // Italic: *text*
      tokens.push(<em key={`i-${keyIndex++}`}>{match[6]}</em>);
    }

    remaining = remaining.substring(matchIndex + match[0].length);
  }

  return tokens;
}

export default function RichContent({ content, className = "" }) {
  if (!content || typeof content !== "string") return null;

  // Split into lines while normalizing line endings
  const rawLines = content.replace(/\r\n/g, "\n").replace(/\r/g, "\n").split("\n");
  const blocks = [];

  let currentList = null; // { type: 'ul' | 'ol', items: [] }
  let currentBlockquote = null; // { lines: [] }

  function flushList() {
    if (currentList) {
      if (currentList.type === "ol") {
        blocks.push({
          type: "ol",
          items: currentList.items,
        });
      } else {
        blocks.push({
          type: "ul",
          items: currentList.items,
        });
      }
      currentList = null;
    }
  }

  function flushBlockquote() {
    if (currentBlockquote) {
      blocks.push({
        type: "quote",
        text: currentBlockquote.lines.join(" "),
      });
      currentBlockquote = null;
    }
  }

  for (let i = 0; i < rawLines.length; i++) {
    const line = rawLines[i].trim();

    if (!line) {
      flushList();
      flushBlockquote();
      continue;
    }

    // Check for Heading 4 (#### or <h4>)
    const h4Match = line.match(/^(?:####\s+|<h4[^>]*>)(.*?)(?:<\/h4>)?$/i);
    if (h4Match) {
      flushList();
      flushBlockquote();
      blocks.push({ type: "h4", text: h4Match[1].trim() });
      continue;
    }

    // Check for Heading 3 (### or <h3>)
    const h3Match = line.match(/^(?:###\s+|<h3[^>]*>)(.*?)(?:<\/h3>)?$/i);
    if (h3Match) {
      flushList();
      flushBlockquote();
      blocks.push({ type: "h3", text: h3Match[1].trim() });
      continue;
    }

    // Check for Subtitle / Heading 2 (## or <h2>)
    const subMatch = line.match(/^(?:##\s+|<h2[^>]*>)(.*?)(?:<\/h2>)?$/i);
    if (subMatch) {
      flushList();
      flushBlockquote();
      blocks.push({ type: "subtitle", text: subMatch[1].trim() });
      continue;
    }

    // Check for Blockquote (> or <blockquote>)
    const quoteMatch = line.match(/^(?:>\s*|<blockquote[^>]*>)(.*?)(?:<\/blockquote>)?$/i);
    if (quoteMatch) {
      flushList();
      if (!currentBlockquote) {
        currentBlockquote = { lines: [quoteMatch[1].trim()] };
      } else {
        currentBlockquote.lines.push(quoteMatch[1].trim());
      }
      continue;
    }
    flushBlockquote();

    // Check for Ordered list item (e.g. "1. Item" or "<li>")
    const olMatch = line.match(/^(\d+)[\.\)]\s+(.*)$/);
    if (olMatch) {
      if (!currentList || currentList.type !== "ol") {
        flushList();
        currentList = { type: "ol", items: [] };
      }
      currentList.items.push(olMatch[2].trim());
      continue;
    }

    // Check for Unordered list item (e.g. "- Item", "* Item", "• Item")
    const ulMatch = line.match(/^[-*•]\s+(.*)$/);
    if (ulMatch) {
      if (!currentList || currentList.type !== "ul") {
        flushList();
        currentList = { type: "ul", items: [] };
      }
      currentList.items.push(ulMatch[1].trim());
      continue;
    }

    // Check for HTML <li> tags inside plain text
    const liMatch = line.match(/^<li[^>]*>(.*?)<\/li>$/i);
    if (liMatch) {
      if (!currentList) {
        currentList = { type: "ul", items: [] };
      }
      currentList.items.push(liMatch[1].trim());
      continue;
    }

    // Normal paragraph line
    flushList();
    blocks.push({ type: "p", text: line });
  }

  flushList();
  flushBlockquote();

  return (
    <div className={`mkt-rich-content ${className}`}>
      {blocks.map((block, idx) => {
        switch (block.type) {
          case "subtitle":
            return (
              <h3 key={idx} className="mkt-rich-subtitle">
                {parseInlineFormatting(block.text)}
              </h3>
            );
          case "h3":
            return (
              <h3 key={idx} className="mkt-rich-h3">
                {parseInlineFormatting(block.text)}
              </h3>
            );
          case "h4":
            return (
              <h4 key={idx} className="mkt-rich-h4">
                {parseInlineFormatting(block.text)}
              </h4>
            );
          case "ul":
            return (
              <ul key={idx} className="mkt-rich-ul">
                {block.items.map((item, itemIdx) => (
                  <li key={itemIdx} className="mkt-rich-li">
                    {parseInlineFormatting(item)}
                  </li>
                ))}
              </ul>
            );
          case "ol":
            return (
              <ol key={idx} className="mkt-rich-ol">
                {block.items.map((item, itemIdx) => (
                  <li key={itemIdx} className="mkt-rich-li">
                    {parseInlineFormatting(item)}
                  </li>
                ))}
              </ol>
            );
          case "quote":
            return (
              <blockquote key={idx} className="mkt-rich-quote">
                {parseInlineFormatting(block.text)}
              </blockquote>
            );
          case "p":
          default:
            return (
              <p key={idx} className="mkt-rich-p">
                {parseInlineFormatting(block.text)}
              </p>
            );
        }
      })}
    </div>
  );
}
