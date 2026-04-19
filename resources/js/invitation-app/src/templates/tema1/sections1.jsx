import React, { useEffect, useState } from 'react';
import { motion } from 'framer-motion';
import { palette, fonts, formatDate, formatTime, generateGoogleCalendarUrl } from './utils.js';

const resolveMediaUrl = (value, fallback = null) => {
    if (!value) return fallback;
    const raw = Array.isArray(value) ? value[0] : value;
    const src = String(raw);
    if (src.startsWith('http')) return src;
    if (src.startsWith('/storage/')) return src;
    return `/storage/${src.replace(/^\/+/, '')}`;
};

const profileWrapStyle = {
    background: 'transparent',
    minHeight: '100svh',
    padding: 'clamp(80px, 11vw, 120px) 20px',
    display: 'flex',
    alignItems: 'center'
};

function PersonPage({ role, image, name, father, mother, instagram }) {
    return (
        <section style={profileWrapStyle}>
            <div style={{ maxWidth: 560, margin: '0 auto', textAlign: 'center', width: '100%' }}>
                <motion.div
                    initial={{ opacity: 0, y: 20 }}
                    whileInView={{ opacity: 1, y: 0 }}
                    viewport={{ once: true, amount: 0.35 }}
                    transition={{ duration: 1 }}
                >
                    <p style={{ fontFamily: fonts.sans, fontSize: 12, letterSpacing: '4px', textTransform: 'uppercase', color: palette.accent, margin: '0 0 16px' }}>
                        {role}
                    </p>
                    <div style={{ margin: '0 auto 20px', width: 'min(64vw, 300px)', aspectRatio: '4 / 5', overflow: 'hidden' }}>
                        <img src={image} alt={name} style={{ width: '100%', height: '100%', objectFit: 'cover' }} />
                    </div>
                    <h3 style={{ fontFamily: fonts.serif, fontSize: 'clamp(2.3rem, 10vw, 4.4rem)', fontWeight: 400, color: palette.text, margin: 0, lineHeight: 0.92 }}>
                        {name}
                    </h3>
                    <p style={{ fontFamily: fonts.sans, fontSize: 14, color: palette.muted, lineHeight: 1.75, margin: '16px 0 0' }}>
                        {role === 'THE GROOM' ? 'Putra' : 'Putri'} dari<br />Bpk. {father} & Ibu {mother}
                    </p>
                    {instagram && (
                        <a
                            href={`https://instagram.com/${instagram.replace('@', '')}`}
                            target="_blank"
                            rel="noreferrer"
                            style={{
                                display: 'inline-flex', alignItems: 'center', justifyContent: 'center', gap: 8,
                                marginTop: 24, minWidth: 210,
                                fontFamily: fonts.sans, fontSize: 12, color: palette.text, letterSpacing: '1px',
                                textDecoration: 'none', border: `1px solid ${palette.border}`,
                                borderRadius: 999, padding: '10px 18px', textTransform: 'uppercase'
                            }}
                        >
                            <i className="fab fa-instagram"></i> {instagram}
                        </a>
                    )}
                </motion.div>
            </div>
        </section>
    );
}

export function GroomSection({ data }) {
    const groomImg = resolveMediaUrl(data?.media_files?.groom_profile) || data.groom_photo_url || data.photo_prewedding_url || resolveMediaUrl(data.thumbnail);
    return (
        <section id="groom">
            <PersonPage
                role="THE GROOM"
                image={groomImg}
                name={data.groom_name}
                father={data.groom_father}
                mother={data.groom_mother}
                instagram={data.groom_instagram}
            />
        </section>
    );
}

export function BrideSection({ data }) {
    const brideImg = resolveMediaUrl(data?.media_files?.bride_profile) || data.bride_photo_url || data.photo_prewedding_url || resolveMediaUrl(data.thumbnail);
    return (
        <section id="bride">
            <PersonPage
                role="THE BRIDE"
                image={brideImg}
                name={data.bride_name}
                father={data.bride_father}
                mother={data.bride_mother}
                instagram={data.bride_instagram}
            />
        </section>
    );
}

