import React from 'react';
import { Routes, Route, Navigate } from 'react-router-dom';
import InvitationPage from './pages/InvitationPage.jsx';

export default function App() {
    return (
        <Routes>
            <Route path="/undangan/:slug" element={<InvitationPage />} />
            <Route path="*" element={<Navigate to="/" replace />} />
        </Routes>
    );
}
