import React, { useState, useEffect, useRef } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import { GroomSection, BrideSection, JourneySection, SaveDateSection, EventSection } from './sections1.jsx';
import { GallerySection, GuestbookSection, GiftSection, ClosingSection } from './sections2.jsx';
import { palette, fonts, formatDate } from './utils.js';
import { ActionButton } from './components.jsx';
import '@fortawesome/fontawesome-free/css/all.min.css';

export default function Tema1({ invitation }) {
    const [opened, setOpened] = useState(false);
    const [playing, setPlaying] = useState(false);
    const [activePage, setActivePage] = useState('quote');
    const audioRef = useRef(null);
    
    // Extract guest name from URL
    const searchParams = new URLSearchParams(window.location.search);
    const toName = searchParams.get('to') || 'Tamu Undangan';

    // Provide default data for preview mode if empty
    const data = {
        groom_name: 'Adam',
        bride_name: 'Hawa',
        groom_short_name: 'Adam',
        bride_short_name: 'Hawa',
        groom_father: 'Bapak',
        groom_mother: 'Ibu',
        bride_father: 'Bapak',
        bride_mother: 'Ibu',
        akad_datetime: '2025-12-12T08:00:00',
        akad_venue: 'Masjid Agung',
        akad_address: 'Jl. Merdeka No.1',
        reception_datetime: '2025-12-12T11:00:00',
        reception_venue: 'Gedung Serbaguna',
        reception_address: 'Jl. Pemuda No.10',
        maps_link: '#',
        love_story: 'Berawal dari teman biasa, hingga akhirnya kami mengikat janji suci.',
        opening_quote: 'Dan di antara tanda-tanda kekuasaan-Nya ialah Dia menciptakan untukmu isteri-isteri dari jenismu sendiri...',
        closing_message: 'Merupakan suatu kehormatan dan kebahagiaan bagi kami apabila Bapak/Ibu/Saudara/i berkenan hadir di acara pernikahan kami.',
        hashtag: '#AdamHawa2025',
        thumbnail: 'https://images.unsplash.com/photo-1511285560929-80b456fea0bc?auto=format&fit=crop&q=80',
        photo_prewedding_url: 'https://images.unsplash.com/photo-1519741497674-611481863552?auto=format&fit=crop&q=80',
        music_file_url: null,
        media_files: {},
        ...(invitation?.content || {})
    };

    const resolveMediaUrl = (value) => {
        if (!value) return null;
        const raw = Array.isArray(value) ? value[0] : value;
        const src = String(raw);
        if (src.startsWith('http')) return src;
        if (src.startsWith('/storage/')) return src;
        return `/storage/${src.replace(/^\/+/, '')}`;
    };

    const hasMusic = !!data.music_file_url;
    // Cover image is the first cover slot upload if available, else prewedding demo/thumbnail
    const coverImg = resolveMediaUrl(data?.media_files?.cover) || data.photo_prewedding_url || resolveMediaUrl(data.thumbnail);
    const mainBgImg = resolveMediaUrl(data?.media_files?.main_background) || coverImg;
    const quoteImage = resolveMediaUrl(data?.media_files?.quote_photo) || resolveMediaUrl(data?.media_files?.story_photo_1) || data.photo_prewedding_url || mainBgImg;
    const hasJourney = Array.isArray(data.love_story)
        ? data.love_story.length > 0
        : typeof data.love_story === 'string' && data.love_story.trim().length > 0;
    const hasGift = (!!data.bank_accounts && data.bank_accounts.length > 0) || !!data.qris_image;

    const galleryRaw = data?.media_files?.gallery || data?.media_files?.galeri || data.gallery_photo_urls || data.demo_gallery || [];
    const galleryImages = (Array.isArray(galleryRaw) ? galleryRaw : [galleryRaw])
        .map((item) => resolveMediaUrl(item))
        .filter(Boolean);

    const pageBackgroundRaw = data?.media_files?.page_backgrounds || data?.media_files?.story_backgrounds || [];
    const customPageBackgrounds = (Array.isArray(pageBackgroundRaw) ? pageBackgroundRaw : [pageBackgroundRaw])
        .map((item) => resolveMediaUrl(item))
        .filter(Boolean);

    const pageKeys = [
        'quote',
        'groom',
        'bride',
        ...(hasJourney ? ['journey'] : []),
        'savedate',
        'events',
        'rsvp',
        ...(hasGift ? ['gift'] : []),
        'gallery',
        'closing'
    ];
    const backgroundPool = [
        ...customPageBackgrounds,
        resolveMediaUrl(data?.media_files?.main_background),
        coverImg,
        quoteImage,
        resolveMediaUrl(data?.media_files?.story_photo_1),
        resolveMediaUrl(data?.media_files?.story_photo_2),
        resolveMediaUrl(data.photo_prewedding_url),
        ...galleryImages,
        resolveMediaUrl(data.thumbnail)
    ].filter(Boolean);
    const uniqueBackgroundPool = [...new Set(backgroundPool)];

    const pageBackgrounds = pageKeys.reduce((acc, key, index) => {
        const fallback = uniqueBackgroundPool[uniqueBackgroundPool.length - 1] || mainBgImg;
        acc[key] = uniqueBackgroundPool[index] || fallback;
        return acc;
    }, {});

    const activeBgImg = pageBackgrounds[activePage] || mainBgImg;

    useEffect(() => {
        if (hasMusic && audioRef.current) {
            audioRef.current.volume = 0.5;
            audioRef.current.loop = true;
        }
    }, [hasMusic]);

    useEffect(() => {
        if (!opened) return;

        const pageNodes = Array.from(document.querySelectorAll('[data-page-key]'));
        if (!pageNodes.length) return;

        setActivePage(pageNodes[0].getAttribute('data-page-key') || 'quote');

        const observer = new IntersectionObserver(
            (entries) => {
                const visible = entries
                    .filter((entry) => entry.isIntersecting)
                    .sort((a, b) => b.intersectionRatio - a.intersectionRatio)[0];

                if (!visible) return;
                const key = visible.target.getAttribute('data-page-key');
                if (key) setActivePage(key);
            },
            {
                threshold: [0.45, 0.6, 0.75],
                rootMargin: '-8% 0px -20% 0px'
            }
        );

        pageNodes.forEach((node) => observer.observe(node));

        return () => observer.disconnect();
    }, [opened]);

    const handleOpen = () => {
        setOpened(true);
        if (hasMusic && audioRef.current) {
            audioRef.current.play().then(() => setPlaying(true)).catch(e => console.log("Audio play blocked", e));
        }
        document.body.style.overflow = 'auto'; // allow scrolling after open
        document.body.setAttribute('data-tema1-opened', 'true');
    };

    const togglePlay = () => {
        if (!audioRef.current) return;
        if (playing) {
            audioRef.current.pause();
            setPlaying(false);
        } else {
            audioRef.current.play();
            setPlaying(true);
        }
    };

    // Lock body scroll initially
    useEffect(() => {
        document.body.style.overflow = 'hidden';
        document.body.style.overflowX = 'hidden';
        document.body.style.backgroundColor = palette.darkFill;
        document.body.style.color = palette.text;
        document.body.style.margin = 0;
        return () => {
            document.body.style.overflow = 'auto';
            document.body.style.overflowX = '';
            document.body.style.backgroundColor = '';
            document.body.style.color = '';
            document.body.removeAttribute('data-tema1-opened');
        };
    }, []);

    useEffect(() => {
        if (opened) {
            document.body.setAttribute('data-tema1-opened', 'true');
        } else {
            document.body.removeAttribute('data-tema1-opened');
        }
    }, [opened]);

    return (
        <div style={{ fontFamily: fonts.sans, minHeight: '100vh', width: '100%', overflowX: 'hidden', MozOsxFontSmoothing: 'grayscale', WebkitFontSmoothing: 'antialiased' }}>
            <style>{`
                @font-face {
                    font-family: 'The Seasons Light';
                    font-weight: 400;
                    src: url('https://groovepublic.com/wp-content/uploads/2026/03/The-Seasons-Light.ttf') format('truetype');
                }
                @font-face {
                    font-family: 'Humnst777 Lt BT Light';
                    font-weight: 400;
                    src: url('https://groovepublic.com/wp-content/uploads/2026/03/Humnst777-Lt-BT-Light.ttf') format('truetype');
                }
                @font-face {
                    font-family: 'Aston Script';
                    font-weight: 400;
                    src: url('https://groovepublic.com/wp-content/uploads/2026/03/Aston-Script.ttf') format('truetype');
                }
                @font-face {
                    font-family: 'CormorantGaramond-Regular';
                    font-weight: 400;
                    src: url('https://groovepublic.com/wp-content/uploads/2026/03/CormorantGaramond-Regular.ttf') format('truetype');
                }
                @font-face {
                    font-family: 'lacheyard_script';
                    font-weight: 400;
                    src: url('https://groovepublic.com/wp-content/uploads/2026/03/LacheyardScript_PERSONAL_USE_ONLY.ttf') format('truetype');
                }

                html,
                body {
                    scroll-behavior: smooth;
                }

                body[data-tema1-opened='true'] {
                    scroll-snap-type: y mandatory;
                }

                [data-page-key] {
                    scroll-snap-align: start;
                    scroll-snap-stop: always;
                    min-height: 100svh;
                }
            `}</style>
            {hasMusic && <audio ref={audioRef} src={data.music_file_url} />}

            {/* Cinematic Opening Cover (Fixed & Fullscreen) */}
            <AnimatePresence>
                {!opened && (
                    <motion.div
                        initial={{ opacity: 1 }}
                        exit={{ opacity: 0, scale: 1.05, filter: 'blur(10px)' }}
                        transition={{ duration: 1.2, ease: [0.16, 1, 0.3, 1] }}
                        style={{
                            position: 'fixed', inset: 0, zIndex: 9999,
                            backgroundImage: `url(${coverImg})`,
                            backgroundSize: 'cover', backgroundPosition: 'center',
                            display: 'flex', flexDirection: 'column', alignItems: 'center', justifyContent: 'center'
                        }}
                    >
                        <div style={{
                            position: 'absolute', inset: 0,
                            background: 'linear-gradient(to bottom, rgba(0,0,0,0.2) 0%, rgba(0,0,0,0.8) 80%, rgba(5,5,5,1) 100%)'
                        }} />
                        
                        <div style={{
                            position: 'relative', zIndex: 1, textAlign: 'center',
                            width: '100%', padding: '0 20px', marginTop: 'auto', marginBottom: '10vh'
                        }}>
                            <motion.p
                                initial={{ opacity: 0, y: 10 }} animate={{ opacity: 1, y: 0 }} transition={{ delay: 0.3, duration: 1 }}
                                style={{ fontFamily: fonts.sans, fontSize: 12, letterSpacing: '3px', textTransform: 'uppercase', color: palette.accent, marginBottom: 12 }}
                            >
                                WE INVITE YOU TO CELEBRATE
                            </motion.p>

                            <motion.div
                                initial={{ opacity: 0, y: 20 }} animate={{ opacity: 1, y: 0 }} transition={{ delay: 0.5, duration: 1.2 }}
                                style={{ margin: '0 0 20px' }}
                            >
                                <h1 style={{ fontFamily: fonts.serif, fontSize: 'clamp(2.6rem, 10vw, 4rem)', fontWeight: 400, color: '#fff', margin: 0, textShadow: '0 4px 20px rgba(0,0,0,0.5)' }}>
                                    {data.groom_short_name || data.groom_name?.split(' ')[0]}
                                </h1>
                                <div style={{ fontFamily: fonts.sans, fontSize: 14, letterSpacing: '4px', textTransform: 'uppercase', color: palette.accent, margin: '8px 0' }}>
                                    and
                                </div>
                                <h1 style={{ fontFamily: fonts.serif, fontSize: 'clamp(2.6rem, 10vw, 4rem)', fontWeight: 400, color: '#fff', margin: 0, textShadow: '0 4px 20px rgba(0,0,0,0.5)' }}>
                                    {data.bride_short_name || data.bride_name?.split(' ')[0]}
                                </h1>
                            </motion.div>

                            <motion.p
                                initial={{ opacity: 0, y: 10 }} animate={{ opacity: 1, y: 0 }} transition={{ delay: 0.7, duration: 1 }}
                                style={{ fontFamily: fonts.sans, fontSize: 12, letterSpacing: '2px', color: palette.accent, marginBottom: 12 }}
                            >
                                ({formatDate(data.reception_datetime)})
                            </motion.p>

                            <motion.p
                                initial={{ opacity: 0, y: 10 }} animate={{ opacity: 1, y: 0 }} transition={{ delay: 0.85, duration: 1 }}
                                style={{ fontFamily: fonts.sans, fontSize: 12, letterSpacing: '2px', textTransform: 'uppercase', color: palette.text, lineHeight: 1.6, margin: '0 0 20px' }}
                            >
                                we make an eternal vow<br />AT THIS MOMENT
                            </motion.p>

                            {toName && (
                                <motion.div
                                    initial={{ opacity: 0 }} animate={{ opacity: 1 }} transition={{ delay: 1, duration: 1 }}
                                    style={{ margin: '30px 0 40px' }}
                                >
                                    <p style={{ fontFamily: fonts.sans, fontSize: 11, color: palette.muted, letterSpacing: '2px', textTransform: 'uppercase', margin: '0 0 8px' }}>
                                        Kepada Yth. Bapak/Ibu/Saudara/i
                                    </p>
                                    <p style={{ fontFamily: fonts.serif, fontSize: 24, color: palette.text, margin: 0 }}>
                                        {toName}
                                    </p>
                                    <p style={{ fontFamily: fonts.sans, fontSize: 10, color: 'rgba(255,255,255,0.4)', marginTop: 8 }}>
                                        *Mohon maaf jika ada kesalahan ejaan nama/gelar.
                                    </p>
                                </motion.div>
                            )}
                            
                            <motion.div initial={{ opacity: 0, y: 20 }} animate={{ opacity: 1, y: 0 }} transition={{ delay: 1.2, duration: 1 }}>
                                <ActionButton onClick={handleOpen} style={{ padding: '16px 40px', fontSize: 11, letterSpacing: '3px' }}>
                                    <i className="fas fa-envelope-open" style={{ marginRight: 8 }}></i> Buka Undangan
                                </ActionButton>
                            </motion.div>
                        </div>
                    </motion.div>
                )}
            </AnimatePresence>

            {/* Main Content (Scrollable) */}
            {opened && (
                <>
                    <div
                        style={{
                            position: 'fixed',
                            inset: 0,
                            zIndex: 0,
                            pointerEvents: 'none',
                            backgroundImage: `url(${mainBgImg})`,
                            backgroundSize: 'cover',
                            backgroundPosition: 'center center',
                            backgroundRepeat: 'no-repeat'
                        }}
                    />
                    <div
                        style={{
                            position: 'fixed',
                            inset: 0,
                            zIndex: 0,
                            pointerEvents: 'none',
                            background: 'rgba(0,0,0,0.66)'
                        }}
                    />
                    <motion.div
                        initial={{ opacity: 0 }}
                        animate={{ opacity: 1 }}
                        transition={{ duration: 1 }}
                        style={{ position: 'relative', zIndex: 1, background: 'transparent', overflowX: 'hidden' }}
                    >
                    {/* Intro / Quote */}
                    <section data-page-key="quote" style={{ padding: 'clamp(80px, 12vw, 120px) 20px', textAlign: 'center', maxWidth: 800, margin: '0 auto', display: 'flex', alignItems: 'center', justifyContent: 'center' }}>
                        <div style={{ width: '100%' }}>
                            <motion.h2
                                initial={{ opacity: 0, y: 24 }}
                                whileInView={{ opacity: 1, y: 0 }}
                                viewport={{ once: true }}
                                transition={{ duration: 0.9 }}
                                style={{ margin: 0, fontFamily: fonts.script, fontSize: 'clamp(2.2rem, 12vw, 4.8rem)', color: palette.text, fontWeight: 400 }}
                            >
                                Ar-Rum : 21
                            </motion.h2>

                            <motion.div
                                initial={{ opacity: 0, y: 24 }}
                                whileInView={{ opacity: 1, y: 0 }}
                                viewport={{ once: true }}
                                transition={{ duration: 0.95, delay: 0.08 }}
                                style={{ margin: '24px auto 20px', width: 'min(68vw, 290px)', aspectRatio: '4 / 5', overflow: 'hidden' }}
                            >
                                <img src={quoteImage} alt="quote" style={{ width: '100%', height: '100%', objectFit: 'cover' }} />
                            </motion.div>

                            {data.opening_quote && (
                                <motion.div
                                    initial={{ opacity: 0, y: 30 }} whileInView={{ opacity: 1, y: 0 }} viewport={{ once: true }} transition={{ duration: 1 }}
                                    style={{ fontFamily: fonts.serif, fontSize: '1.3rem', color: palette.text, fontStyle: 'italic', lineHeight: 1.7 }}
                                >
                                    "{data.opening_quote}"
                                </motion.div>
                            )}
                            <p style={{ margin: '20px 0 0', fontFamily: fonts.sans, letterSpacing: '4px', textTransform: 'uppercase', color: palette.accent, fontSize: 12 }}>
                                {data.bride_short_name} &amp; {data.groom_short_name}
                            </p>
                        </div>
                    </section>

                    <div data-page-key="groom">
                        <GroomSection data={data} />
                    </div>
                    <div data-page-key="bride">
                        <BrideSection data={data} />
                    </div>
                    {hasJourney && (
                        <div data-page-key="journey">
                            <JourneySection data={data} />
                        </div>
                    )}
                    <div data-page-key="savedate">
                        <SaveDateSection data={data} />
                    </div>
                    <div data-page-key="events">
                        <EventSection data={data} />
                    </div>
                    <div data-page-key="rsvp">
                        <GuestbookSection data={data} />
                    </div>
                    {hasGift && (
                        <div data-page-key="gift">
                            <GiftSection data={data} />
                        </div>
                    )}
                    <div data-page-key="gallery">
                        <GallerySection data={data} />
                    </div>
                    <div data-page-key="closing">
                        <ClosingSection data={data} />
                    </div>
                    </motion.div>
                </>
            )}

            {/* Floating Music Toggle */}
            <AnimatePresence>
                {opened && hasMusic && (
                    <motion.button
                        initial={{ opacity: 0, scale: 0 }}
                        animate={{ opacity: 1, scale: 1 }}
                        exit={{ opacity: 0, scale: 0 }}
                        whileHover={{ scale: 1.1 }}
                        whileTap={{ scale: 0.9 }}
                        onClick={togglePlay}
                        style={{
                            position: 'fixed', bottom: 30, right: 30, zIndex: 50,
                            width: 50, height: 50, borderRadius: '50%',
                            background: palette.darkFillAlt,
                            border: `1px solid ${palette.border}`, color: palette.accent,
                            display: 'flex', alignItems: 'center', justifyContent: 'center',
                            cursor: 'pointer', boxShadow: '0 4px 12px rgba(0,0,0,0.5)',
                            outline: 'none', transition: 'border-color 0.3s'
                        }}
                    >
                        <i className={`fas ${playing ? 'fa-pause' : 'fa-play'}`}></i>
                    </motion.button>
                )}
            </AnimatePresence>
        </div>
    );
}