const normalizeStories = (loveStory) => {
    if (Array.isArray(loveStory)) return loveStory;
    if (typeof loveStory !== 'string' || !loveStory.trim()) return [];

    const blocks = loveStory
        .split(/\n\s*\n/)
        .map((block) => block.trim())
        .filter(Boolean);

    return blocks.map((description, idx) => ({
        title: idx === 0 ? 'Our First Story' : `Chapter ${idx + 1}`,
        description
    }));
};

export function JourneySection({ data }) {
    const stories = normalizeStories(data.love_story).slice(0, 4);
    if (!stories.length) return null;

    const leftImage = resolveMediaUrl(data?.media_files?.story_photo_1 || data.photo_prewedding_url || data.thumbnail, data.thumbnail);
    const rightImage = resolveMediaUrl(data?.media_files?.story_photo_2 || data.thumbnail, data.thumbnail);

    return (
        <section id="journey" style={{ background: 'transparent', minHeight: '100svh', padding: 'clamp(70px, 10vw, 110px) 20px', display: 'flex', alignItems: 'center' }}>
            <div style={{ width: '100%', maxWidth: 760, margin: '0 auto' }}>
                <div style={{ display: 'grid', gridTemplateColumns: 'repeat(2, minmax(0, 1fr))', gap: 12, marginBottom: 24 }}>
                    <img src={leftImage} alt="story-left" style={{ width: '100%', aspectRatio: '4 / 3', objectFit: 'cover', display: 'block' }} />
                    <img src={rightImage} alt="story-right" style={{ width: '100%', aspectRatio: '4 / 3', objectFit: 'cover', display: 'block' }} />
                </div>

                <motion.h3
                    initial={{ opacity: 0, y: 20 }}
                    whileInView={{ opacity: 1, y: 0 }}
                    viewport={{ once: true, amount: 0.35 }}
                    transition={{ duration: 0.9 }}
                    style={{ fontFamily: fonts.serif, fontSize: 'clamp(1.8rem, 8vw, 3rem)', color: palette.text, fontWeight: 400, margin: '0 0 20px' }}
                >
                    The Journey of Two Souls in Love
                </motion.h3>

                <div style={{ display: 'grid', gap: 20 }}>
                    {stories.map((story, idx) => (
                        <motion.div
                            key={idx}
                            initial={{ opacity: 0, y: 14 }}
                            whileInView={{ opacity: 1, y: 0 }}
                            viewport={{ once: true, amount: 0.35 }}
                            transition={{ duration: 0.8, delay: idx * 0.08 }}
                        >
                            <p style={{ margin: 0, fontFamily: fonts.serif, fontSize: 32, color: palette.text }}>
                                {story.title || `Chapter ${idx + 1}`}
                            </p>
                            <p style={{ margin: '8px 0 0', fontFamily: fonts.sans, fontSize: 13, lineHeight: 1.8, color: palette.muted, whiteSpace: 'pre-line' }}>
                                {story.description || story.text || ''}
                            </p>
                        </motion.div>
                    ))}
                </div>
            </div>
        </section>
    );
}

const getRemaining = (targetDate) => {
    if (!targetDate) return null;
    const target = new Date(targetDate).getTime();
    if (Number.isNaN(target)) return null;
    const diff = target - Date.now();
    if (diff <= 0) return { days: 0, hours: 0, minutes: 0, seconds: 0 };
    return {
        days: Math.floor(diff / 86400000),
        hours: Math.floor((diff % 86400000) / 3600000),
        minutes: Math.floor((diff % 3600000) / 60000),
        seconds: Math.floor((diff % 60000) / 1000)
    };
};

