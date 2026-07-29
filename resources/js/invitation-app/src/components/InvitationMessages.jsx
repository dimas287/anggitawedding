import React, { useEffect, useState } from 'react';

export default function InvitationMessages({ data }) {
    const [name, setName] = useState('');
    const [attendance, setAttendance] = useState('hadir');
    const [message, setMessage] = useState('');
    const [entries, setEntries] = useState([]);
    const [submitting, setSubmitting] = useState(false);
    const [feedback, setFeedback] = useState(null);

    useEffect(() => {
        if (!data.slug || data.is_demo) return;

        fetch(`/api/invitations/${encodeURIComponent(data.slug)}/guestbook`, {
            headers: { Accept: 'application/json' },
        })
            .then((response) => response.ok ? response.json() : Promise.reject(new Error()))
            .then((result) => setEntries(Array.isArray(result) ? result : []))
            .catch(() => setFeedback({ type: 'danger', text: 'Ucapan belum dapat dimuat.' }));
    }, [data.is_demo, data.slug]);

    const submit = async (event) => {
        event.preventDefault();

        if (!data.slug || data.is_demo) {
            setFeedback({ type: 'warning', text: 'Form tidak aktif pada pratinjau template.' });
            return;
        }

        setSubmitting(true);
        setFeedback(null);

        try {
            if (data.rsvp_enabled) {
                const rsvpResponse = await fetch(`/api/invitations/${encodeURIComponent(data.slug)}/rsvp`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
                    body: JSON.stringify({
                        name: name.trim(),
                        guests_count: 1,
                        attendance,
                        message: message.trim(),
                        hp_field: '',
                    }),
                });

                if (!rsvpResponse.ok) {
                    const result = await rsvpResponse.json().catch(() => ({}));
                    throw new Error(result.message || 'Konfirmasi kehadiran gagal dikirim.');
                }
            }

            const messageResponse = await fetch(`/api/invitations/${encodeURIComponent(data.slug)}/guestbook`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
                body: JSON.stringify({
                    name: name.trim(),
                    message: message.trim(),
                    hp_field: '',
                }),
            });
            const result = await messageResponse.json();

            if (!messageResponse.ok || !result.entry) {
                throw new Error(result.message || 'Ucapan gagal dikirim.');
            }

            setEntries((current) => [result.entry, ...current]);
            setName('');
            setMessage('');
            setFeedback({
                type: 'success',
                text: data.rsvp_enabled
                    ? 'Konfirmasi dan ucapan Anda berhasil dikirim.'
                    : 'Ucapan Anda berhasil dikirim.',
            });
        } catch (error) {
            setFeedback({ type: 'danger', text: error.message || 'Terjadi kesalahan koneksi.' });
        } finally {
            setSubmitting(false);
        }
    };

    return (
        <section className="bg-light-dark my-0 pb-0 pt-3" id="comment">
            <div className="container">
                <div className="border rounded-5 shadow p-3 mb-2">
                    <h2 className="font-esthetic text-center mt-2 mb-3" style={{ fontSize: '2.5rem' }}>
                        Ucapan &amp; Doa
                    </h2>

                    {feedback && (
                        <div role="status" className={`alert alert-${feedback.type} py-2`}>
                            {feedback.text}
                        </div>
                    )}

                    <form onSubmit={submit}>
                        <div className="mb-3">
                            <label className="form-label my-1" htmlFor="guest-name">
                                <i className="fa-solid fa-person me-2"></i>Nama
                            </label>
                            <input
                                id="guest-name"
                                required
                                maxLength={100}
                                value={name}
                                onChange={(event) => setName(event.target.value)}
                                className="form-control shadow-sm rounded-4"
                                placeholder="Isikan nama Anda"
                            />
                        </div>

                        {data.rsvp_enabled && (
                            <div className="mb-3">
                                <label className="form-label my-1" htmlFor="guest-attendance">
                                    <i className="fa-solid fa-person-circle-question me-2"></i>Presensi
                                </label>
                                <select
                                    id="guest-attendance"
                                    className="form-select shadow-sm rounded-4"
                                    value={attendance}
                                    onChange={(event) => setAttendance(event.target.value)}
                                >
                                    <option value="hadir">Hadir</option>
                                    <option value="tidak_hadir">Berhalangan hadir</option>
                                    <option value="mungkin">Masih tentative</option>
                                </select>
                            </div>
                        )}

                        <div className="mb-3">
                            <label className="form-label my-1" htmlFor="guest-message">
                                <i className="fa-solid fa-comment me-2"></i>Ucapan &amp; Doa
                            </label>
                            <textarea
                                id="guest-message"
                                required
                                maxLength={1000}
                                value={message}
                                onChange={(event) => setMessage(event.target.value)}
                                className="form-control shadow-sm rounded-4"
                                rows={4}
                                placeholder="Tulis ucapan dan doa"
                            />
                        </div>

                        <div className="d-grid">
                            <button
                                disabled={submitting}
                                type="submit"
                                className="btn btn-primary btn-sm rounded-4 shadow-sm m-1 py-2"
                                style={{ letterSpacing: '0.05em' }}
                            >
                                <i className="fa-solid fa-paper-plane me-2"></i>
                                {submitting ? 'Mengirim...' : 'Kirim Ucapan'}
                            </button>
                        </div>
                    </form>

                    <div className="mt-4">
                        {entries.length === 0 ? (
                            <p className="text-center small opacity-75">Belum ada ucapan.</p>
                        ) : entries.slice(0, 20).map((entry) => (
                            <div key={entry.id} className="border-top py-3">
                                <strong className="d-block">{entry.name}</strong>
                                <span className="small">{entry.message}</span>
                            </div>
                        ))}
                    </div>
                </div>
            </div>
        </section>
    );
}
