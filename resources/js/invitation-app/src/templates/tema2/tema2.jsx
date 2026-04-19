import React, { useMemo, useState, useEffect, useRef } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import { Swiper, SwiperSlide } from 'swiper/react';
import { Pagination, Autoplay, EffectFade } from 'swiper/modules';
import { Howl } from 'howler';
import LazyImage from '../../components/LazyImage.jsx';

import { palette, glassCard, formatDate, formatTime } from './utils.js';
import { SectionTitle, FloatingOrbs, ActionButton, FadeUp } from './components.jsx';
import { CountdownTimer, LoveStory, AmplopDigital } from './sections1.jsx';
import { Guestbook, RSVPSection } from './sections2.jsx';

import 'swiper/css';
import 'swiper/css/pagination';
import 'swiper/css/effect-fade';

// ─── MUSIC PLAYER ─────────────────────────────────────────────
function MusicPlayer({ musicUrl, coverClosed }) {
    const [playing, setPlaying] = useState(false);
    const soundRef = useRef(null);

    useEffect(() => {
        if (!musicUrl) return;
        soundRef.current = new Howl({ src: [musicUrl], loop: true, volume: 0.45 });
        return () => soundRef.current?.stop();
    }, [musicUrl]);

    const toggle = () => {
        if (!soundRef.current) return;
        if (playing) { soundRef.current.pause(); setPlaying(false); }
        else { soundRef.current.play(); setPlaying(true); }
    };

    // expose for cover open action
    window.__tema2PlayMusic = () => { soundRef.current?.play(); setPlaying(true); };

    if (!musicUrl || coverClosed === false) return null;

    return (
        <motion.button
            whileTap={{ scale: 0.9 }}
            whileHover={{ scale: 1.1 }}
            animate={playing ? { boxShadow: ['0 0 0px rgba(255,113,200,0)', '0 0 28px rgba(255,113,200,0.7)', '0 0 0px rgba(255,113,200,0)'] } : {}}
            transition={{ duration: 1.8, repeat: Infinity }}
            onClick={toggle}
            style={{
                position: 'fixed', right: 20, bottom: 24, zIndex: 20,
                width: 52, height: 52, borderRadius: '50%',
                background: playing
                    ? 'linear-gradient(135deg, #ff71c8, #6cfeff)'
                    : 'rgba(255,255,255,0.1)',
                border: '1px solid rgba(255,255,255,0.2)',
                backdropFilter: 'blur(16px)',
                cursor: 'pointer', fontSize: 22,
                display: 'flex', alignItems: 'center', justifyContent: 'center',
            }}
        >
            {playing ? '🎵' : '🔇'}
        </motion.button>
    );
}

