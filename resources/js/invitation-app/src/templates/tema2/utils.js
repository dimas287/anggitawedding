// Shared utilities & design tokens for Tema2

export const palette = {
    bg: '#050012',
    gradient: 'linear-gradient(160deg, #050012 0%, #0d0025 30%, #1a0040 60%, #2a0060 100%)',
    neon: '#ff71c8',
    neonAlt: '#6cfeff',
    neonGold: '#ffd97d',
    text: '#f0eeff',
    muted: '#9992b8',
    surface: 'rgba(10, 5, 30, 0.65)',
};

export const glassCard = {
    background: 'rgba(8, 4, 25, 0.6)',
    borderRadius: 28,
    border: '1px solid rgba(255,255,255,0.08)',
    padding: '36px 28px',
    boxShadow: '0 24px 60px rgba(0,0,0,0.45), inset 0 1px 0 rgba(255,255,255,0.06)',
    backdropFilter: 'blur(24px)',
    WebkitBackdropFilter: 'blur(24px)',
};

export const inputStyle = {
    width: '100%',
    boxSizing: 'border-box',
    borderRadius: 16,
    padding: '14px 18px',
    border: '1px solid rgba(255,255,255,0.14)',
    background: 'rgba(255,255,255,0.04)',
    color: palette.text,
    fontSize: 15,
    outline: 'none',
    transition: 'border-color .25s, box-shadow .25s',
    fontFamily: 'inherit',
};

export function formatDate(iso) {
    if (!iso) return null;
    const d = new Date(iso);
    if (Number.isNaN(d.getTime())) return null;
    return d.toLocaleDateString('id-ID', {
        weekday: 'long', year: 'numeric', month: 'long', day: 'numeric',
    });
}

export function formatTime(iso) {
    if (!iso) return null;
    const d = new Date(iso);
    if (Number.isNaN(d.getTime())) return null;
    return d.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
}

export function getEventTarget(iso) {
    if (!iso) return null;
    const d = new Date(iso);
    return Number.isNaN(d.getTime()) ? null : d;
}

export function generateGoogleCalendarUrl({ title, start, end, location, description }) {
    const fmt = (d) => d.toISOString().replace(/[-:]/g, '').replace('.000', '');
    const endDate = end || new Date(start.getTime() + 3 * 60 * 60 * 1000);
    const params = new URLSearchParams({
        action: 'TEMPLATE',
        text: title,
        dates: `${fmt(start)}/${fmt(endDate)}`,
        location: location || '',
        details: description || '',
    });
    return `https://calendar.google.com/calendar/render?${params}`;
}

export function generateICS({ title, start, end, location, description }) {
    const fmt = (d) => d.toISOString().replace(/[-:]/g, '').replace('.000', '');
    const endDate = end || new Date(start.getTime() + 3 * 60 * 60 * 1000);
    const ics = [
        'BEGIN:VCALENDAR',
        'VERSION:2.0',
        'PRODID:-//Anggita WO//tema2//ID',
        'BEGIN:VEVENT',
        `DTSTART:${fmt(start)}`,
        `DTEND:${fmt(endDate)}`,
        `SUMMARY:${title}`,
        `LOCATION:${location || ''}`,
        `DESCRIPTION:${description || ''}`,
        'END:VEVENT',
        'END:VCALENDAR',
    ].join('\r\n');
    return ics;
}
