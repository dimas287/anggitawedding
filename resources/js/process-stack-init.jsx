import React from 'react';
import { createRoot } from 'react-dom/client';
import ProcessSectionStack from './components/ProcessSectionStack';

document.addEventListener('DOMContentLoaded', () => {
    const container = document.getElementById('harmoni-pelayanan-root');
    if (container) {
        const items = JSON.parse(container.dataset.items || '[]');
        const root = createRoot(container);
        root.render(<ProcessSectionStack items={items} />);
    }
});
