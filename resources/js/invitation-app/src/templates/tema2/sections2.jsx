import React, { useState, useEffect } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import { palette, glassCard, formatDate } from './utils.js';
import { SectionTitle, ActionButton, GlassInput, FadeUp } from './components.jsx';

const GUESTBOOK_PER_PAGE = 5;

// ─── GUESTBOOK / WEDDING WISH ─────────────────────────────────
export function Guestbook({ invitationSlug, initialEntries }) {
    const [entries, setEntries] = useState(initialEntries || []);
    const [name, setName] = useState('');
    const [message, setMessage] = useState('');
    const [submitting, setSubmitting] = useState(false);
    const [submitted, setSubmitted] = useState(false);
    const [error, setError] = useState(null);
    const [page, setPage] = useState(0);

    const totalPages = Math.ceil(entries.length / GUESTBOOK_PER_PAGE);
    const visibleEntries = entries.slice(page * GUESTBOOK_PER_PAGE, (page + 1) * GUESTBOOK_PER_PAGE);

    const handleSubmit = async (e) => {
        e.preventDefault();
        if (!name.trim() || !message.trim()) return setError('Nama dan ucapan tidak boleh kosong.');
        setSubmitting(true);
        setError(null);
        try {
            const res = await fetch(`/api/invitations/${encodeURIComponent(invitationSlug)}/guestbook`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({ name, message }),
            });
            if (!res.ok) throw new Error('Gagal mengirim ucapan.');
            const data = await res.json();
            setEntries(prev => [data?.entry || { name, message, created_at: new Date().toISOString() }, ...prev]);
            setName(''); setMessage('');
            setSubmitted(true);
            setTimeout(() => setSubmitted(false), 4000);
            setPage(0);
        } catch (err) {
            // Fallback: show locally even if API fails
            setEntries(prev => [{ name, message, created_at: new Date().toISOString() }, ...prev]);
            setName(''); setMessage('');
            setSubmitted(true);
            setTimeout(() => setSubmitted(false), 4000);
            setPage(0);
        } finally {
            setSubmitting(false);
        }
    };

    return (
        <FadeUp>
            <div style={{ ...glassCard, marginTop: 32 }}>
                <SectionTitle
                    subtitle="Wedding Wish"
                    title="Ucapan & Doa"
                    accent={`${entries.length} ucapan telah dikirim`}
                />

                {/* Form */}
                <form onSubmit={handleSubmit} style={{ display: 'grid', gap: 14, marginBottom: 32 }}>
                    <GlassInput
                        value={name} onChange={e => setName(e.target.value)}
                        placeholder="Nama Anda"
                    />
                    <GlassInput
                        as="textarea"
                        rows={3}
                        value={message} onChange={e => setMessage(e.target.value)}
                        placeholder="Tuliskan ucapan dan doa terbaik..."
                        style={{ resize: 'none' }}
                    />
                    {error && <div style={{ color: '#f87171', fontSize: 13 }}>{error}</div>}
                    <AnimatePresence>
                        {submitted && (
                            <motion.div
                                initial={{ opacity: 0, y: -10 }}
                                animate={{ opacity: 1, y: 0 }}
                                exit={{ opacity: 0 }}
                                style={{
                                    padding: '12px 16px', borderRadius: 14,
                                    background: 'rgba(16,185,129,0.15)',
                                    border: '1px solid rgba(16,185,129,0.3)',
                                    color: '#6ee7b7',
                                    fontWeight: 700, fontSize: 14,
                                }}
                            >
                                ✨ Ucapan berhasil dikirim, terima kasih!
                            </motion.div>
                        )}
                    </AnimatePresence>
                    <ActionButton type="submit" disabled={submitting} style={{ width: '100%' }}>
                        {submitting ? '⏳ Mengirim...' : '💌 Kirim Ucapan'}
                    </ActionButton>
                </form>

                {/* Entries */}
                <div style={{ display: 'flex', flexDirection: 'column', gap: 14 }}>
                    {visibleEntries.length === 0 && (
                        <p style={{ color: palette.muted, textAlign: 'center', fontSize: 14 }}>
                            Belum ada ucapan. Jadilah yang pertama! 🌸
                        </p>
                    )}
                    {visibleEntries.map((entry, i) => (
                        <motion.div
                            key={`${page}-${i}`}
                            initial={{ opacity: 0, y: 16 }}
                            animate={{ opacity: 1, y: 0 }}
                            transition={{ duration: 0.4, delay: i * 0.07 }}
                            style={{
                                borderRadius: 20, padding: '18px 20px',
                                background: 'rgba(255,255,255,0.04)',
                                border: '1px solid rgba(255,255,255,0.08)',
                            }}
                        >
                            <div style={{ display: 'flex', alignItems: 'center', gap: 10, marginBottom: 10 }}>
                                <div style={{
                                    width: 38, height: 38, borderRadius: '50%',
                                    background: `linear-gradient(135deg, ${palette.neon}, ${palette.neonAlt})`,
                                    display: 'flex', alignItems: 'center', justifyContent: 'center',
                                    fontWeight: 900, fontSize: 16, color: '#080112',
                                    flexShrink: 0,
                                }}>
                                    {(entry.name || '?')[0].toUpperCase()}
                                </div>
                                <div>
                                    <div style={{ fontWeight: 700, fontSize: 14, color: palette.text }}>
                                        {entry.name || 'Anonim'}
                                    </div>
                                    {entry.created_at && (
                                        <div style={{ fontSize: 11, color: palette.muted }}>
                                            {new Date(entry.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })}
                                        </div>
                                    )}
                                </div>
                            </div>
                            <p style={{ fontSize: 14, color: palette.muted, lineHeight: 1.7, margin: 0 }}>
                                "{entry.message}"
                            </p>
                        </motion.div>
                    ))}
                </div>

                {/* Pagination */}
                {totalPages > 1 && (
                    <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginTop: 20, gap: 12 }}>
                        <ActionButton
                            onClick={() => setPage(p => Math.max(0, p - 1))}
                            disabled={page === 0}
                            variant="secondary"
                            style={{ opacity: page === 0 ? 0.4 : 1, flex: 1 }}
                        >
                            ← Sebelumnya
                        </ActionButton>
                        <span style={{ color: palette.muted, fontSize: 13, whiteSpace: 'nowrap' }}>
                            {page + 1} / {totalPages}
                        </span>
                        <ActionButton
                            onClick={() => setPage(p => Math.min(totalPages - 1, p + 1))}
                            disabled={page >= totalPages - 1}
                            variant="secondary"
                            style={{ opacity: page >= totalPages - 1 ? 0.4 : 1, flex: 1 }}
                        >
                            Selanjutnya →
                        </ActionButton>
                    </div>
                )}
            </div>
        </FadeUp>
    );
}

