import React from "react";

/**
 * Shared product poster renderer.
 * Preserves natural aspect ratio (object-fit: contain). Never stretch/crop poster art.
 *
 * Preferred upload ratio: 4:3 — other ratios are supported without distortion.
 *
 * @param {"card"|"featured"|"detail"} variant
 */
export default function ProductPoster({
  src,
  alt = "Product poster",
  variant = "card",
  lazy = true,
  className = "",
}) {
  const [failed, setFailed] = React.useState(false);
  React.useEffect(() => {
    setFailed(false);
  }, [src]);

  const frameClass = ["mkt-poster", `mkt-poster--${variant}`, className].filter(Boolean).join(" ");

  if (!src || failed) {
    return (
      <div className={frameClass} role="img" aria-label={alt}>
        <div className="mkt-poster__placeholder">
          <span className="mkt-poster__placeholder-mark" aria-hidden="true">
            P
          </span>
          <span className="mkt-poster__placeholder-label">Prady</span>
        </div>
      </div>
    );
  }

  return (
    <div className={frameClass}>
      <img
        src={src}
        alt={alt}
        className="mkt-poster__img"
        loading={lazy ? "lazy" : "eager"}
        decoding="async"
        width={800}
        height={600}
        onError={() => setFailed(true)}
      />
    </div>
  );
}
