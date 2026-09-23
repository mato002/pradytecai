import React, { useEffect, useId, useState } from "react";
import ProductPoster from "./ProductPoster";
import {
  mediaThumbSrc,
  pickFeaturedMedia,
  text,
  visibleMedia,
} from "../../lib/productDetailModel";

function PlayBadge({ className = "" }) {
  return (
    <span className={`mkt-pdp-play${className ? ` ${className}` : ""}`} aria-hidden="true">
      <svg viewBox="0 0 24 24" width="22" height="22" focusable="false">
        <path fill="currentColor" d="M8 5.5v13l11-6.5L8 5.5z" />
      </svg>
    </span>
  );
}

function ProductImage({ item, productName, variant = "gallery", lazy = true }) {
  const alt = text(item.alt_text) || text(item.title) || `${productName} screenshot`;
  return <ProductPoster src={item.image_url} alt={alt} variant={variant} lazy={lazy} />;
}

/**
 * Uploaded MP4/WebM via native controls. External URLs: safe link only (no arbitrary iframe HTML).
 */
function ProductVideo({ item, productName, autoFocusPoster = false }) {
  const title = text(item.title) || `${productName} video`;
  const poster = text(item.thumbnail_url) || undefined;
  const fileUrl = text(item.video_file_url);
  const externalUrl = text(item.video_url);
  const isUpload = text(item.video_source) === "upload" || Boolean(fileUrl);

  if (isUpload && fileUrl) {
    return (
      <div className="mkt-pdp-video">
        <video
          className="mkt-pdp-video__el"
          controls
          playsInline
          preload="metadata"
          poster={poster}
          aria-label={title}
        >
          <source src={fileUrl} type={text(item.mime_type) || undefined} />
          Your browser does not support this video.
        </video>
      </div>
    );
  }

  // External: schema-ready, no unsafe embed HTML. Thumbnail + open link.
  return (
    <div className="mkt-pdp-video mkt-pdp-video--external">
      <div className="mkt-pdp-video__poster-wrap">
        {poster ? (
          <img
            src={poster}
            alt=""
            className="mkt-pdp-video__poster"
            loading={autoFocusPoster ? "eager" : "lazy"}
            decoding="async"
          />
        ) : (
          <div className="mkt-pdp-video__placeholder" role="img" aria-label={title}>
            <span className="mkt-poster__placeholder-mark" aria-hidden="true">
              P
            </span>
            <span className="mkt-poster__placeholder-label">Video</span>
          </div>
        )}
        <PlayBadge className="mkt-pdp-play--lg" />
      </div>
      {externalUrl ? (
        <a className="mkt-pdp-video__external-link" href={externalUrl} target="_blank" rel="noopener noreferrer">
          Open video
        </a>
      ) : null}
    </div>
  );
}

function MediaViewer({ item, productName }) {
  if (!item) return null;
  if (text(item.media_type) === "video") {
    return <ProductVideo item={item} productName={productName} autoFocusPoster />;
  }
  return <ProductImage item={item} productName={productName} variant="gallery" lazy={false} />;
}

function ThumbButton({ item, productName, selected, onSelect }) {
  const isVideo = text(item.media_type) === "video";
  const thumb = mediaThumbSrc(item);
  const label = text(item.title) || (isVideo ? "Video" : "Image");
  const alt = text(item.alt_text) || label;

  return (
    <button
      type="button"
      role="tab"
      aria-selected={selected}
      aria-label={label}
      className={`mkt-pdp-thumb${selected ? " is-active" : ""}${isVideo ? " mkt-pdp-thumb--video" : ""}`}
      onClick={onSelect}
    >
      {thumb ? (
        <ProductPoster src={thumb} alt={alt} variant="thumb" lazy />
      ) : (
        <div className="mkt-pdp-thumb__empty" aria-hidden="true">
          {isVideo ? <PlayBadge /> : <span>P</span>}
        </div>
      )}
      {isVideo ? <PlayBadge className="mkt-pdp-play--thumb" /> : null}
    </button>
  );
}

/**
 * Mixed image/video gallery driven entirely by ProductMedia API rows.
 * Featured (or first) item is the primary viewer; remaining items are a compact grid.
 */
export default function ProductMediaGallery({ product, title, subtitle }) {
  const items = visibleMedia(product);
  const featured = pickFeaturedMedia(items);
  const featuredKey = featured?.id ?? featured?.video_file_url ?? featured?.image_url ?? "";
  const [activeKey, setActiveKey] = useState(featuredKey);
  const labelId = useId();

  useEffect(() => {
    setActiveKey(featuredKey);
  }, [featuredKey, product?.slug]);

  if (!items.length) return null;

  const current =
    items.find(
      (item) =>
        String(item.id ?? item.video_file_url ?? item.image_url) === String(activeKey)
    ) || featured || items[0];

  const name = text(product.name) || "Product";
  const caption = text(current?.caption) || text(current?.title);

  return (
    <section className="mkt-pdp-section mkt-pdp-media" aria-labelledby={labelId}>
      <h2 id={labelId} className="mkt-pdp-section__title">
        {title || "See the product"}
      </h2>
      {subtitle ? <p className="mkt-pdp-section__intro">{subtitle}</p> : null}

      <div className="mkt-pdp-gallery">
        <div className="mkt-pdp-gallery__featured">
          <MediaViewer item={current} productName={name} />
          {caption ? <p className="mkt-pdp-gallery__caption">{caption}</p> : null}
        </div>

        {items.length > 1 ? (
          <div className="mkt-pdp-thumbs" role="tablist" aria-label="Product media">
            {items.map((item, index) => {
              const key = item.id ?? item.video_file_url ?? item.image_url ?? index;
              const selected =
                String(key) === String(current?.id ?? current?.video_file_url ?? current?.image_url);
              return (
                <ThumbButton
                  key={key}
                  item={item}
                  productName={name}
                  selected={selected}
                  onSelect={() => setActiveKey(key)}
                />
              );
            })}
          </div>
        ) : null}
      </div>
    </section>
  );
}