// ─── COVER SCREEN ─────────────────────────────────────────────
function CoverScreen({ content, onOpen, onOpenSilent, coverOpen }) {
    const guestName = useMemo(() => {
        if (typeof window === 'undefined') return null;
        return new URLSearchParams(window.location.search).get('to');
    }, []);

    const receptionDate = formatDate(content.reception_datetime);

    return (
        <AnimatePresence>
            {coverOpen && (
                <motion.div
                    initial={{ opacity: 0 }}
                    animate={{ opacity: 1 }}
                    exit={{ opacity: 0, scale: 0.96 }}
                    transition={{ duration: 0.7 }}
                    style={{
                        position: 'fixed', inset: 0, zIndex: 50,
                        display: 'flex', alignItems: 'center', justifyContent: 'center',
                        padding: '20px',
                        background: 'rgba(3, 0, 14, 0.94)',
                        backdropFilter: 'blur(20px)',
                        overflow: 'auto',
                    }}
                >
                    {/* Background orbs on cover */}
                    {[
                        { size: 300, color: '#ff71c814', top: '10%', left: '60%' },
                        { size: 200, color: '#6cfeff12', top: '70%', left: '10%' },
                    ].map((orb, i) => (
                        <motion.div key={i}
                            animate={{ scale: [0.9, 1.08, 0.9], opacity: [0.6, 1, 0.6] }}
                            transition={{ duration: 18 + i * 4, repeat: Infinity }}
                            style={{
                                position: 'fixed', width: orb.size, height: orb.size,
                                borderRadius: '50%', background: orb.color,
                                filter: 'blur(50px)', top: orb.top, left: orb.left,
                                pointerEvents: 'none',
                            }}
                        />
                    ))}

                    <motion.div
                        initial={{ scale: 0.85, opacity: 0 }}
                        animate={{ scale: 1, opacity: 1 }}
                        exit={{ scale: 0.92, opacity: 0 }}
                        transition={{ duration: 0.9, ease: [0.22, 1, 0.36, 1] }}
                        style={{
                            ...glassCard, width: '100%', maxWidth: 440,
                            textAlign: 'center',
                            background: 'rgba(5, 0, 22, 0.88)',
                            border: '1px solid rgba(255,255,255,0.15)',
                            position: 'relative', zIndex: 2,
                        }}
                    >
                        {/* Rotating border ring */}
                        <motion.div
                            animate={{ rotate: 360 }}
                            transition={{ duration: 20, repeat: Infinity, ease: 'linear' }}
                            style={{
                                position: 'absolute', top: -12, left: -12, right: -12, bottom: -12,
                                borderRadius: 40, border: '1px dashed rgba(255,113,200,0.25)',
                                pointerEvents: 'none',
                            }}
                        />

                        {/* Photo */}
                        <div style={{ marginBottom: 24, position: 'relative', display: 'inline-block' }}>
                            <motion.div
                                animate={{ rotate: -360 }}
                                transition={{ duration: 16, repeat: Infinity, ease: 'linear' }}
                                style={{
                                    position: 'absolute', inset: -8, borderRadius: '50%',
                                    border: '2px dashed rgba(108,254,255,0.35)',
                                }}
                            />
                            {content.photo_prewedding_url ? (
                                <img
                                    src={content.photo_prewedding_url}
                                    alt="Prewedding"
                                    style={{
                                        width: 140, height: 140, borderRadius: '50%',
                                        objectFit: 'cover',
                                        border: `3px solid rgba(255,113,200,0.5)`,
                                        boxShadow: `0 0 40px rgba(255,113,200,0.3)`,
                                    }}
                                />
                            ) : (
                                <div style={{
                                    width: 140, height: 140, borderRadius: '50%',
                                    background: `linear-gradient(135deg, ${palette.neon}, ${palette.neonAlt})`,
                                    display: 'flex', alignItems: 'center', justifyContent: 'center',
                                    fontSize: 56,
                                }}>💍</div>
                            )}
                        </div>

                        <motion.p
                            animate={{ opacity: [0.6, 1, 0.6], letterSpacing: [4, 7, 4] }}
                            transition={{ duration: 2.5, repeat: Infinity }}
                            style={{ color: palette.neonAlt, textTransform: 'uppercase', fontSize: 11, letterSpacing: 6 }}
                        >
                            Undangan Pernikahan
                        </motion.p>

                        <motion.div
                            animate={{ scale: [1, 1.04, 0.98, 1] }}
                            transition={{ duration: 3, repeat: Infinity }}
                            style={{ marginTop: 14 }}
                        >
                            <div style={{ fontSize: 34, fontWeight: 900, color: palette.text }}>
                                {content.groom_short_name || content.groom_name?.split(' ')[0]}
                            </div>
                            <motion.div
                                animate={{ y: [0, -5, 0], opacity: [0.8, 1, 0.8] }}
                                transition={{ duration: 1.5, repeat: Infinity }}
                                style={{ fontSize: 26, color: palette.neonAlt, margin: '6px 0' }}
                            >
                                &amp;
                            </motion.div>
                            <div style={{ fontSize: 34, fontWeight: 900, color: palette.text }}>
                                {content.bride_short_name || content.bride_name?.split(' ')[0]}
                            </div>
                        </motion.div>

                        {receptionDate && (
                            <p style={{ marginTop: 14, color: palette.muted, fontSize: 14 }}>{receptionDate}</p>
                        )}

                        {guestName && (
                            <motion.div
                                initial={{ opacity: 0 }}
                                animate={{ opacity: 1 }}
                                transition={{ delay: 0.5 }}
                                style={{
                                    margin: '18px 0 0',
                                    padding: '12px 20px',
                                    borderRadius: 16,
                                    background: 'rgba(255,255,255,0.04)',
                                    border: '1px solid rgba(255,255,255,0.1)',
                                }}
                            >
                                <p style={{ color: palette.muted, fontSize: 12 }}>Kepada Yth.</p>
                                <p style={{ color: palette.neon, fontWeight: 800, fontSize: 16, marginTop: 4 }}>
                                    {decodeURIComponent(guestName)}
                                </p>
                            </motion.div>
                        )}

                        <div style={{ marginTop: 28, display: 'flex', flexDirection: 'column', gap: 12 }}>
                            <ActionButton onClick={onOpen} style={{ width: '100%' }}>
                                💌 Buka Undangan
                            </ActionButton>
                            <ActionButton onClick={onOpenSilent} variant="secondary" style={{ width: '100%' }}>
                                🔇 Masuk Tanpa Musik
                            </ActionButton>
                        </div>
                    </motion.div>
                </motion.div>
            )}
        </AnimatePresence>
    );
}

