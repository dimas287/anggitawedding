import React, { useEffect, useMemo, useState, Suspense } from 'react';
import InvitationSkeleton from '../components/InvitationSkeleton.jsx';

const templateRegistry = {
    'tema1': () => import('../templates/tema1/tema1.jsx'),
    'tema2': () => import('../templates/tema2/tema2.jsx'),
    'tema3': () => import('../templates/tema3/tema3.jsx'),
};

function resolveTemplateLoader(templateSlug) {
    if (!templateSlug) return null;
    return templateRegistry[templateSlug] ?? null;
}

export default function InvitationPage() {
    const slug = useMemo(() => {
        const segments = window.location.pathname.split('/').filter(Boolean);
        const invitationIndex = segments.indexOf('undangan');
        return invitationIndex >= 0 ? decodeURIComponent(segments[invitationIndex + 1] ?? '') : '';
    }, []);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);
    const [invitation, setInvitation] = useState(null);

    useEffect(() => {
        if (!slug) {
            setError('Tautan undangan tidak valid.');
            setLoading(false);
            return undefined;
        }

        let mounted = true;
        setLoading(true);
        setError(null);

        fetch(`/api/invitations/${encodeURIComponent(slug)}`, {
            headers: { Accept: 'application/json' },
        })
            .then(async (res) => {
                if (!res.ok) {
                    if (res.status === 404) {
                        throw new Error('Undangan tidak ditemukan atau belum dipublikasikan.');
                    }
                    if (res.status === 503) {
                        throw new Error('Layanan undangan sedang dalam pemeliharaan.');
                    }
                    throw new Error('Undangan belum dapat dimuat. Silakan coba beberapa saat lagi.');
                }
                return res.json();
            })
            .then((data) => {
                if (!mounted) return;
                setInvitation(data);
            })
            .catch((e) => {
                if (!mounted) return;
                setError(e.message || 'Gagal memuat undangan.');
            })
            .finally(() => {
                if (!mounted) return;
                setLoading(false);
            });

        return () => {
            mounted = false;
        };
    }, [slug]);

    const Template = useMemo(() => {
        const loader = resolveTemplateLoader(invitation?.template?.slug);
        return loader ? React.lazy(loader) : null;
    }, [invitation?.template?.slug]);

    if (loading) {
        return <InvitationSkeleton />;
    }

    if (error) {
        return (
            <div role="alert" style={{ minHeight: '100vh', padding: 24, display: 'grid', placeItems: 'center', textAlign: 'center', fontFamily: 'system-ui, sans-serif', color: '#7f1d1d', background: '#fff7ed' }}>
                <div>
                    <h1 style={{ margin: '0 0 8px', fontSize: 24 }}>Undangan belum dapat dibuka</h1>
                    <p style={{ margin: 0 }}>{error}</p>
                </div>
            </div>
        );
    }

    if (!invitation) {
        return (
            <div style={{ padding: 24, fontFamily: 'system-ui, sans-serif' }}>
                Undangan tidak ditemukan.
            </div>
        );
    }

    if (!Template) {
        return (
            <div style={{ padding: 24, fontFamily: 'system-ui, sans-serif' }}>
                Template belum tersedia untuk undangan ini.
            </div>
        );
    }

    return (
        <Suspense fallback={<InvitationSkeleton />}>
            <Template invitation={invitation} />
        </Suspense>
    );
}
