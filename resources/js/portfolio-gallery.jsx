import React, { useState } from 'react';
import { createRoot } from 'react-dom/client';
import DomeGallery from './components/DomeGallery';
import MediaSlider from './components/MediaSlider';
import './components/MediaSlider.css';

function PortfolioGallery({ cards }) {
    const [sliderData, setSliderData] = useState(null);

    // Convert cards to dome images (cover only for tiles)
    const domeImages = cards.map((card, i) => ({
        src: card.cover,
        alt: card.title || 'Galeri',
        type: 'image',
        embed: '',
        cardIndex: i
    }));

    const handleTileClick = (imageData) => {
        const idx = imageData.cardIndex;
        if (idx !== undefined && cards[idx]) {
            setSliderData({
                media: cards[idx].media,
                title: cards[idx].title,
                caption: cards[idx].caption
            });
        }
    };

    return (
        <>
            <DomeGallery
                images={domeImages}
                grayscale={false}
                imageBorderRadius="16px"
                openedImageBorderRadius="24px"
                padFactor={0.1}
                segments={18}
                overlayBlurColor="var(--dome-bg, #0A0A0A)"
                autoRotateSpeed={-0.05}
                onTileClick={handleTileClick}
                disableEnlarge={true}
            />
            {sliderData && (
                <MediaSlider
                    media={sliderData.media}
                    title={sliderData.title}
                    caption={sliderData.caption}
                    onClose={() => setSliderData(null)}
                />
            )}
        </>
    );
}

document.addEventListener('DOMContentLoaded', () => {
    const rootElement = document.getElementById('react-dome-gallery');
    if (rootElement) {
        let cards = [];
        try {
            const rawData = rootElement.getAttribute('data-cards');
            if (rawData) {
                cards = JSON.parse(rawData);
            }
        } catch (e) {
            console.error('Failed to parse portfolio cards data', e);
        }

        const root = createRoot(rootElement);
        root.render(<PortfolioGallery cards={cards} />);
    }
});