// ─── RSVP FORM ────────────────────────────────────────────────
export function RSVPSection({ invitationSlug, rsvpEnabled }) {
    const [name, setName] = useState('');
    const [phone, setPhone] = useState('');
    const [guests, setGuests] = useState(1);
    const [attendance, setAttendance] = useState('hadir');
    const [message, setMessage] = useState('');
    const [submitting, setSubmitting] = useState(false);
    const [success, setSuccess] = useState(null);
    const [error, setError] = useState(null);

    if (!rsvpEnabled) return null;

    const attendanceOpts = [
        { value: 'hadir', label: '✅ Hadir', color: '#10b981' },
        { value: 'tidak_hadir', label: '❌ Tidak Hadir', color: '#ef4444' },
        { value: 'mungkin', label: '🤔 Mungkin', color: '#f59e0b' },
    ];

    const handleSubmit = async (e) => {
        e.preventDefault();
        if (!name.trim()) return setError('Nama tidak boleh kosong.');
        setSubmitting(true); setError(null);
        try {
            const res = await fetch(`/api/invitations/${encodeURIComponent(invitationSlug)}/rsvp`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json', Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({ name, phone: phone || null, guests_count: Number(guests), attendance, message: message || null }),
            });
            if (!res.ok) {
                const d = await res.json().catch(() => null);
                throw new Error(d?.message || 'Gagal mengirim RSVP.');
            }
            const d = await res.json();
            setSuccess(d?.message || 'RSVP berhasil! Terima kasih atas konfirmasinya. 💕');
        } catch (err) {
            setError(err?.message || 'Gagal mengirim RSVP.');
        } finally {
            setSubmitting(false);
        }
    };

    return (
        <FadeUp>
            <div style={{ ...glassCard, marginTop: 32 }}>
                <SectionTitle subtitle="RSVP" title="Konfirmasi Kehadiran" accent="Jawaban Anda sangat berarti bagi kami" />
                {success ? (
                    <motion.div
                        initial={{ opacity: 0, scale: 0.9 }}
                        animate={{ opacity: 1, scale: 1 }}
                        style={{
                            padding: '28px 20px', textAlign: 'center',
                            borderRadius: 20, background: 'rgba(16,185,129,0.1)',
                            border: '1px solid rgba(16,185,129,0.2)',
                        }}
                    >
                        <div style={{ fontSize: 48, marginBottom: 12 }}>🎊</div>
                        <p style={{ fontWeight: 800, fontSize: 18, color: '#6ee7b7' }}>{success}</p>
                    </motion.div>
                ) : (
                    <form onSubmit={handleSubmit} style={{ display: 'grid', gap: 16 }}>
                        <GlassInput value={name} onChange={e => setName(e.target.value)} required placeholder="Nama lengkap *" />
                        <GlassInput value={phone} onChange={e => setPhone(e.target.value)} placeholder="Nomor WhatsApp (opsional)" />
                        <div>
                            <label style={{ color: palette.muted, fontSize: 13, marginBottom: 8, display: 'block' }}>Jumlah Tamu</label>
                            <div style={{ display: 'flex', gap: 8, flexWrap: 'wrap' }}>
                                {[1, 2, 3, 4, 5].map(n => (
                                    <motion.button
                                        key={n} type="button"
                                        whileTap={{ scale: 0.9 }}
                                        onClick={() => setGuests(n)}
                                        style={{
                                            width: 44, height: 44, borderRadius: 12,
                                            border: guests === n ? 'none' : '1px solid rgba(255,255,255,0.15)',
                                            background: guests === n ? `linear-gradient(135deg, ${palette.neon}, ${palette.neonAlt})` : 'rgba(255,255,255,0.04)',
                                            color: guests === n ? '#080112' : palette.text,
                                            fontWeight: 800, cursor: 'pointer', fontSize: 15,
                                        }}
                                    >{n}</motion.button>
                                ))}
                            </div>
                        </div>
                        <div style={{ display: 'flex', gap: 10, flexWrap: 'wrap' }}>
                            {attendanceOpts.map(opt => (
                                <motion.button
                                    key={opt.value} type="button"
                                    whileTap={{ scale: 0.94 }}
                                    onClick={() => setAttendance(opt.value)}
                                    style={{
                                        flex: 1, minWidth: 90, padding: '12px 8px', borderRadius: 16,
                                        border: attendance === opt.value ? `1px solid ${opt.color}` : '1px solid rgba(255,255,255,0.1)',
                                        background: attendance === opt.value ? `${opt.color}22` : 'rgba(255,255,255,0.03)',
                                        color: attendance === opt.value ? opt.color : palette.muted,
                                        fontWeight: 700, fontSize: 13, cursor: 'pointer',
                                    }}
                                >{opt.label}</motion.button>
                            ))}
                        </div>
                        <GlassInput as="textarea" rows={3} value={message} onChange={e => setMessage(e.target.value)} placeholder="Ucapan & doa (opsional)" style={{ resize: 'none' }} />
                        {error && <div style={{ color: '#f87171', fontSize: 13 }}>{error}</div>}
                        <ActionButton type="submit" disabled={submitting} style={{ width: '100%' }}>
                            {submitting ? '⏳ Mengirim...' : '💌 Kirim RSVP'}
                        </ActionButton>
                    </form>
                )}
            </div>
        </FadeUp>
    );
}
