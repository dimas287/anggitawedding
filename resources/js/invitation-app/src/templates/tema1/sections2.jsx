import React, { useState, useEffect } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import { FadeUp } from './components.jsx';
import { palette, fonts } from './utils.js';

const resolveMediaUrl = (value) => {
    if (!value) return null;
    const raw = Array.isArray(value) ? value[0] : value;
    const src = String(raw);
    if (src.startsWith('http')) return src;
    if (src.startsWith('/storage/')) return src;
    return `/storage/${src.replace(/^\/+/, '')}`;
};

export function GallerySection({ data }) {
    const defaultGallery = [
        data.thumbnail, data.photo_prewedding_url, data.photo_prewedding_url
    ].filter(Boolean);
    
    // Use dynamic media slots gallery if available (support 'gallery' or 'galeri')
    let galleryImages = data?.media_files?.gallery || data?.media_files?.galeri || data.gallery_photo_urls || data.demo_gallery || defaultGallery;
    if (!Array.isArray(galleryImages)) {
        galleryImages = [galleryImages];
    }
    const fullUrls = galleryImages
        .filter(Boolean)
        .map((img) => resolveMediaUrl(img))
        .filter(Boolean);

    const clipUrlRaw = data?.media_files?.prewedding_clip || data?.prewedding_clip || data?.video_url || null;
    const clipUrl = clipUrlRaw
        ? (Array.isArray(clipUrlRaw) ? clipUrlRaw[0] : clipUrlRaw)
        : null;
    const normalizedClipUrl = clipUrl ? resolveMediaUrl(clipUrl) : null;
    const fallbackClipPoster = resolveMediaUrl(data.photo_prewedding_url || data.thumbnail);

    const [selectedImg, setSelectedImg] = useState(null);

    return (
        <section id="gallery" style={{ background: 'transparent', padding: '100px 20px 120px' }}>
            <div style={{ maxWidth: 760, margin: '0 auto', textAlign: 'center' }}>
                <FadeUp>
                    <h3 style={{ fontFamily: fonts.serif, fontSize: 'clamp(1.7rem, 6vw, 2.4rem)', color: palette.text, fontWeight: 400, margin: 0 }}>
                        Our Prewedding Clip
                    </h3>
                </FadeUp>

                <FadeUp delay={0.15}>
                    <div style={{ marginTop: 20, border: `1px solid ${palette.border}`, overflow: 'hidden', background: '#0d0d0d' }}>
                        {normalizedClipUrl ? (
                            <video
                                src={normalizedClipUrl}
                                controls
                                muted
                                loop
                                playsInline
                                style={{ width: '100%', display: 'block', aspectRatio: '9 / 16', objectFit: 'cover' }}
                            />
                        ) : (
                            <div
                                style={{
                                    width: '100%',
                                    aspectRatio: '9 / 16',
                                    backgroundImage: `url(${fallbackClipPoster})`,
                                    backgroundSize: 'cover',
                                    backgroundPosition: 'center'
                                }}
                            />
                        )}
                    </div>
                </FadeUp>

                <FadeUp delay={0.2}>
                    <p style={{ margin: '14px 0 26px', fontFamily: fonts.sans, fontSize: 12, color: palette.muted, letterSpacing: '1px' }}>
                        Click image for preview
                    </p>
                </FadeUp>

                <div style={{ display: 'grid', gridTemplateColumns: 'repeat(3, minmax(0, 1fr))', gap: 10 }}>
                    {fullUrls.map((img, i) => (
                        <motion.button
                            key={i}
                            type="button"
                            onClick={() => setSelectedImg(img)}
                            initial={{ opacity: 0, y: 20 }}
                            whileInView={{ opacity: 1, y: 0 }}
                            viewport={{ once: true }}
                            transition={{ delay: 0.1 + (i * 0.05), duration: 0.7 }}
                            style={{
                                border: 'none',
                                padding: 0,
                                cursor: 'pointer',
                                width: '100%',
                                background: 'transparent',
                                overflow: 'hidden'
                            }}
                        >
                            <img
                                src={img}
                                alt={`gallery-${i}`}
                                style={{ width: '100%', height: '100%', aspectRatio: i % 2 === 0 ? '3 / 4' : '4 / 5', objectFit: 'cover', display: 'block' }}
                            />
                        </motion.button>
                    ))}
                </div>

                <FadeUp delay={0.35}>
                    <p style={{ marginTop: 26, fontFamily: fonts.serif, fontSize: 'clamp(1rem, 3.8vw, 1.25rem)', color: palette.muted, fontStyle: 'italic', lineHeight: 1.8 }}>
                        “We are here to love, to learn how to love and to become good at it. This is our greatest challenge and also our greatest reward.”
                    </p>
                </FadeUp>
            </div>

            <AnimatePresence>
                {selectedImg && (
                    <motion.div
                        initial={{ opacity: 0 }}
                        animate={{ opacity: 1 }}
                        exit={{ opacity: 0 }}
                        onClick={() => setSelectedImg(null)}
                        style={{
                            position: 'fixed', inset: 0, background: 'rgba(0,0,0,0.95)',
                            display: 'flex', alignItems: 'center', justifyContent: 'center',
                            zIndex: 9999, padding: 20
                        }}
                    >
                        <motion.img
                            initial={{ scale: 0.9, opacity: 0 }}
                            animate={{ scale: 1, opacity: 1 }}
                            exit={{ scale: 0.9, opacity: 0 }}
                            src={selectedImg}
                            style={{
                                maxWidth: '100%', maxHeight: '90vh', objectFit: 'contain',
                                borderRadius: 4, boxShadow: '0 20px 50px rgba(0,0,0,0.5)'
                            }}
                            onClick={e => e.stopPropagation()}
                        />
                        <button
                            onClick={() => setSelectedImg(null)}
                            style={{
                                position: 'absolute', top: 30, right: 30,
                                background: 'transparent', border: 'none', color: '#fff',
                                fontSize: 32, cursor: 'pointer', padding: 10
                            }}
                        >
                            <i className="fas fa-times"></i>
                        </button>
                    </motion.div>
                )}
            </AnimatePresence>
        </section>
    );
}

