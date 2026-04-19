import React from 'react';
import { motion } from 'framer-motion';
import { palette, fonts } from './utils.js';

export function FadeUp({ children, delay = 0, style }) {
    return (
        <motion.div
            initial={{ opacity: 0, y: 30 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true, margin: '-50px' }}
            transition={{ duration: 1, delay, ease: [0.16, 1, 0.3, 1] }}
            style={style}
        >
            {children}
        </motion.div>
    );
}

export function SectionTitle({ title, subtitle, accent }) {
    return (
        <div style={{ textAlign: 'center', marginBottom: 40, padding: '0 20px' }}>
            {subtitle && (
                <motion.div
                    initial={{ opacity: 0, y: 10 }}
                    whileInView={{ opacity: 1, y: 0 }}
                    viewport={{ once: true }}
                    transition={{ duration: 0.8 }}
                    style={{
                        fontFamily: fonts.sans, fontSize: 11, letterSpacing: '4px',
                        textTransform: 'uppercase', color: palette.accent, marginBottom: 12
                    }}
                >
                    {subtitle}
                </motion.div>
            )}
            <motion.h2
                initial={{ opacity: 0, y: 20 }}
                whileInView={{ opacity: 1, y: 0 }}
                viewport={{ once: true }}
                transition={{ duration: 1, delay: 0.2 }}
                style={{
                    fontFamily: fonts.serif, fontSize: 'clamp(2rem, 6vw, 2.5rem)', fontWeight: 400,
                    color: palette.text, margin: '0 0 16px', lineHeight: 1.2
                }}
            >
                {title}
            </motion.h2>
            {accent && (
                <motion.div
                    initial={{ opacity: 0 }}
                    whileInView={{ opacity: 1 }}
                    viewport={{ once: true }}
                    transition={{ duration: 1, delay: 0.4 }}
                    style={{
                        fontFamily: fonts.sans, fontSize: 13, color: palette.muted,
                        maxWidth: 400, margin: '0 auto', lineHeight: 1.6
                    }}
                >
                    {accent}
                </motion.div>
            )}
            <motion.div
                initial={{ scaleX: 0 }}
                whileInView={{ scaleX: 1 }}
                viewport={{ once: true }}
                transition={{ duration: 1, delay: 0.5 }}
                style={{
                    width: 40, height: 1, background: palette.accent,
                    margin: '24px auto 0', transformOrigin: 'center'
                }}
            />
        </div>
    );
}

export function ActionButton({ children, onClick, variant = 'primary', style, type = "button", disabled = false }) {
    const isPrimary = variant === 'primary';
    const bg = isPrimary ? palette.accent : 'transparent';
    const color = isPrimary ? palette.darkFill : palette.accent;
    const border = isPrimary ? 'none' : `1px solid ${palette.accent}`;

    return (
        <motion.button
            type={type}
            disabled={disabled}
            whileHover={{ scale: 1.02, backgroundColor: isPrimary ? palette.accentHover : 'rgba(212,175,55,0.1)' }}
            whileTap={{ scale: 0.98 }}
            onClick={onClick}
            style={{
                background: bg, color, border,
                padding: '12px 28px', borderRadius: '4px', // Sharper corners for cinematic feel
                fontFamily: fonts.sans, fontSize: 12, letterSpacing: '2px', textTransform: 'uppercase',
                fontWeight: 600, cursor: disabled ? 'not-allowed' : 'pointer',
                opacity: disabled ? 0.6 : 1, transition: 'background-color 0.3s ease',
                ...style
            }}
        >
            {children}
        </motion.button>
    );
}

export function GlassInput({ label, type = "text", value, onChange, placeholder, required = false, as = "input", rows = 3 }) {
    return (
        <div style={{ marginBottom: '16px', textAlign: 'left' }}>
            {label && (
                <label style={{
                    display: 'block', fontFamily: fonts.sans, fontSize: 11,
                    textTransform: 'uppercase', letterSpacing: '1px',
                    color: palette.muted, marginBottom: 8
                }}>
                    {label} {required && <span style={{ color: palette.accent }}>*</span>}
                </label>
            )}
            {as === 'textarea' ? (
                <textarea
                    required={required} rows={rows} value={value} onChange={onChange} placeholder={placeholder}
                    style={{
                        width: '100%', padding: '14px 16px', background: 'rgba(255,255,255,0.03)',
                        border: `1px solid rgba(255,255,255,0.1)`, borderRadius: '4px',
                        color: palette.text, fontFamily: fonts.sans, fontSize: 14, outline: 'none',
                        resize: 'none', transition: 'border-color 0.3s'
                    }}
                    onFocus={(e) => e.target.style.borderColor = palette.accent}
                    onBlur={(e) => e.target.style.borderColor = 'rgba(255,255,255,0.1)'}
                />
            ) : (
                <input
                    type={type} required={required} value={value} onChange={onChange} placeholder={placeholder}
                    style={{
                        width: '100%', padding: '14px 16px', background: 'rgba(255,255,255,0.03)',
                        border: `1px solid rgba(255,255,255,0.1)`, borderRadius: '4px',
                        color: palette.text, fontFamily: fonts.sans, fontSize: 14, outline: 'none',
                        transition: 'border-color 0.3s'
                    }}
                    onFocus={(e) => e.target.style.borderColor = palette.accent}
                    onBlur={(e) => e.target.style.borderColor = 'rgba(255,255,255,0.1)'}
                />
            )}
        </div>
    );
}
