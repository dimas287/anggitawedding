export const palette = {
    darkFill: '#000000',
    darkFillAlt: '#101010',
    text: '#ffffff',
    muted: 'rgba(255,255,255,0.65)',
    accent: '#dbd6d3',
    accentHover: '#f2eeec',
    border: 'rgba(219, 214, 211, 0.35)',
};

export const fonts = {
    serif: "'CormorantGaramond-Regular', serif",
    sans: "'Josefin Sans', sans-serif",
    script: "'Sacramento', cursive",
};

export const formatDate = (dateStr) => {
    if (!dateStr) return '';
    try {
        const d = new Date(dateStr);
        return d.toLocaleDateString('id-ID', {
            weekday: 'long', day: 'numeric', month: 'long', year: 'numeric'
        });
    } catch(e) { return dateStr; }
};

export const formatTime = (dateStr) => {
    if (!dateStr) return '';
    try {
        const d = new Date(dateStr);
        return d.toLocaleTimeString('id-ID', { hour: '2-digit', minute:'2-digit' }).replace('.', ':');
    } catch(e) { return ''; }
};

export const generateGoogleCalendarUrl = (title, dateStr, location, details) => {
    if (!dateStr) return '#';
    const start = new Date(dateStr);
    const end = new Date(start.getTime() + 2 * 60 * 60 * 1000); // +2 hours

    const formatObj = (d) => d.toISOString().replace(/-|:|\.\d\d\d/g, "");
    const params = new URLSearchParams({
        action: 'TEMPLATE',
        text: title,
        dates: `${formatObj(start)}/${formatObj(end)}`,
        details: details || '',
        location: location || '',
    });
    return `https://calendar.google.com/calendar/render?${params.toString()}`;
};

export const resolveMediaUrl = (value, fallback = null) => {
    if (!value) return fallback;
    const raw = Array.isArray(value) ? value[0] : value;
    const src = String(raw);
    if (src.startsWith('http')) return src;
    if (src.startsWith('/storage/')) return src;
    return `/storage/${src.replace(/^\/+/, '')}`;
};
