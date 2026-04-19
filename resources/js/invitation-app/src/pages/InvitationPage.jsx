import React, { useEffect, useMemo, useState, Suspense } from 'react';
import { useParams } from 'react-router-dom';
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
    const { slug } = useParams();
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);
    const [invitation, setInvitation] = useState(null);

    useEffect(() => {
        let mounted = true;
        setLoading(true);
        setError(null);

        fetch(`/api/invitations/${encodeURIComponent(slug)}`)
            .then(async (res) => {
                if (!res.ok) {
                    const text = await res.text();
                    throw new Error(text || `Request failed: ${res.status}`);
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
            <div style={{ padding: 24, fontFamily: 'system-ui, sans-serif', color: '#b91c1c' }}>
                {error}
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