export function SaveDateSection({ data }) {
    const [remaining, setRemaining] = useState(() => getRemaining(data.reception_datetime));

    useEffect(() => {
        const id = setInterval(() => setRemaining(getRemaining(data.reception_datetime)), 1000);
        return () => clearInterval(id);
    }, [data.reception_datetime]);

    const receptionCalendarUrl = generateGoogleCalendarUrl(`Resepsi ${data.groom_name} & ${data.bride_name}`, data.reception_datetime, data.reception_address, '');
    const saveImage = resolveMediaUrl(data?.media_files?.save_the_date || data.photo_prewedding_url || data.thumbnail, data.thumbnail);

    return (
        <section id="savedate" style={{ background: 'transparent', minHeight: '100svh', padding: 'clamp(70px, 10vw, 110px) 20px', display: 'flex', alignItems: 'center' }}>
            <div style={{ width: '100%', maxWidth: 560, margin: '0 auto', textAlign: 'center' }}>
                <img src={saveImage} alt="save-date" style={{ width: '100%', aspectRatio: '3 / 4', objectFit: 'cover', display: 'block' }} />

                <motion.h3
                    initial={{ opacity: 0, y: 20 }}
                    whileInView={{ opacity: 1, y: 0 }}
                    viewport={{ once: true, amount: 0.35 }}
                    transition={{ duration: 0.9 }}
                    style={{ margin: '24px 0 0', fontFamily: fonts.script, fontSize: 'clamp(2rem, 12vw, 3.8rem)', color: palette.text, fontWeight: 400, lineHeight: 0.95 }}
                >
                    Save The Date
                </motion.h3>

                <p style={{ margin: '12px 0 18px', fontFamily: fonts.sans, color: palette.muted, fontSize: 13 }}>
                    {`${data.groom_short_name} & ${data.bride_short_name} • ${formatDate(data.reception_datetime)}`}
                </p>

                {remaining && (
                    <div style={{ display: 'grid', gridTemplateColumns: 'repeat(4, minmax(0, 1fr))', gap: 6, marginBottom: 18 }}>
                        {[
                            { label: 'Hari', value: remaining.days },
                            { label: 'Jam', value: remaining.hours },
                            { label: 'Menit', value: remaining.minutes },
                            { label: 'Detik', value: remaining.seconds }
                        ].map((item) => (
                            <div key={item.label} style={{ border: `1px solid ${palette.border}`, padding: '10px 6px' }}>
                                <p style={{ margin: 0, fontFamily: fonts.serif, fontSize: 28, lineHeight: 1, color: palette.text }}>{String(item.value).padStart(2, '0')}</p>
                                <p style={{ margin: '6px 0 0', fontFamily: fonts.sans, fontSize: 10, textTransform: 'uppercase', letterSpacing: '1px', color: palette.muted }}>{item.label}</p>
                            </div>
                        ))}
                    </div>
                )}

                <a
                    href={receptionCalendarUrl}
                    target="_blank"
                    rel="noreferrer"
                    style={{
                        display: 'inline-flex',
                        alignItems: 'center',
                        justifyContent: 'center',
                        minWidth: 220,
                        border: `1px solid ${palette.border}`,
                        borderRadius: 999,
                        color: palette.text,
                        textDecoration: 'none',
                        padding: '12px 20px',
                        fontFamily: fonts.sans,
                        letterSpacing: '2px',
                        textTransform: 'uppercase'
                    }}
                >
                    Save The Date →
                </a>
            </div>
        </section>
    );
}