// ─── PROFILE SECTION ──────────────────────────────────────────
function ProfileSection({ content }) {
    const groom = {
        name: content.groom_name,
        father: content.groom_father,
        mother: content.groom_mother,
        photo: content.groom_photo_url,
        short: content.groom_short_name || content.groom_name?.split(' ')[0],
    };
    const bride = {
        name: content.bride_name,
        father: content.bride_father,
        mother: content.bride_mother,
        photo: content.bride_photo_url,
        short: content.bride_short_name || content.bride_name?.split(' ')[0],
    };

    const CardProfile = ({ person, delay = 0 }) => (
        <motion.div
            initial={{ opacity: 0, y: 40 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true, margin: '-50px' }}
            transition={{ duration: 0.7, delay }}
            style={{
                flex: 1, textAlign: 'center',
                borderRadius: 24, padding: '24px 16px',
                background: 'rgba(255,255,255,0.04)',
                border: '1px solid rgba(255,255,255,0.08)',
            }}
        >
            {person.photo ? (
                <img
                    src={person.photo} alt={person.name}
                    style={{ width: 100, height: 100, borderRadius: '50%', objectFit: 'cover', margin: '0 auto 14px', display: 'block', border: `3px solid rgba(255,113,200,0.4)` }}
                />
            ) : (
                <div style={{
                    width: 100, height: 100, borderRadius: '50%', margin: '0 auto 14px',
                    background: `linear-gradient(135deg, ${palette.neon}, ${palette.neonAlt})`,
                    display: 'flex', alignItems: 'center', justifyContent: 'center', fontSize: 40,
                }}>💖</div>
            )}
            <div style={{ fontWeight: 900, fontSize: 20, color: palette.text, marginBottom: 6 }}>{person.short}</div>
            {person.name && <div style={{ fontSize: 13, color: palette.muted, marginBottom: 10 }}>{person.name}</div>}
            {(person.father || person.mother) && (
                <div style={{ fontSize: 12, color: palette.muted, lineHeight: 1.8 }}>
                    <p>Putra/i dari:</p>
                    {person.father && <p style={{ color: palette.text }}>{person.father}</p>}
                    {person.mother && <p style={{ color: palette.text }}>{person.mother}</p>}
                </div>
            )}
        </motion.div>
    );

    if (!groom.name && !bride.name) return null;
    return (
        <FadeUp>
            <div style={{ ...glassCard, marginTop: 32 }}>
                <SectionTitle subtitle="The Couple" title="Mempelai" />
                <div style={{ display: 'flex', gap: 16, flexWrap: 'wrap' }}>
                    <CardProfile person={groom} delay={0} />
                    <CardProfile person={bride} delay={0.15} />
                </div>
            </div>
        </FadeUp>
    );
}

