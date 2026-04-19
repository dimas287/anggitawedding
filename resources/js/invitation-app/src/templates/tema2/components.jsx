import React from 'react';
import { motion } from 'framer-motion';
import { palette } from './utils.js';

export const SectionTitle = ({ subtitle, title, accent }) => (
    <div style={{ textAlign: 'center', marginBottom: 32 }}>
        <div style={{
            display: 'inline-flex', alignItems: 'center', gap: 10,
            textTransform: 'uppercase', letterSpacing: 6, fontSize: 11,
            color: palette.neonAlt, fontWeight: 700,
        }}>
            <span style={{ width: 32, height: 1, background: `linear-gradient(to right, transparent, ${palette.neonAlt})` }} />
            {subtitle}
            <span style={{ width: 32, height: 1, background: `linear-gradient(to left, transparent, ${palette.neonAlt})` }} />
        </div>
        <h2 style={{
            marginTop: 14, fontSize: 30, fontWeight: 900, letterSpacing: 1,
            color: palette.text, textShadow: `0 0 24px rgba(255,113,200,0.4)`,
            lineHeight: 1.25,
        }}>{title}</h2>
        {accent && <p style={{ marginTop: 10, color: palette.muted, fontSize: 14, lineHeight: 1.7 }}>{accent}</p>}
    </div>
);

export const FloatingOrbs = () => (
    <>
        {[
            { size: 180, color: '#ff71c820', top: '8%', left: '5%', dur: 20 },
            { size: 120, color: '#6cfeff18', top: '35%', left: '80%', dur: 25 },
            { size: 240, color: '#ffd97d10', top: '65%', left: '20%', dur: 22 },
            { size: 100, color: '#ff71c815', top: '85%', left: '70%', dur: 18 },
        ].map((orb, i) => (
            <motion.div
                key={i}
                animate={{
                    opacity: [0.5, 0.9, 0.5],
                    scale: [0.9, 1.1, 0.9],
                    x: [0, 30, -15, 0],
                    y: [0, -20, 10, 0],
                }}
                transition={{ duration: orb.dur, repeat: Infinity, ease: 'easeInOut' }}
                style={{
                    position: 'fixed',
                    width: orb.size, height: orb.size,
                    borderRadius: '50%',
                    background: orb.color,
                    filter: 'blur(40px)',
                    top: orb.top, left: orb.left,
                    pointerEvents: 'none',
                    zIndex: 0,
                }}
            />
        ))}
    </>
);

export const ActionButton = ({ children, onClick, variant = 'primary', style, ...rest }) => (
    <motion.button
        whileTap={{ scale: 0.94 }}
        whileHover={{ scale: 1.04, boxShadow: '0 0 30px rgba(255,113,200,0.6)' }}
        animate={variant === 'primary' ? {
            boxShadow: ['0 0 0px rgba(255,113,200,0)', '0 0 35px rgba(255,113,200,0.4)', '0 0 0px rgba(255,113,200,0)'],
        } : {}}
        transition={{ duration: 2.6, repeat: Infinity, ease: 'easeInOut' }}
        onClick={onClick}
        {...rest}
        style={{
            borderRadius: 20,
            padding: '14px 28px',
            cursor: 'pointer',
            fontWeight: 800,
            letterSpacing: 1.5,
            fontSize: 14,
            textTransform: 'uppercase',
            border: 'none',
            background: variant === 'primary'
                ? 'linear-gradient(120deg, #ff71c8, #c945a5, #6cfeff)'
                : 'rgba(255,255,255,0.08)',
            color: variant === 'primary' ? '#080112' : palette.text,
            backdropFilter: 'blur(8px)',
            boxSizing: 'border-box',
            fontFamily: 'inherit',
            ...style,
        }}
    >
        {children}
    </motion.button>
);

export const GlassInput = ({ as: Tag = 'input', style, ...props }) => {
    const baseStyle = {
        width: '100%', boxSizing: 'border-box',
        borderRadius: 16, padding: '14px 18px',
        border: '1px solid rgba(255,255,255,0.12)',
        background: 'rgba(255,255,255,0.04)',
        color: palette.text, fontSize: 15, outline: 'none',
        fontFamily: 'inherit', transition: 'border-color .25s, box-shadow .25s',
        ...style,
    };
    return (
        <Tag
            {...props}
            style={baseStyle}
            onFocus={e => { e.target.style.borderColor = palette.neon; e.target.style.boxShadow = `0 0 18px rgba(255,113,200,0.25)`; }}
            onBlur={e => { e.target.style.borderColor = 'rgba(255,255,255,0.12)'; e.target.style.boxShadow = 'none'; }}
        />
    );
};

export const FadeUp = ({ children, delay = 0, style }) => (
    <motion.div
        initial={{ opacity: 0, y: 40 }}
        whileInView={{ opacity: 1, y: 0 }}
        viewport={{ once: true, margin: '-60px' }}
        transition={{ duration: 0.7, delay, ease: [0.22, 1, 0.36, 1] }}
        style={style}
    >
        {children}
    </motion.div>
);
