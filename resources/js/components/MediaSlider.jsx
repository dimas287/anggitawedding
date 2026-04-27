import React, { useState, useEffect, useCallback, useRef } from 'react';

export default function MediaSlider({ media, title, caption, onClose }) {
  const [current, setCurrent] = useState(0);
  const [closing, setClosing] = useState(false);
  const backdropRef = useRef(null);

  const total = media.length;

  const next = useCallback(() => {
    setCurrent(prev => (prev + 1) % total);
  }, [total]);

  const prev = useCallback(() => {
    setCurrent(prev => (prev - 1 + total) % total);
  }, [total]);

  const handleClose = useCallback(() => {
    setClosing(true);
    setTimeout(() => onClose(), 350);
  }, [onClose]);

  useEffect(() => {
    const onKey = (e) => {
      if (e.key === 'Escape') handleClose();
      if (e.key === 'ArrowRight') next();
      if (e.key === 'ArrowLeft') prev();
    };
    window.addEventListener('keydown', onKey);
    document.body.style.overflow = 'hidden';
    return () => {
      window.removeEventListener('keydown', onKey);
      document.body.style.overflow = '';
    };
  }, [handleClose, next, prev]);

  const item = media[current];

  const renderMedia = (slide) => {
    if (slide.type === 'video') {
      if (slide.embed) {
        let src = slide.embed;
        if (src.includes('youtube.com') || src.includes('youtu.be')) {
          src += src.includes('?') ? '&autoplay=1' : '?autoplay=1';
        }
        return (
          <iframe
            src={src}
            className="slider-media"
            frameBorder="0"
            allow="autoplay; fullscreen"
            allowFullScreen
          />
        );
      }
      return (
        <video
          src={slide.src}
          className="slider-media"
          autoPlay
          controls
          loop
          playsInline
        />
      );
    }
    return <img src={slide.src} className="slider-media" alt={title || 'Galeri'} draggable={false} />;
  };

  return (
    <div
      ref={backdropRef}
      className={`slider-overlay ${closing ? 'slider-closing' : 'slider-opening'}`}
      onClick={(e) => { if (e.target === backdropRef.current) handleClose(); }}
    >
      <div className="slider-container">
        {/* Close Button */}
        <button className="slider-close" onClick={handleClose} aria-label="Tutup">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
            <path d="M18 6L6 18M6 6l12 12" />
          </svg>
        </button>

        {/* Main Media */}
        <div className="slider-stage">
          {renderMedia(item)}
        </div>

        {/* Navigation Arrows */}
        {total > 1 && (
          <>
            <button className="slider-arrow slider-arrow--left" onClick={prev} aria-label="Sebelumnya">
              <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                <path d="M15 18l-6-6 6-6" />
              </svg>
            </button>
            <button className="slider-arrow slider-arrow--right" onClick={next} aria-label="Berikutnya">
              <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                <path d="M9 18l6-6-6-6" />
              </svg>
            </button>
          </>
        )}

        {/* Info Bar */}
        <div className="slider-info">
          <div className="slider-info__text">
            {title && <h3 className="slider-info__title">{title}</h3>}
            {caption && <p className="slider-info__caption">{caption}</p>}
          </div>
          {total > 1 && (
            <div className="slider-info__counter">
              {current + 1} / {total}
            </div>
          )}
        </div>

        {/* Thumbnail Strip */}
        {total > 1 && (
          <div className="slider-thumbs">
            {media.map((m, i) => (
              <button
                key={i}
                className={`slider-thumb ${i === current ? 'slider-thumb--active' : ''}`}
                onClick={() => setCurrent(i)}
              >
                {m.type === 'video' ? (
                  <div className="slider-thumb__video">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                      <path d="M8 5v14l11-7z" />
                    </svg>
                  </div>
                ) : (
                  <img src={m.src} alt="" draggable={false} />
                )}
              </button>
            ))}
          </div>
        )}
      </div>
    </div>
  );
}
