import React from 'react';
import { createRoot } from 'react-dom/client';
import DomeGallery from './components/DomeGallery';

document.addEventListener('DOMContentLoaded', () => {
    const rootElement = document.getElementById('react-dome-gallery');
    if (rootElement) {
        let images = [];
        try {
            const rawData = rootElement.getAttribute('data-images');
            if (rawData) {
                images = JSON.parse(rawData);
            }
        } catch (e) {
            console.error('Failed to parse portfolio images data', e);
        }

        const root = createRoot(rootElement);
        root.render(
            <DomeGallery 
                images={images}
                grayscale={false}
                imageBorderRadius="16px"
                openedImageBorderRadius="24px"
                openedImageWidth="90vw"
                openedImageHeight="90vh"
                padFactor={0.1}
            />
        );
    }
});