export function ClosingSection({ data }) {
    return (
        <section
            style={{
                position: 'relative',
                minHeight: '78vh',
                padding: '100px 20px 120px',
                background: 'transparent'
            }}
        >
            <div style={{ position: 'absolute', inset: 0, background: 'rgba(0,0,0,0.12)' }} />

            <div style={{ position: 'relative', zIndex: 1, maxWidth: 740, margin: '0 auto', textAlign: 'center' }}>
                <motion.h3
                    initial={{ opacity: 0, y: 20 }}
                    whileInView={{ opacity: 1, y: 0 }}
                    viewport={{ once: true }}
                    transition={{ duration: 0.9 }}
                    style={{
                        margin: 0,
                        fontFamily: fonts.serif,
                        fontSize: 'clamp(1.65rem, 6vw, 2.4rem)',
                        fontWeight: 400,
                        color: palette.text,
                        lineHeight: 1.45
                    }}
                >
                    Thank You For Your Attendance And Prayers For Us.
                </motion.h3>

                <motion.p
                    initial={{ opacity: 0, y: 18 }}
                    whileInView={{ opacity: 1, y: 0 }}
                    viewport={{ once: true }}
                    transition={{ duration: 0.9, delay: 0.15 }}
                    style={{
                        margin: '18px auto 0',
                        maxWidth: 620,
                        fontFamily: fonts.sans,
                        fontSize: 13,
                        color: palette.muted,
                        lineHeight: 1.9
                    }}
                >
                    {data.closing_message || 'It is a pleasure and honor for us, if you are willing to attend and give us your blessing.'}
                </motion.p>

                <motion.h2
                    initial={{ opacity: 0, y: 22 }}
                    whileInView={{ opacity: 1, y: 0 }}
                    viewport={{ once: true }}
                    transition={{ duration: 1, delay: 0.25 }}
                    style={{
                        margin: '24px 0 0',
                        fontFamily: fonts.serif,
                        fontSize: 'clamp(2.3rem, 11vw, 4.1rem)',
                        fontWeight: 400,
                        color: palette.text,
                        lineHeight: 1.08
                    }}
                >
                    {data.groom_short_name} &amp; {data.bride_short_name}
                </motion.h2>
            </div>
        </section>
    );
}

