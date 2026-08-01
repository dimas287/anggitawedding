import React from 'react';

export default function DesktopInvitationLayout({
    children,
    backgroundImage,
    imagePosition = 'center',
    eyebrow = 'The Wedding Of',
    groomName,
    brideName,
    dateLabel,
    venue,
    accent = '#d4af37',
    panelOverlay = 'linear-gradient(135deg, rgba(4,4,6,.28), rgba(4,4,6,.82))',
    panelBackground = '#090909',
    contentBackground = '#0d0d0d',
    contentColor = '#ffffff',
    fontFamily = 'system-ui, sans-serif',
    nameFontFamily = "'Playfair Display', Georgia, serif",
}) {
    const resolvedGroom = groomName || 'Mempelai Pria';
    const resolvedBride = brideName || 'Mempelai Wanita';

    return (
        <div
            className="aw-invitation-layout"
            style={{
                '--aw-accent': accent,
                '--aw-panel-background': panelBackground,
                '--aw-content-background': contentBackground,
                '--aw-content-color': contentColor,
                '--aw-font-family': fontFamily,
                '--aw-name-font-family': nameFontFamily,
            }}
        >
            <style>{`
                .aw-invitation-layout {
                    min-height: 100vh;
                    width: 100%;
                    background: var(--aw-panel-background);
                    color: var(--aw-content-color);
                    font-family: var(--aw-font-family);
                }

                .aw-invitation-stage {
                    display: none;
                }

                .aw-invitation-canvas {
                    position: relative;
                    z-index: 1;
                    width: 100%;
                    min-width: 0;
                    min-height: 100vh;
                    overflow: hidden;
                    background: var(--aw-content-background);
                }

                @media (min-width: 768px) {
                    .aw-invitation-layout {
                        display: grid;
                        grid-template-columns: minmax(0, 1fr) clamp(390px, 39vw, 540px);
                        align-items: start;
                    }

                    .aw-invitation-stage {
                        position: sticky;
                        top: 0;
                        display: flex;
                        height: 100vh;
                        min-width: 0;
                        align-items: flex-end;
                        overflow: hidden;
                        isolation: isolate;
                    }

                    .aw-invitation-stage__image,
                    .aw-invitation-stage__overlay {
                        position: absolute;
                        inset: 0;
                    }

                    .aw-invitation-stage__image {
                        z-index: -2;
                        background-color: var(--aw-panel-background);
                        background-repeat: no-repeat;
                        background-size: cover;
                        transform: scale(1.015);
                        transition: background-image .6s ease, background-position .6s ease;
                    }

                    .aw-invitation-stage__overlay {
                        z-index: -1;
                    }

                    .aw-invitation-stage__content {
                        width: min(720px, 86%);
                        padding: clamp(38px, 5vw, 88px);
                    }

                    .aw-invitation-stage__eyebrow {
                        margin: 0 0 18px;
                        color: var(--aw-accent);
                        font-size: 11px;
                        font-weight: 700;
                        letter-spacing: .34em;
                        text-transform: uppercase;
                    }

                    .aw-invitation-stage__names {
                        margin: 0;
                        max-width: 680px;
                        color: #fff;
                        font-family: var(--aw-name-font-family);
                        font-size: clamp(44px, 5.3vw, 88px);
                        font-weight: 400;
                        letter-spacing: -.035em;
                        line-height: .96;
                        text-wrap: balance;
                        text-shadow: 0 8px 32px rgba(0,0,0,.38);
                    }

                    .aw-invitation-stage__ampersand {
                        color: var(--aw-accent);
                        font-size: .68em;
                        font-style: italic;
                    }

                    .aw-invitation-stage__details {
                        display: flex;
                        flex-wrap: wrap;
                        gap: 9px 20px;
                        margin-top: 24px;
                        color: rgba(255,255,255,.76);
                        font-size: 12px;
                        letter-spacing: .13em;
                        text-transform: uppercase;
                    }

                    .aw-invitation-stage__brand {
                        position: absolute;
                        top: clamp(28px, 4vw, 52px);
                        left: clamp(32px, 5vw, 88px);
                        color: rgba(255,255,255,.72);
                        font-size: 10px;
                        font-weight: 700;
                        letter-spacing: .3em;
                        text-transform: uppercase;
                    }

                    .aw-invitation-canvas {
                        width: 100%;
                        max-width: 540px;
                        box-shadow: -24px 0 70px rgba(0,0,0,.28);
                    }
                }

                @media (min-width: 1500px) {
                    .aw-invitation-layout {
                        grid-template-columns: minmax(0, 1fr) clamp(430px, 30vw, 560px);
                    }

                    .aw-invitation-canvas {
                        max-width: 560px;
                    }
                }

                @media (prefers-reduced-motion: reduce) {
                    .aw-invitation-stage__image {
                        transition: none;
                    }
                }
            `}</style>

            <aside className="aw-invitation-stage" aria-label={`Undangan ${resolvedGroom} dan ${resolvedBride}`}>
                <div
                    className="aw-invitation-stage__image"
                    role="img"
                    aria-label={`Foto prewedding ${resolvedGroom} dan ${resolvedBride}`}
                    style={{
                        backgroundImage: backgroundImage ? `url(${backgroundImage})` : 'none',
                        backgroundPosition: imagePosition,
                    }}
                />
                <div className="aw-invitation-stage__overlay" style={{ background: panelOverlay }} />
                <div className="aw-invitation-stage__brand">Anggita Wedding Organizer</div>
                <div className="aw-invitation-stage__content">
                    <p className="aw-invitation-stage__eyebrow">{eyebrow}</p>
                    <h1 className="aw-invitation-stage__names">
                        {resolvedGroom}
                        <br />
                        <span className="aw-invitation-stage__ampersand">&amp;</span> {resolvedBride}
                    </h1>
                    {(dateLabel || venue) && (
                        <div className="aw-invitation-stage__details">
                            {dateLabel && <span>{dateLabel}</span>}
                            {venue && <span>{venue}</span>}
                        </div>
                    )}
                </div>
            </aside>

            <main className="aw-invitation-canvas">
                {children}
            </main>
        </div>
    );
}
