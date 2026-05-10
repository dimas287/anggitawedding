import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';

export default defineConfig({
    server: {
        host: '127.0.0.1',
        hmr: {
            host: '127.0.0.1',
        },
    },
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js', 'resources/js/invitation-app/main.jsx', 'resources/js/portfolio-gallery.jsx'],
            refresh: true,
        }),
        react({
            include: '**/*.{jsx,tsx}',
        }),
    ],
    build: {
        // Raise warning limit to reduce noise (500KB is fine for modern apps)
        chunkSizeWarningLimit: 800,
        rollupOptions: {
            output: {
                // Code splitting: separate large vendor libraries into their own chunks
                manualChunks(id) {
                    // Alpine.js into its own chunk
                    if (id.includes('alpinejs')) {
                        return 'alpine';
                    }
                    // Flatpickr (date picker) into its own chunk
                    if (id.includes('flatpickr')) {
                        return 'flatpickr';
                    }
                    // Axios into its own chunk
                    if (id.includes('axios')) {
                        return 'axios';
                    }
                    // All other node_modules into a shared vendor chunk
                    if (id.includes('node_modules') && !id.includes('react') && !id.includes('react-dom')) {
                        return 'vendor';
                    }
                },
            },
        },
    },
});