export function GuestbookSection({ data }) {
    const [name, setName] = useState('');
    const [message, setMessage] = useState('');
    const [attendance, setAttendance] = useState('EXCITED TO ATTEND');
    const [guestCount, setGuestCount] = useState(1);
    const [entries, setEntries] = useState([]);
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState(null);

    // Fetch guestbook
    useEffect(() => {
        const fetchSlug = data.slug || 'tema1-demo';
        fetch(`/api/invitations/${fetchSlug}/guestbook`)
            .then(res => res.json())
            .then(res => {
                if (res.success) {
                    setEntries(res.data.data || []);
                }
            })
            .catch(err => console.error("Failed to fetch guestbook", err));
    }, [data.slug]);

    const handleSubmit = async (e) => {
        e.preventDefault();
        if (!name.trim() || !message.trim()) return;
        
        const submitSlug = data.slug || 'tema1-demo';

        setLoading(true);
        setError(null);
        try {
            const res = await fetch(`/api/invitations/${submitSlug}/guestbook`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify({ name, message, attendance, guest_count: guestCount })
            });
            const result = await res.json();
            if (result.success) {
                setEntries([result.data, ...entries]);
                setName('');
                setMessage('');
                alert('Terima kasih! Pesan Anda telah terkirim.');
            } else {
                setError('Maaf, saat ini tidak bisa mengirim pesan. (Pastikan slug "tema1-demo" ada di database)');
            }
        } catch (e) {
            setError('Terjadi kesalahan koneksi.');
        } finally {
            setLoading(false);
        }
    };

    return (
        <section id="rsvp" style={{ background: 'transparent', padding: '100px 20px' }}>
            <div style={{ maxWidth: 740, margin: '0 auto' }}>
                <FadeUp>
                    <h3 style={{ textAlign: 'center', fontFamily: fonts.serif, fontSize: 'clamp(1.6rem, 5.5vw, 2.3rem)', color: palette.text, fontWeight: 400, margin: '0 0 28px' }}>
                        Kindly Confirm Your Presence And Share Your Blessings
                    </h3>
                </FadeUp>

                <FadeUp delay={0.15}>
                    <form onSubmit={handleSubmit} style={{ border: `1px solid ${palette.border}`, padding: '24px 20px', background: 'rgba(255,255,255,0.03)', backdropFilter: 'blur(4px)' }}>
                        {error && <p style={{ color: '#ef4444', fontSize: 13, marginTop: 0 }}>{error}</p>}

                        <div style={{ marginBottom: 20 }}>
                            <label style={{ display: 'block', fontFamily: fonts.sans, fontSize: 11, letterSpacing: '2px', textTransform: 'uppercase', color: palette.accent, marginBottom: 8 }}>Name</label>
                            <input
                                type="text"
                                required
                                value={name}
                                onChange={(e) => setName(e.target.value)}
                                style={{ width: '100%', padding: '12px 0', border: 'none', borderBottom: `1px solid ${palette.border}`, background: 'transparent', color: palette.text, fontFamily: fonts.sans, outline: 'none', fontSize: 14 }}
                            />
                        </div>

                        <div style={{ marginBottom: 20 }}>
                            <label style={{ display: 'block', fontFamily: fonts.sans, fontSize: 11, letterSpacing: '2px', textTransform: 'uppercase', color: palette.accent, marginBottom: 12 }}>Attendance</label>
                            <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 10 }}>
                                <button
                                    type="button"
                                    onClick={() => setAttendance('EXCITED TO ATTEND')}
                                    style={{ padding: '12px 8px', border: `1px solid ${palette.border}`, background: attendance === 'EXCITED TO ATTEND' ? palette.accent : 'transparent', color: attendance === 'EXCITED TO ATTEND' ? '#000' : palette.text, fontFamily: fonts.sans, fontSize: 10, letterSpacing: '1px', cursor: 'pointer', transition: '0.3s' }}
                                >
                                    EXCITED TO ATTEND
                                </button>
                                <button
                                    type="button"
                                    onClick={() => setAttendance('UNABLE ATTEND')}
                                    style={{ padding: '12px 8px', border: `1px solid ${palette.border}`, background: attendance === 'UNABLE ATTEND' ? palette.accent : 'transparent', color: attendance === 'UNABLE ATTEND' ? '#000' : palette.text, fontFamily: fonts.sans, fontSize: 10, letterSpacing: '1px', cursor: 'pointer', transition: '0.3s' }}
                                >
                                    UNABLE ATTEND
                                </button>
                            </div>
                        </div>

                        <div style={{ marginBottom: 20 }}>
                            <label style={{ display: 'block', fontFamily: fonts.sans, fontSize: 11, letterSpacing: '2px', textTransform: 'uppercase', color: palette.accent, marginBottom: 8 }}>No of Guest (Max 2)</label>
                            <div style={{ display: 'flex', alignItems: 'center', borderBottom: `1px solid ${palette.border}`, padding: '4px 0' }}>
                                <button type="button" onClick={() => setGuestCount((v) => Math.max(1, v - 1))} style={{ width: 40, height: 40, border: 'none', background: 'transparent', color: palette.text, fontSize: 20, cursor: 'pointer' }}>-</button>
                                <input
                                    type="number"
                                    min={1}
                                    max={2}
                                    value={guestCount}
                                    readOnly
                                    style={{ flex: 1, height: 40, border: 'none', background: 'transparent', color: palette.text, textAlign: 'center', fontFamily: fonts.sans, outline: 'none', fontSize: 16 }}
                                />
                                <button type="button" onClick={() => setGuestCount((v) => Math.min(2, v + 1))} style={{ width: 40, height: 40, border: 'none', background: 'transparent', color: palette.text, fontSize: 20, cursor: 'pointer' }}>+</button>
                            </div>
                        </div>

                        <div style={{ marginBottom: 24 }}>
                            <label style={{ display: 'block', fontFamily: fonts.sans, fontSize: 11, letterSpacing: '2px', textTransform: 'uppercase', color: palette.accent, marginBottom: 8 }}>Wishes</label>
                            <textarea
                                rows={4}
                                required
                                value={message}
                                onChange={(e) => setMessage(e.target.value)}
                                style={{ width: '100%', padding: '12px 0', border: 'none', borderBottom: `1px solid ${palette.border}`, background: 'transparent', color: palette.text, fontFamily: fonts.sans, outline: 'none', resize: 'none', fontSize: 14, lineHeight: 1.6 }}
                            />
                        </div>

                        <button type="submit" disabled={loading} style={{ width: '100%', padding: '14px', border: `1px solid ${palette.border}`, background: 'transparent', color: palette.text, fontFamily: fonts.sans, letterSpacing: '3px', textTransform: 'uppercase', cursor: 'pointer', fontSize: 12, transition: '0.3s' }}>
                            {loading ? 'Sending...' : 'Send Message'}
                        </button>
                    </form>
                </FadeUp>

                <FadeUp delay={0.25}>
                    <div style={{ marginTop: 24, maxHeight: 360, overflowY: 'auto' }}>
                        {entries.length === 0 ? (
                            <p style={{ textAlign: 'center', color: palette.muted, fontFamily: fonts.sans, fontSize: 13 }}>
                                Belum ada ucapan.
                            </p>
                        ) : (
                            entries.map((entry, i) => (
                                <motion.div
                                    key={entry.id || i}
                                    initial={{ opacity: 0, y: 12 }}
                                    animate={{ opacity: 1, y: 0 }}
                                    transition={{ delay: i * 0.04 }}
                                    style={{ borderBottom: `1px solid rgba(255,255,255,0.08)`, padding: '12px 4px' }}
                                >
                                    <p style={{ margin: '0 0 4px', fontFamily: fonts.sans, fontSize: 13, color: palette.text }}>{entry.name}</p>
                                    <p style={{ margin: 0, fontFamily: fonts.sans, fontSize: 12, color: palette.muted, lineHeight: 1.7 }}>
                                        {entry.message}
                                    </p>
                                </motion.div>
                            ))
                        )}
                    </div>
                </FadeUp>
            </div>
        </section>
    );
}