// ─── GALLERY ──────────────────────────────────────────────────
function GallerySection({ photos }) {
    if (!photos || photos.length === 0) return null;
    return (
        <FadeUp>
            <div style={{ ...glassCard, marginTop: 32, overflow: 'hidden' }}>
                <SectionTitle subtitle="Gallery" title="Foto Kenangan" accent="Swipe untuk melihat koleksi foto kami" />
                <Swiper
                    modules={[Pagination, Autoplay, EffectFade]}
                    effect="fade"
                    pagination={{ clickable: true }}
                    autoplay={{ delay: 3500, disableOnInteraction: false }}
                    loop={photos.length > 1}
                    spaceBetween={0}
                    slidesPerView={1}
                    style={{ borderRadius: 20 }}
                >
                    {photos.map((url, i) => (
                        <SwiperSlide key={i}>
                            <motion.div whileHover={{ scale: 1.02 }} transition={{ duration: 0.4 }}>
                                <LazyImage
                                    src={url} alt={`Gallery ${i + 1}`}
                                    style={{ width: '100%', height: 360, borderRadius: 20 }}
                                    imgStyle={{ borderRadius: 20, objectFit: 'cover' }}
                                />
                            </motion.div>
                        </SwiperSlide>
                    ))}
                </Swiper>
            </div>
        </FadeUp>
    );
}

// ─── DETAIL ACARA ─────────────────────────────────────────────
function DetailAcara({ content }) {
    const blocks = [
        {
            label: 'Akad Nikah', emoji: '🤍',
            date: formatDate(content.akad_datetime),
            time: formatTime(content.akad_datetime),
            venue: content.akad_venue,
            address: content.akad_address,
        },
        {
            label: 'Resepsi', emoji: '🎊',
            date: formatDate(content.reception_datetime),
            time: formatTime(content.reception_datetime),
            venue: content.reception_venue,
            address: content.reception_address,
        },
    ].filter(b => b.date || b.venue);

    if (!blocks.length) return null;
    return (
        <FadeUp>
            <div style={{ ...glassCard, marginTop: 32 }}>
                <SectionTitle subtitle="Schedule" title="Detail Acara" accent="Waktu & lokasi rangkaian prosesi" />
                <div style={{ display: 'flex', flexDirection: 'column', gap: 18 }}>
                    {blocks.map((block, i) => (
                        <motion.div
                            key={i}
                            whileHover={{ scale: 1.02, boxShadow: `0 0 30px rgba(255,113,200,0.15)` }}
                            transition={{ duration: 0.3 }}
                            style={{
                                borderRadius: 22, padding: '22px 20px',
                                border: '1px solid rgba(255,255,255,0.1)',
                                background: 'rgba(255,255,255,0.03)',
                            }}
                        >
                            <div style={{ display: 'flex', alignItems: 'center', gap: 10, marginBottom: 12 }}>
                                <span style={{ fontSize: 24 }}>{block.emoji}</span>
                                <span style={{ fontSize: 12, letterSpacing: 4, textTransform: 'uppercase', color: palette.neonAlt }}>
                                    {block.label}
                                </span>
                            </div>
                            {block.date && <p style={{ fontSize: 18, fontWeight: 800, color: palette.text }}>{block.date}</p>}
                            {block.time && <p style={{ color: palette.muted, marginTop: 4, fontSize: 14 }}>Pukul {block.time} WIB</p>}
                            {block.venue && <p style={{ marginTop: 10, fontWeight: 700, color: palette.text }}>{block.venue}</p>}
                            {block.address && <p style={{ color: palette.muted, fontSize: 13, marginTop: 4 }}>{block.address}</p>}
                        </motion.div>
                    ))}
                </div>
                {content.maps_link && (
                    <div style={{ marginTop: 24, textAlign: 'center' }}>
                        <ActionButton onClick={() => window.open(content.maps_link, '_blank')} style={{ width: '100%' }}>
                            📍 Buka Google Maps
                        </ActionButton>
                    </div>
                )}
            </div>
        </FadeUp>
    );
}

