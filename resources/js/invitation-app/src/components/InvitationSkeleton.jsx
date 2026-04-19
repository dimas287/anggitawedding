import React from 'react';

const shimmerStyle = {
    background: 'linear-gradient(90deg, rgba(255,255,255,0.12) 25%, rgba(255,255,255,0.3) 37%, rgba(255,255,255,0.12) 63%)',
    backgroundSize: '400% 100%',
    animation: 'shimmer 1.4s ease infinite',
};

const block = (height) => ({
    height,
    borderRadius: 24,
    marginBottom: 16,
    ...shimmerStyle,
});

export default function InvitationSkeleton() {
    return (
        <div
            style={{
                minHeight: '100vh',
                background: 'radial-gradient(circle at top, #3b2418, #120804 65%)',
                padding: '64px 20px',
                color: '#fef3c7',
                fontFamily: '"Space Grotesk", "Poppins", sans-serif',
            }}
        >
            <style>
                {`@keyframes shimmer { 0% { background-position: 200% 0; } 100% { background-position: -200% 0; } }`}
            </style>
            <div style={{ maxWidth: 720, margin: '0 auto' }}>
                <div style={{ ...block(280) }} />
                <div style={{ ...block(220) }} />
                <div style={{ ...block(320) }} />
                <div style={{ ...block(360) }} />
                <div style={{ ...block(260) }} />
            </div>
        </div>
    );
}