export function GiftSection({ data }) {
    const handleCopy = (text) => {
        navigator.clipboard.writeText(text);
        alert('Nomor rekening berhasil disalin!');
    };

    const hasAccounts = data.bank_accounts && data.bank_accounts.length > 0;
    const hasQris = !!data.qris_image;

    if (!hasAccounts && !hasQris) return null;

    return (
        <section id="weddinggift" style={{ background: 'transparent', padding: '100px 20px' }}>
            <div style={{ maxWidth: 760, margin: '0 auto' }}>
                <FadeUp>
                    <h3 style={{ textAlign: 'center', fontFamily: fonts.serif, fontSize: 'clamp(1.7rem, 5.8vw, 2.4rem)', color: palette.text, fontWeight: 400, margin: '0 0 30px' }}>
                        Wedding Gift
                    </h3>
                </FadeUp>

                <div style={{ display: 'grid', gridTemplateColumns: '1fr', gap: 14 }}>
                    {data.bank_accounts?.map((acc, i) => (
                        <motion.div
                            key={i}
                            initial={{ opacity: 0, y: 20 }}
                            whileInView={{ opacity: 1, y: 0 }}
                            viewport={{ once: true }}
                            transition={{ delay: i * 0.08 }}
                            style={{ border: `1px solid ${palette.border}`, padding: '16px 14px', textAlign: 'center' }}
                        >
                            <p style={{ margin: '0 0 8px', fontFamily: fonts.sans, fontSize: 13, color: palette.text }}>{acc.account_name}</p>
                            <div style={{ margin: '0 auto 10px', maxWidth: 320, background: 'rgba(134,134,134,0.28)', padding: '10px 12px' }}>
                                <p style={{ margin: 0, fontFamily: fonts.serif, fontSize: 20, color: palette.text, lineHeight: 1.5 }}>
                                    {acc.bank_name}<br />{acc.account_number}
                                </p>
                            </div>
                            <button
                                type="button"
                                onClick={() => handleCopy(acc.account_number)}
                                style={{ width: 46, height: 42, border: `1px solid ${palette.border}`, background: 'transparent', color: palette.text, cursor: 'pointer' }}
                                aria-label="copy-account"
                            >
                                <i className="fas fa-copy"></i>
                            </button>
                        </motion.div>
                    ))}

                    {data.qris_image && (
                        <motion.div
                            initial={{ opacity: 0, y: 20 }}
                            whileInView={{ opacity: 1, y: 0 }}
                            viewport={{ once: true }}
                            transition={{ delay: 0.2 }}
                            style={{ border: `1px solid ${palette.border}`, padding: '16px 14px', textAlign: 'center' }}
                        >
                            <p style={{ margin: '0 0 10px', fontFamily: fonts.sans, fontSize: 13, color: palette.text }}>QRIS</p>
                            <img
                                src={`/storage/${data.qris_image}`}
                                alt="QRIS"
                                style={{ width: '100%', maxWidth: 260, margin: '0 auto', display: 'block' }}
                            />
                        </motion.div>
                    )}

                    <FadeUp delay={0.25}>
                        <a
                            href="#rsvp"
                            style={{
                                display: 'inline-block',
                                margin: '6px auto 0',
                                padding: '10px 18px',
                                border: `1px solid ${palette.border}`,
                                color: palette.text,
                                textDecoration: 'none',
                                fontFamily: fonts.sans,
                                fontSize: 12,
                                letterSpacing: '2px',
                                textTransform: 'uppercase'
                            }}
                        >
                            Confirm
                        </a>
                    </FadeUp>
                </div>
            </div>
        </section>
    );
}
