import React, { useState, useEffect } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import { palette, glassCard, getEventTarget, generateGoogleCalendarUrl, generateICS } from './utils.js';
import { SectionTitle, ActionButton, FadeUp } from './components.jsx';

// ─── COUNTDOWN TIMER ──────────────────────────────────────────
export function CountdownTimer({ targetDate, title, location }) {
    const target = getEventTarget(targetDate);
    const [remaining, setRemaining] = useState(() => getRemaining(target));

    function getRemaining(t) {
        if (!t) return null;
        const diff = t - Date.now();
        if (diff <= 0) return { done: true };
        return {
            days: Math.floor(diff / 86400000),
            hours: Math.floor((diff % 86400000) / 3600000),
            minutes: Math.floor((diff % 3600000) / 60000),
            seconds: Math.floor((diff % 60000) / 1000),
        };
    }

    useEffect(() => {
        if (!target) return;
        const id = setInterval(() => setRemaining(getRemaining(target)), 1000);
        return () => clearInterval(id);
    }, [target]);

    if (!remaining) return null;

    const units = remaining.done ? [] : [
        { label: 'Hari', value: remaining.days },
        { label: 'Jam', value: remaining.hours },
        { label: 'Menit', value: remaining.minutes },
        { label: 'Detik', value: remaining.seconds },
    ];

    const handleGoogleCalendar = () => {
        if (!target) return;
        window.open(generateGoogleCalendarUrl({
            title: title || 'Pernikahan',
            start: target,
            location: location || '',
            description: 'Hadir dengan penuh cinta',
        }), '_blank');
    };

    const handleDownloadICS = () => {
        if (!target) return;
        const ics = generateICS({
            title: title || 'Pernikahan',
            start: target,
            location: location || '',
            description: 'Hadir dengan penuh cinta',
        });
        const blob = new Blob([ics], { type: 'text/calendar' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url; a.download = 'undangan.ics'; a.click();
        URL.revokeObjectURL(url);
    };

    return (
        <FadeUp>
            <div style={{ ...glassCard, marginTop: 32 }}>
                <SectionTitle subtitle="Save The Date" title="Hitung Mundur" />
                {remaining.done ? (
                    <motion.p
                        animate={{ scale: [1, 1.04, 1] }}
                        transition={{ duration: 2, repeat: Infinity }}
                        style={{ textAlign: 'center', fontSize: 22, fontWeight: 800, color: palette.neon }}
                    >
                        🎊 Acara sedang berlangsung!
                    </motion.p>
                ) : (
                    <div style={{ display: 'grid', gridTemplateColumns: 'repeat(4, 1fr)', gap: 12 }}>
                        {units.map(({ label, value }) => (
                            <motion.div
                                key={label}
                                animate={{ scale: label === 'Detik' ? [1, 1.06, 1] : 1 }}
                                transition={{ duration: 1, repeat: Infinity }}
                                style={{
                                    textAlign: 'center', borderRadius: 20,
                                    padding: '20px 8px',
                                    background: 'rgba(255,255,255,0.04)',
                                    border: '1px solid rgba(255,255,255,0.1)',
                                    boxShadow: label === 'Detik' ? `0 0 18px rgba(255,113,200,0.2)` : 'none',
                                }}
                            >
                                <div style={{ fontSize: 36, fontWeight: 900, color: palette.neon, lineHeight: 1 }}>
                                    {String(value).padStart(2, '0')}
                                </div>
                                <div style={{ marginTop: 8, fontSize: 11, letterSpacing: 3, textTransform: 'uppercase', color: palette.muted }}>
                                    {label}
                                </div>
                            </motion.div>
                        ))}
                    </div>
                )}

                {/* Add to Calendar */}
                <div style={{ marginTop: 28, display: 'flex', flexDirection: 'column', gap: 12 }}>
                    <p style={{ textAlign: 'center', color: palette.muted, fontSize: 13, letterSpacing: 2, textTransform: 'uppercase' }}>
                        Simpan ke Kalender
                    </p>
                    <div style={{ display: 'flex', gap: 12, flexWrap: 'wrap' }}>
                        <ActionButton onClick={handleGoogleCalendar} style={{ flex: 1, minWidth: 140 }}>
                            📅 Google Calendar
                        </ActionButton>
                        <ActionButton onClick={handleDownloadICS} variant="secondary" style={{ flex: 1, minWidth: 140 }}>
                            ⬇️ Download .ics
                        </ActionButton>
                    </div>
                </div>
            </div>
        </FadeUp>
    );
}

// ─── LOVE STORY TIMELINE ─────────────────────────────────────
export function LoveStory({ stories }) {
    if (!stories || !stories.length) return null;
    return (
        <FadeUp>
            <div style={{ ...glassCard, marginTop: 32 }}>
                <SectionTitle subtitle="Our Journey" title="Kisah Cinta Kami" />
                <div style={{ position: 'relative', paddingLeft: 8 }}>
                    {/* Center line */}
                    <div style={{
                        position: 'absolute', left: '50%', top: 0, bottom: 0,
                        width: 2, background: `linear-gradient(to bottom, ${palette.neonAlt}, ${palette.neon})`,
                        transform: 'translateX(-50%)', opacity: 0.3,
                    }} />

                    {stories.map((story, i) => {
                        const isLeft = i % 2 === 0;
                        return (
                            <motion.div
                                key={i}
                                initial={{ opacity: 0, x: isLeft ? -50 : 50 }}
                                whileInView={{ opacity: 1, x: 0 }}
                                viewport={{ once: true, margin: '-60px' }}
                                transition={{ duration: 0.7, delay: i * 0.1 }}
                                style={{
                                    display: 'flex',
                                    justifyContent: isLeft ? 'flex-start' : 'flex-end',
                                    marginBottom: 36,
                                    position: 'relative',
                                }}
                            >
                                {/* Dot on center line */}
                                <div style={{
                                    position: 'absolute', left: '50%', top: 20,
                                    width: 14, height: 14, borderRadius: '50%',
                                    background: palette.neon,
                                    transform: 'translateX(-50%)',
                                    boxShadow: `0 0 12px ${palette.neon}`,
                                    zIndex: 2,
                                }} />

                                <div style={{
                                    width: '44%',
                                    borderRadius: 20,
                                    padding: '18px 20px',
                                    background: 'rgba(255,255,255,0.04)',
                                    border: '1px solid rgba(255,255,255,0.1)',
                                    textAlign: isLeft ? 'right' : 'left',
                                }}>
                                    {story.year && (
                                        <div style={{ fontSize: 12, letterSpacing: 3, color: palette.neonAlt, marginBottom: 6 }}>
                                            {story.year}
                                        </div>
                                    )}
                                    {story.title && (
                                        <div style={{ fontWeight: 800, fontSize: 16, color: palette.text, marginBottom: 6 }}>
                                            {story.title}
                                        </div>
                                    )}
                                    <div style={{ fontSize: 13, color: palette.muted, lineHeight: 1.7 }}>
                                        {story.description || story.text}
                                    </div>
                                </div>
                            </motion.div>
                        );
                    })}
                </div>
            </div>
        </FadeUp>
    );
}

// ─── AMPLOP DIGITAL / GIFT ────────────────────────────────────
export function AmplopDigital({ bankAccounts, qrisImageUrl }) {
    const [copiedIdx, setCopiedIdx] = useState(null);
    const [showQris, setShowQris] = useState(false);

    if ((!bankAccounts || !bankAccounts.length) && !qrisImageUrl) return null;

    const handleCopy = (text, idx) => {
        navigator.clipboard.writeText(text).then(() => {
            setCopiedIdx(idx);
            setTimeout(() => setCopiedIdx(null), 2000);
        });
    };

    return (
        <FadeUp>
            <div style={{ ...glassCard, marginTop: 32 }}>
                <SectionTitle subtitle="Wedding Gift" title="Amplop Digital" accent="Kasih sayang Anda adalah kebahagiaan terbesar kami" />

                {bankAccounts && bankAccounts.map((acc, i) => (
                    <motion.div
                        key={i}
                        whileHover={{ boxShadow: `0 0 30px rgba(255,113,200,0.2)` }}
                        style={{
                            borderRadius: 20, padding: '20px 22px',
                            border: '1px solid rgba(255,255,255,0.1)',
                            background: 'rgba(255,255,255,0.03)',
                            marginBottom: 14,
                        }}
                    >
                        <div style={{ fontSize: 12, letterSpacing: 3, textTransform: 'uppercase', color: palette.neonAlt, marginBottom: 8 }}>
                            {acc.bank_name || acc.bank}
                        </div>
                        <div style={{ fontSize: 22, fontWeight: 900, letterSpacing: 2, color: palette.text, marginBottom: 6 }}>
                            {acc.account_number || acc.number}
                        </div>
                        <div style={{ color: palette.muted, fontSize: 14, marginBottom: 16 }}>
                            a/n {acc.account_name || acc.name}
                        </div>
                        <ActionButton
                            onClick={() => handleCopy(acc.account_number || acc.number, i)}
                            variant={copiedIdx === i ? 'secondary' : 'primary'}
                            style={{ width: '100%' }}
                        >
                            {copiedIdx === i ? '✅ Tersalin!' : '📋 Salin Nomor'}
                        </ActionButton>
                    </motion.div>
                ))}

                {qrisImageUrl && (
                    <div style={{ marginTop: 20, textAlign: 'center' }}>
                        <ActionButton onClick={() => setShowQris(!showQris)} variant="secondary" style={{ width: '100%' }}>
                            {showQris ? 'Tutup QR Code' : '📷 Tampilkan QR Code'}
                        </ActionButton>
                        <AnimatePresence>
                            {showQris && (
                                <motion.div
                                    initial={{ opacity: 0, height: 0 }}
                                    animate={{ opacity: 1, height: 'auto' }}
                                    exit={{ opacity: 0, height: 0 }}
                                    style={{ overflow: 'hidden', marginTop: 20 }}
                                >
                                    <img
                                        src={qrisImageUrl}
                                        alt="QRIS"
                                        style={{ width: '100%', maxWidth: 280, borderRadius: 20, margin: '0 auto', display: 'block' }}
                                    />
                                </motion.div>
                            )}
                        </AnimatePresence>
                    </div>
                )}
            </div>
        </FadeUp>
    );
}