export function EventSection({ data }) {
    const eventTitle = 'Save Our Date';
    const akadCalendarUrl = generateGoogleCalendarUrl(`Akad Nikah ${data.groom_name} & ${data.bride_name}`, data.akad_datetime, data.akad_address, '');

    return (
        <section id="events" style={{
            position: 'relative',
            width: '100%',
            minHeight: '100vh',
            padding: 'clamp(80px, 12vw, 120px) 20px',
            background: 'transparent',
            display: 'flex',
            alignItems: 'center'
        }}>
            <div style={{ position: 'relative', zIndex: 1, width: '100%', maxWidth: 640, margin: '0 auto', textAlign: 'center' }}>
                <motion.h3
                    initial={{ opacity: 0, y: 20 }}
                    whileInView={{ opacity: 1, y: 0 }}
                    viewport={{ once: true }}
                    transition={{ duration: 0.9 }}
                    style={{
                        fontFamily: fonts.serif,
                        fontSize: 'clamp(1.8rem, 6vw, 2.5rem)',
                        fontWeight: 400,
                        color: palette.text,
                        margin: 0
                    }}
                >
                    {eventTitle}
                </motion.h3>

                <motion.div
                    initial={{ opacity: 0, y: 20 }}
                    whileInView={{ opacity: 1, y: 0 }}
                    viewport={{ once: true }}
                    transition={{ duration: 0.9, delay: 0.2 }}
                    style={{ marginBottom: 34 }}
                >
                    <h4 style={{ fontFamily: fonts.serif, fontSize: 'clamp(1.5rem, 5vw, 2rem)', color: palette.text, fontWeight: 400, margin: '0 0 10px' }}>
                        Akad nikah
                    </h4>
                    <p style={{ fontFamily: fonts.sans, fontSize: 14, color: palette.text, lineHeight: 1.8, margin: '0 0 8px', whiteSpace: 'pre-line' }}>
                        {`${formatDate(data.akad_datetime)}\n${formatTime(data.akad_datetime)} WIB - Selesai\n(${data.akad_venue || 'Lokasi acara'})`}
                    </p>
                    <p style={{ fontFamily: fonts.sans, fontSize: 13, color: palette.muted, lineHeight: 1.7, margin: '0 0 16px' }}>
                        {data.akad_address}
                    </p>
                    <a
                        href={data.maps_link || '#'}
                        target="_blank"
                        rel="noreferrer"
                        style={{
                            display: 'inline-block',
                            padding: '8px 16px',
                            border: `1px solid ${palette.border}`,
                            color: palette.text,
                            textDecoration: 'none',
                            fontFamily: fonts.sans,
                            fontSize: 11,
                            letterSpacing: '2px',
                            textTransform: 'uppercase'
                        }}
                    >
                        Google Maps
                    </a>
                </motion.div>

                <motion.div
                    initial={{ opacity: 0, y: 20 }}
                    whileInView={{ opacity: 1, y: 0 }}
                    viewport={{ once: true }}
                    transition={{ duration: 0.9, delay: 0.3 }}
                >
                    <h4 style={{ fontFamily: fonts.serif, fontSize: 'clamp(1.5rem, 5vw, 2rem)', color: palette.text, fontWeight: 400, margin: '0 0 10px' }}>
                        Resepsi
                    </h4>
                    <p style={{ fontFamily: fonts.sans, fontSize: 14, color: palette.text, lineHeight: 1.8, margin: '0 0 8px', whiteSpace: 'pre-line' }}>
                        {`${formatDate(data.reception_datetime)}\n${formatTime(data.reception_datetime)} WIB - Selesai\n(${data.reception_venue || 'Lokasi acara'})`}
                    </p>
                    <p style={{ fontFamily: fonts.sans, fontSize: 13, color: palette.muted, lineHeight: 1.7, margin: '0 0 16px' }}>
                        {data.reception_address}
                    </p>
                    <div style={{ display: 'flex', justifyContent: 'center', gap: 10, flexWrap: 'wrap' }}>
                        <a
                            href={data.maps_link || '#'}
                            target="_blank"
                            rel="noreferrer"
                            style={{
                                display: 'inline-block',
                                padding: '8px 16px',
                                border: `1px solid ${palette.border}`,
                                color: palette.text,
                                textDecoration: 'none',
                                fontFamily: fonts.sans,
                                fontSize: 11,
                                letterSpacing: '2px',
                                textTransform: 'uppercase'
                            }}
                        >
                            Google Maps
                        </a>
                        <a
                            href={akadCalendarUrl}
                            target="_blank"
                            rel="noreferrer"
                            style={{
                                display: 'inline-block',
                                padding: '8px 16px',
                                border: `1px solid ${palette.border}`,
                                color: palette.text,
                                textDecoration: 'none',
                                fontFamily: fonts.sans,
                                fontSize: 11,
                                letterSpacing: '2px',
                                textTransform: 'uppercase'
                            }}
                        >
                            Akad Calendar
                        </a>
                    </div>
                </motion.div>
            </div>
        </section>
    );
}
