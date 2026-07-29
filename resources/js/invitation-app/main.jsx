import React from 'react';
import ReactDOM from 'react-dom/client';
import App from './src/App.jsx';

const el = document.getElementById('invitation-root');

if (el) {
    ReactDOM.createRoot(el).render(
        <React.StrictMode>
            <App />
        </React.StrictMode>
    );
}
