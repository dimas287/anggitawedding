import React from 'react';

export function WaveSeparator({ pathData, noGapBottom = true }) {
    return (
        <div className="svg-wrapper">
            <svg 
                xmlns="http://www.w3.org/2000/svg" 
                viewBox="0 0 1440 320" 
                className={`color-theme-svg ${noGapBottom ? 'no-gap-bottom' : ''}`}
            >
                <path fill="currentColor" fillOpacity="1" d={pathData}></path>
            </svg>
        </div>
    );
}

export function SectionTitle({ title, subtitle, arabicTitle, className = "" }) {
    return (
        <div className={`text-center ${className}`}>
            {arabicTitle && (
                <h2 className="font-arabic py-4 m-0" style={{ fontSize: '2rem' }}>
                    {arabicTitle}
                </h2>
            )}
            <h2 className="font-esthetic py-4 m-0" style={{ fontSize: '2rem' }}>
                {title}
            </h2>
            {subtitle && (
                <p className="pb-4 px-2 m-0" style={{ fontSize: '0.95rem' }}>
                    {subtitle}
                </p>
            )}
        </div>
    );
}
