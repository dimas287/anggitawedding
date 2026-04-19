import React, { useEffect, useRef, useState } from 'react';

export default function LazyImage({
    src,
    alt = '',
    className = '',
    style = {},
    imgClassName = '',
    imgStyle = {},
    placeholderColor = 'rgba(255,255,255,0.08)',
    ...rest
}) {
    const wrapperRef = useRef(null);
    const [visible, setVisible] = useState(false);
    const [loaded, setLoaded] = useState(false);

    useEffect(() => {
        const node = wrapperRef.current;
        if (!node) return;

        if ('IntersectionObserver' in window) {
            const observer = new IntersectionObserver(
                (entries) => {
                    entries.forEach((entry) => {
                        if (entry.isIntersecting) {
                            setVisible(true);
                            observer.disconnect();
                        }
                    });
                },
                { rootMargin: '120px 0px' }
            );

            observer.observe(node);
            return () => observer.disconnect();
        }

        setVisible(true);
    }, []);

    return (
        <div
            ref={wrapperRef}
            className={`lazy-image-wrapper ${className}`.trim()}
            style={{
                position: 'relative',
                overflow: 'hidden',
                background: placeholderColor,
                ...style,
            }}
        >
            {visible && (
                <img
                    src={src}
                    alt={alt}
                    loading="lazy"
                    onLoad={() => setLoaded(true)}
                    className={`lazy-image ${imgClassName}`.trim()}
                    style={{
                        width: '100%',
                        height: '100%',
                        display: 'block',
                        objectFit: 'cover',
                        transition: 'opacity 0.6s ease',
                        opacity: loaded ? 1 : 0,
                        ...imgStyle,
                    }}
                    {...rest}
                />
            )}
            {!loaded && (
                <div
                    className="lazy-image-placeholder"
                    style={{
                        position: 'absolute',
                        inset: 0,
                        background: placeholderColor,
                        transition: 'opacity 0.4s ease',
                        opacity: visible ? 1 : 0.8,
                    }}
                />
            )}
        </div>
    );
}