// ─── QUOTE ────────────────────────────────────────────────────
function QuoteSection({ quote }) {
    if (!quote) return null;
    return (
        <FadeUp>
            <motion.div
                style={{ ...glassCard, marginTop: 32, textAlign: 'center' }}
            >
                <motion.div
                    animate={{ scale: [1, 1.03, 1] }}
                    transition={{ duration: 4, repeat: Infinity }}
                    style={{ fontSize: 40, marginBottom: 16 }}
                >
                    ✨
                </motion.div>
                <p style={{ fontStyle: 'italic', color: palette.muted, lineHeight: 1.9, fontSize: 16 }}>
                    "{quote}"
                </p>
            </motion.div>
        </FadeUp>
    );
}

// ─── CLOSING ─────────────────────────────────────────────────
function Closing({ content }) {
    return (
        <FadeUp>
            <div style={{
                ...glassCard, marginTop: 32, textAlign: 'center',
                background: 'rgba(255,113,200,0.04)',
                border: '1px solid rgba(255,113,200,0.15)',
            }}>
                <motion.div
                    animate={{ scale: [1, 1.05, 1] }}
                    transition={{ duration: 3, repeat: Infinity }}
                    style={{ fontSize: 42, marginBottom: 16 }}
                >
                    💍
                </motion.div>
                <p style={{ fontSize: 22, fontWeight: 900, color: palette.text }}>
                    {content.groom_short_name || content.groom_name?.split(' ')[0]}
                    {' & '}
                    {content.bride_short_name || content.bride_name?.split(' ')[0]}
                </p>
                <p style={{ marginTop: 14, color: palette.muted, lineHeight: 1.8, fontSize: 14 }}>
                    {content.closing_message || 'Merupakan suatu kebahagiaan dan kehormatan bagi kami apabila Bapak/Ibu/Saudara/i berkenan hadir dan memberikan doa restu.'}
                </p>
                <p style={{ marginTop: 24, fontSize: 11, letterSpacing: 4, color: palette.neonAlt, textTransform: 'uppercase' }}>
                    Made with 💖 by Anggita Wedding Organizer
                </p>
            </div>
        </FadeUp>
    );
}

// ─── MAIN EXPORT ──────────────────────────────────────────────
export default function Tema2Template({ invitation }) {
    const content = invitation?.content ?? {};
    const [coverOpen, setCoverOpen] = useState(true);

    const gallery = useMemo(() => {
        let urls = content.media_files?.gallery || content.media_files?.galeri || content.gallery_photo_urls || [];
        if (!Array.isArray(urls) && urls) urls = [urls];
        return Array.isArray(urls) ? urls.map(u => u.startsWith('http') ? u : `/storage/${u}`) : [];
    }, [content.media_files, content.gallery_photo_urls]);

    const loveStory = useMemo(() => {
        const stories = content.love_story ?? [];
        return Array.isArray(stories) ? stories : [];
    }, [content.love_story]);

    const bankAccounts = useMemo(() => {
        const accounts = content.bank_accounts ?? [];
        return Array.isArray(accounts) ? accounts : [];
    }, [content.bank_accounts]);

    const guestbook = useMemo(() => {
        const entries = content.guestbook ?? [];
        return Array.isArray(entries) ? entries : [];
    }, [content.guestbook]);

    const openWithMusic = () => {
        setCoverOpen(false);
        setTimeout(() => { window.__tema2PlayMusic?.(); }, 300);
    };

    const openSilent = () => setCoverOpen(false);

    return (
        <div style={{
            minHeight: '100vh',
            background: palette.gradient,
            color: palette.text,
            fontFamily: "'Space Grotesk', 'Poppins', system-ui, sans-serif",
            position: 'relative',
            overflowX: 'hidden',
        }}>
            <FloatingOrbs />

            {/* Star dust texture */}
            <div style={{
                position: 'fixed', inset: 0,
                background: `url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='300' height='300'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.65' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='300' height='300' filter='url(%23noise)' opacity='0.04'/%3E%3C/svg%3E")`,
                pointerEvents: 'none', zIndex: 0, opacity: 0.5,
            }} />

            <CoverScreen
                content={content}
                onOpen={openWithMusic}
                onOpenSilent={openSilent}
                coverOpen={coverOpen}
            />

            <MusicPlayer musicUrl={content.music_file_url} coverClosed={!coverOpen} />

            {/* Main content (visible after cover closes) */}
            <div style={{
                maxWidth: 660, margin: '0 auto',
                padding: '40px 20px 100px',
                position: 'relative', zIndex: 2,
            }}>
                {/* HERO */}
                <FadeUp>
                    <div style={{
                        ...glassCard,
                        minHeight: '70vh',
                        display: 'flex', flexDirection: 'column',
                        alignItems: 'center', justifyContent: 'center',
                        textAlign: 'center',
                        marginTop: 40,
                        position: 'relative', overflow: 'hidden',
                    }}>
                        <div style={{
                            position: 'absolute', inset: 0,
                            background: 'linear-gradient(135deg, rgba(255,113,200,0.12), rgba(108,254,255,0.08))',
                            borderRadius: 28,
                        }} />
                        <div style={{ position: 'relative', zIndex: 1 }}>
                            <motion.p
                                animate={{ opacity: [0.6, 1, 0.6] }}
                                transition={{ duration: 2.5, repeat: Infinity }}
                                style={{ letterSpacing: 8, textTransform: 'uppercase', color: palette.neonAlt, fontSize: 12 }}
                            >
                                The Wedding of
                            </motion.p>
                            <motion.div
                                initial={{ opacity: 0, y: 30 }}
                                animate={{ opacity: 1, y: 0 }}
                                transition={{ duration: 1, delay: 0.3 }}
                                style={{ marginTop: 20 }}
                            >
                                <div style={{ fontSize: 52, fontWeight: 900, lineHeight: 1.1 }}>
                                    {content.groom_short_name || content.groom_name?.split(' ')[0]}
                                </div>
                                <motion.div
                                    animate={{ y: [0, -8, 0], scale: [1, 1.15, 1] }}
                                    transition={{ duration: 1.5, repeat: Infinity }}
                                    style={{ fontSize: 38, color: palette.neonAlt, margin: '10px 0' }}
                                >
                                    &amp;
                                </motion.div>
                                <div style={{ fontSize: 52, fontWeight: 900, lineHeight: 1.1 }}>
                                    {content.bride_short_name || content.bride_name?.split(' ')[0]}
                                </div>
                            </motion.div>
                            {formatDate(content.reception_datetime) && (
                                <motion.p
                                    initial={{ opacity: 0 }}
                                    animate={{ opacity: 1 }}
                                    transition={{ delay: 0.7 }}
                                    style={{ marginTop: 20, fontSize: 17, fontWeight: 700, color: palette.text }}
                                >
                                    {formatDate(content.reception_datetime)}
                                </motion.p>
                            )}
                            {formatTime(content.reception_datetime) && (
                                <p style={{ color: palette.muted, marginTop: 4 }}>
                                    Pukul {formatTime(content.reception_datetime)} WIB
                                </p>
                            )}
                            {content.reception_venue && (
                                <p style={{ color: palette.muted, marginTop: 6, fontSize: 14 }}>
                                    {content.reception_venue}
                                </p>
                            )}
                        </div>
                    </div>
                </FadeUp>

                <QuoteSection quote={content.opening_quote} />
                <ProfileSection content={content} />
                <DetailAcara content={content} />
                <CountdownTimer
                    targetDate={content.reception_datetime}
                    title={`Pernikahan ${content.groom_short_name || ''} & ${content.bride_short_name || ''}`}
                    location={content.reception_venue}
                />
                <GallerySection photos={gallery} />
                <LoveStory stories={loveStory} />
                <RSVPSection invitationSlug={invitation?.slug} rsvpEnabled={!!content.rsvp_enabled} />
                <Guestbook invitationSlug={invitation?.slug} initialEntries={guestbook} />
                <AmplopDigital bankAccounts={bankAccounts} qrisImageUrl={content.qris_image_url} />
                <Closing content={content} />
            </div>
        </div>
    );
}
