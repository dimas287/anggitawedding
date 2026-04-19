import React, { useState, useEffect, useRef } from 'react';
import { WaveSeparator, SectionTitle } from './components.jsx';
import { resolveMediaUrl, formatDate, formatTime, generateGoogleCalendarUrl } from './utils.js';

export function HomeSection({ data, openModal }) {
    const mainBgImg = resolveMediaUrl(data?.media_files?.main_background) || data.photo_prewedding_url || resolveMediaUrl(data.thumbnail);
    const coverImg = resolveMediaUrl(data?.media_files?.cover) || mainBgImg;

    return (
        <section id="home" className="bg-light-dark position-relative overflow-hidden p-0 m-0">
            <img src={mainBgImg} alt="bg" className="position-absolute opacity-25 top-50 start-50 translate-middle bg-cover-home" />

            <div className="position-relative text-center bg-overlay-auto" style={{ backgroundColor: 'unset' }}>
                <p className="pt-5 pb-1 m-0 text-uppercase" style={{ fontSize: '0.7rem', letterSpacing: '0.3em', opacity: 0.5 }}>The Wedding Of</p>
                <h1 className="font-esthetic pb-3 fw-medium" style={{ fontSize: '2.5rem' }}>Undangan Pernikahan</h1>

                <img 
                    src={coverImg} 
                    alt="cover" 
                    onClick={() => openModal(coverImg)} 
                    className="img-center-crop rounded-circle shadow my-3 mx-auto cursor-pointer" 
                    style={{ animationDelay: '0.3s' }}
                />

                <h2 className="font-esthetic my-3 shimmer-text" style={{ fontSize: '2.5rem' }}>
                    {data.groom_short_name || data.groom_name?.split(' ')[0]} &amp; {data.bride_short_name || data.bride_name?.split(' ')[0]}
                </h2>
                <p className="my-2 font-garamond" style={{ fontSize: '1.15rem', letterSpacing: '0.1em', opacity: 0.7 }}>{formatDate(data.reception_datetime)}</p>

                <a 
                    href={generateGoogleCalendarUrl(`Resepsi ${data.groom_short_name} & ${data.bride_short_name}`, data.reception_datetime, data.reception_address, '')}
                    target="_blank" rel="noreferrer"
                    className="btn btn-outline-auto btn-sm shadow-sm rounded-pill px-4 py-2 text-decoration-none d-inline-flex align-items-center mt-2" 
                    style={{ fontSize: '0.75rem', letterSpacing: '0.05em' }}
                >
                    <i className="fa-solid fa-calendar-check me-2"></i>Save Google Calendar
                </a>

                <div className="d-flex justify-content-center align-items-center mt-4 mb-2">
                    <div className="mouse-animation border border-secondary border-2 rounded-5 px-2 py-1 opacity-50">
                        <div className="scroll-animation rounded-4 bg-secondary"></div>
                    </div>
                </div>

                <p className="pb-4 m-0 text-secondary text-uppercase" style={{ fontSize: '0.65rem', letterSpacing: '0.15em' }}>Scroll Down</p>
            </div>
        </section>
    );
}

export function BrideGroomSection({ data, openModal }) {
    const groomImg = resolveMediaUrl(data?.media_files?.groom_profile) || data.groom_photo_url || data.photo_prewedding_url;
    const brideImg = resolveMediaUrl(data?.media_files?.bride_profile) || data.bride_photo_url || data.photo_prewedding_url;

    return (
        <section className="bg-white-black text-center" id="bride">
            <SectionTitle 
                arabicTitle="بِسْمِ اللّٰهِ الرَّحْمٰنِ الرَّحِيْمِ"
                title="Assalamualaikum Warahmatullahi Wabarakatuh"
                subtitle="Tanpa mengurangi rasa hormat, kami mengundang Anda untuk berkenan menghadiri acara pernikahan kami:"
                className="pt-3"
            />

            <div className="overflow-x-hidden pb-4">
                {/* Groom */}
                <div className="position-relative">
                    <LoveAnimation top="0%" right="5%" delay={0} />
                    <div className="pb-1" data-aos="fade-right" data-aos-duration="2000">
                        <img 
                            src={groomImg} alt="groom" onClick={() => openModal(groomImg)}
                            className="img-center-crop rounded-circle border border-3 border-light shadow my-4 mx-auto cursor-pointer" 
                        />
                        <h2 className="font-esthetic m-0" style={{ fontSize: '2.125rem' }}>{data.groom_name}</h2>
                        <p className="mt-3 mb-1" style={{ fontSize: '1.25rem' }}>Putra Bpk. {data.groom_father}</p>
                        <p className="mb-0" style={{ fontSize: '0.95rem' }}>dan Ibu {data.groom_mother}</p>
                    </div>
                    <LoveAnimation top="90%" left="5%" delay={500} />
                </div>

                <h2 className="font-esthetic mt-4" style={{ fontSize: '4.5rem' }}>&amp;</h2>

                {/* Bride */}
                <div className="position-relative mt-2">
                    <LoveAnimation top="0%" right="5%" delay={1000} />
                    <div className="pb-1" data-aos="fade-left" data-aos-duration="2000">
                        <img 
                            src={brideImg} alt="bride" onClick={() => openModal(brideImg)}
                            className="img-center-crop rounded-circle border border-3 border-light shadow my-4 mx-auto cursor-pointer" 
                        />
                        <h2 className="font-esthetic m-0" style={{ fontSize: '2.125rem' }}>{data.bride_name}</h2>
                        <p className="mt-3 mb-1" style={{ fontSize: '1.25rem' }}>Putri Bpk. {data.bride_father}</p>
                        <p className="mb-0" style={{ fontSize: '0.95rem' }}>dan Ibu {data.bride_mother}</p>
                    </div>
                    <LoveAnimation top="90%" left="5%" delay={1500} />
                </div>
            </div>
        </section>
    );
}

function LoveAnimation({ top, left, right, delay = 0 }) {
    return (
        <div className="position-absolute" style={{ top, left, right }}>
            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor" 
                className="opacity-50 animate-love" viewBox="0 0 16 16" style={{ animationDelay: `${delay}ms` }}>
                <path d="m8 2.748-.717-.737C5.6.281 2.514.878 1.4 3.053c-.523 1.023-.641 2.5.314 4.385.92 1.815 2.834 3.989 6.286 6.357 3.452-2.368 5.365-4.542 6.286-6.357.955-1.886.838-3.362.314-4.385C13.486.878 10.4.28 8.717 2.01zM8 15C-7.333 4.868 3.279-3.04 7.824 1.143q.09.083.176.171a3 3 0 0 1 .176-.17C12.72-3.042 23.333 4.867 8 15" />
            </svg>
        </div>
    );
}

export function FirmanSection() {
    return (
        <section className="bg-light-dark pt-3 pb-4">
            <div className="container text-center">
                <h2 className="font-esthetic pt-2 pb-1 m-0" style={{ fontSize: '2.125rem' }}>Allah Subhanahu Wa Ta'ala berfirman</h2>

                <div className="bg-theme-auto mt-4 p-4 shadow-sm rounded-4" data-aos="fade-down" data-aos-duration="1500">
                    <p className="p-1 mb-2 font-garamond fst-italic" style={{ fontSize: '1.05rem', lineHeight: 1.7 }}>"Dan segala sesuatu Kami ciptakan berpasang-pasangan agar kamu mengingat (kebesaran Allah)."</p>
                    <p className="m-0 p-0 text-uppercase" style={{ fontSize: '0.7rem', letterSpacing: '0.15em', opacity: 0.5 }}>QS. Adh-Dhariyat: 49</p>
                </div>

                <div className="bg-theme-auto mt-3 p-4 shadow-sm rounded-4" data-aos="fade-down" data-aos-duration="1500" data-aos-delay="200">
                    <p className="p-1 mb-2 font-garamond fst-italic" style={{ fontSize: '1.05rem', lineHeight: 1.7 }}>"Dan sesungguhnya Dialah yang menciptakan pasangan laki-laki dan perempuan."</p>
                    <p className="m-0 p-0 text-uppercase" style={{ fontSize: '0.7rem', letterSpacing: '0.15em', opacity: 0.5 }}>QS. An-Najm: 45</p>
                </div>
            </div>
        </section>
    );
}

export function LoveStorySection({ data }) {
    const stories = Array.isArray(data.love_story) ? data.love_story : 
        (typeof data.love_story === 'string' && data.love_story.trim().length > 0 ? [{ title: "Kisah Kami", description: data.love_story }] : []);

    if (!stories || stories.length === 0) return null;

    return (
        <section className="bg-light-dark pt-2 pb-4">
            <div className="container">
                <div className="bg-theme-auto rounded-5 shadow p-3">
                    <h2 className="font-esthetic text-center py-2 mb-2" style={{ fontSize: '2.125rem' }}>Kisah Cinta</h2>

                    <div className="position-relative">
                        <div className="overflow-y-auto overflow-x-hidden p-2 with-scrollbar" style={{ maxHeight: '15rem' }}>
                            {stories.map((story, idx) => (
                                <div className="row" key={idx}>
                                    <div className="col-auto position-relative">
                                        <p className="position-relative d-flex justify-content-center align-items-center bg-theme-auto border border-secondary border-2 opacity-100 rounded-circle m-0 p-0 z-1" style={{ width: '2rem', height: '2rem' }}>
                                            {idx + 1}
                                        </p>
                                        {idx !== stories.length - 1 && (
                                            <hr className="position-absolute top-0 start-50 translate-middle-x border border-secondary h-100 z-0 opacity-100 m-0 rounded-4 shadow-none" />
                                        )}
                                    </div>
                                    <div className="col mt-1 mb-3 ps-0">
                                        <p className="fw-bold mb-2">{story.title || `Chapter ${idx + 1}`}</p>
                                        <p className="small mb-0">{story.description || story.text}</p>
                                    </div>
                                </div>
                            ))}
                        </div>
                    </div>
                </div>
            </div>
        </section>
    );
}

export function WeddingDateSection({ data }) {
    const [timeLeft, setTimeLeft] = useState({ days: 0, hours: 0, minutes: 0, seconds: 0 });

    useEffect(() => {
        if (!data.reception_datetime) return;
        const target = new Date(data.reception_datetime).getTime();
        
        const id = setInterval(() => {
            const diff = target - Date.now();
            if (diff <= 0) {
                setTimeLeft({ days: 0, hours: 0, minutes: 0, seconds: 0 });
                clearInterval(id);
                return;
            }
            setTimeLeft({
                days: Math.floor(diff / 86400000),
                hours: Math.floor((diff % 86400000) / 3600000),
                minutes: Math.floor((diff % 3600000) / 60000),
                seconds: Math.floor((diff % 60000) / 1000)
            });
        }, 1000);

        return () => clearInterval(id);
    }, [data.reception_datetime]);

    return (
        <section className="bg-white-black pb-2" id="wedding-date">
            <div className="container text-center">
                <p className="text-uppercase m-0 pt-3" style={{ fontSize: '0.65rem', letterSpacing: '0.2em', opacity: 0.5 }}>Counting Down To</p>
                <h2 className="font-esthetic py-3 m-0" style={{ fontSize: '2.5rem' }}>Moment Bahagia</h2>

                <div className="border rounded-pill shadow py-2 px-4 mt-2 mb-4">
                    <div className="row justify-content-center">
                        {[
                            { label: 'Hari', value: timeLeft.days },
                            { label: 'Jam', value: timeLeft.hours },
                            { label: 'Menit', value: timeLeft.minutes },
                            { label: 'Detik', value: timeLeft.seconds }
                        ].map((item) => (
                            <div className="col-3 p-1" key={item.label}>
                                <p className="d-inline m-0 p-0" style={{ fontSize: '1.25rem' }}>{item.value}</p>
                                <small className="ms-1 me-0 my-0 p-0 d-inline">{item.label}</small>
                            </div>
                        ))}
                    </div>
                </div>

                <p className="py-2 m-0" style={{ fontSize: '0.95rem' }}>
                    Dengan memohon rahmat dan ridho Allah Subhanahu Wa Ta'ala, insyaAllah kami akan menyelenggarakan acara:
                </p>

                <div className="position-relative">
                    <LoveAnimation top="0%" right="5%" delay={500} />
                </div>

                <div className="overflow-x-hidden">
                    <div className="py-2" data-aos="fade-right" data-aos-duration="1500">
                        <h2 className="font-esthetic m-0 py-2" style={{ fontSize: '2rem' }}>Akad</h2>
                        <p style={{ fontSize: '0.95rem' }}>Pukul {formatTime(data.akad_datetime)} WIB - Selesai</p>
                    </div>

                    <div className="py-2" data-aos="fade-left" data-aos-duration="1500">
                        <h2 className="font-esthetic m-0 py-2" style={{ fontSize: '2rem' }}>Resepsi</h2>
                        <p style={{ fontSize: '0.95rem' }}>Pukul {formatTime(data.reception_datetime)} WIB - Selesai</p>
                    </div>
                </div>

                <div className="position-relative">
                    <LoveAnimation top="0%" left="5%" delay={200} />
                </div>

                <div className="py-4" data-aos="fade-down" data-aos-duration="1500">
                    <a href={data.maps_link || "https://goo.gl"} target="_blank" rel="noreferrer" className="btn btn-outline-auto btn-sm rounded-pill shadow mb-2 px-3">
                        <i className="fa-solid fa-map-location-dot me-2"></i>Lihat Google Maps
                    </a>
                    <small className="d-block my-1">{data.reception_address || data.akad_address}</small>
                </div>
            </div>
        </section>
    );
}

export function GallerySection({ data, openModal }) {
    const galleryRaw = data?.media_files?.gallery || data?.media_files?.galeri || data.gallery_photo_urls || data.demo_gallery || [];
    const images = (Array.isArray(galleryRaw) ? galleryRaw : [galleryRaw])
        .map((item) => resolveMediaUrl(item))
        .filter(Boolean);

    if (!images.length) return null;

    // chunk images by 3 for simple carousel if there's many, or just map them 
    const chunks = [];
    for (let i=0; i<images.length; i+=3) {
        chunks.push(images.slice(i, i+3));
    }

    return (
        <section className="bg-white-black pb-5 pt-3" id="gallery">
            <div className="container">
                <div className="border rounded-5 shadow p-3">
                    <h2 className="font-esthetic text-center py-2 m-0" style={{ fontSize: '2.25rem' }}>Galeri</h2>

                    {chunks.map((chunk, cid) => (
                        <div id={`carousel-${cid}`} key={cid} className="carousel slide mt-4" data-bs-ride="carousel" data-aos="fade-up" data-aos-duration="1500">
                            <div className="carousel-indicators">
                                {chunk.map((_, i) => (
                                    <button key={i} type="button" data-bs-target={`#carousel-${cid}`} data-bs-slide-to={i} className={i === 0 ? "active" : ""}></button>
                                ))}
                            </div>
                            <div className="carousel-inner rounded-4">
                                {chunk.map((img, i) => (
                                    <div key={i} className={`carousel-item ${i === 0 ? "active" : ""}`}>
                                        <img src={img} alt={`gallery-${cid}-${i}`} className="d-block img-fluid cursor-pointer w-100" onClick={() => openModal(img)} style={{ aspectRatio: '1/1', objectFit: 'cover' }} />
                                    </div>
                                ))}
                            </div>
                            <button className="carousel-control-prev" type="button" data-bs-target={`#carousel-${cid}`} data-bs-slide="prev">
                                <span className="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span className="visually-hidden">Previous</span>
                            </button>
                            <button className="carousel-control-next" type="button" data-bs-target={`#carousel-${cid}`} data-bs-slide="next">
                                <span className="carousel-control-next-icon" aria-hidden="true"></span>
                                <span className="visually-hidden">Next</span>
                            </button>
                        </div>
                    ))}
                </div>
            </div>
        </section>
    );
}

export function GiftSection({ data }) {
    const hasGift = (!!data.bank_accounts && data.bank_accounts.length > 0) || !!data.qris_image;
    if (!hasGift) return null;

    const copyText = (text) => {
        navigator.clipboard.writeText(text);
        alert('Disalin: ' + text);
    };

    return (
        <section className="bg-light-dark pb-3">
            <div className="container text-center">
                <h2 className="font-esthetic pt-3 mb-3" style={{ fontSize: '2.5rem' }}>Love Gift</h2>
                <p className="mb-1" style={{ fontSize: '0.95rem' }}>Dengan hormat, bagi Anda yang ingin memberikan tanda kasih kepada kami, dapat melalui:</p>

                {data.bank_accounts?.map((bank, i) => (
                    <div className="bg-theme-auto rounded-4 shadow p-3 mx-4 mt-4 text-start" data-aos="fade-up" data-aos-duration="1500" key={i}>
                        <i className="fa-solid fa-money-bill-transfer me-2"></i>
                        <p className="d-inline">Transfer</p>
                        <div className="d-flex justify-content-between align-items-center mt-2">
                            <p className="m-0 p-0" style={{ fontSize: '0.95rem' }}><i className="fa-regular fa-user fa-sm me-1"></i>{bank.account_name}</p>
                            <button className="btn btn-outline-auto btn-sm shadow-sm rounded-4 py-0" style={{ fontSize: '0.75rem' }} data-bs-toggle="collapse" data-bs-target={`#collapseBank${i}`}>
                                <i className="fa-solid fa-circle-info fa-sm me-1"></i>Info
                            </button>
                        </div>
                        <div className="collapse" id={`collapseBank${i}`}>
                            <hr className="my-2 py-1" />
                            <p className="m-0" style={{ fontSize: '0.9rem' }}><i className="fa-solid fa-building-columns me-1"></i>{bank.bank_name}</p>
                            <div className="d-flex justify-content-between align-items-center mt-2">
                                <p className="m-0 p-0" style={{ fontSize: '0.85rem' }}><i className="fa-solid fa-credit-card me-1"></i>{bank.account_number}</p>
                                <button className="btn btn-outline-auto btn-sm shadow-sm rounded-4 py-0" style={{ fontSize: '0.75rem' }} onClick={() => copyText(bank.account_number)}>
                                    <i className="fa-solid fa-copy"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                ))}

                {data.qris_image && (
                    <div className="bg-theme-auto rounded-4 shadow p-3 mx-4 mt-4 text-start" data-aos="fade-up" data-aos-duration="1500">
                        <i className="fa-solid fa-qrcode fa-lg me-2"></i>
                        <p className="d-inline">Qris</p>
                        <div className="d-flex justify-content-between align-items-center mt-2">
                            <p className="m-0 p-0" style={{ fontSize: '0.95rem' }}><i className="fa-regular fa-user fa-sm me-1"></i>Scan QRIS</p>
                            <button className="btn btn-outline-auto btn-sm shadow-sm rounded-4 py-0" style={{ fontSize: '0.75rem' }} data-bs-toggle="collapse" data-bs-target="#collapseQris">
                                <i className="fa-solid fa-circle-info fa-sm me-1"></i>Info
                            </button>
                        </div>
                        <div className="collapse" id="collapseQris">
                            <hr className="my-2 py-1" />
                            <div className="d-flex justify-content-center align-items-center bg-white rounded p-2">
                                <img src={resolveMediaUrl(data.qris_image)} alt="qris" className="img-fluid rounded-3 mx-auto" style={{ maxWidth: '200px' }} />
                            </div>
                        </div>
                    </div>
                )}
            </div>
        </section>
    );
}

export function CommentSection() {
    // Form rendering for React (dummy state without actual backend hook right now, just UI layout for matching template)
    return (
        <section className="bg-light-dark my-0 pb-0 pt-3" id="comment">
            <div className="container">
                <div className="border rounded-5 shadow p-3 mb-2">
                    <h2 className="font-esthetic text-center mt-2 mb-3" style={{ fontSize: '2.5rem' }}>Ucapan &amp; Doa</h2>

                    <div className="mb-3">
                        <label className="form-label my-1"><i className="fa-solid fa-person me-2"></i>Nama</label>
                        <input type="text" className="form-control shadow-sm rounded-4" placeholder="Isikan Nama Anda" />
                    </div>

                    <div className="mb-3">
                        <label className="form-label my-1"><i className="fa-solid fa-person-circle-question me-2"></i>Presensi</label>
                        <select className="form-select shadow-sm rounded-4" defaultValue="0">
                            <option value="0">Konfirmasi Presensi</option>
                            <option value="1">✅ Datang</option>
                            <option value="2">❌ Berhalangan</option>
                        </select>
                    </div>

                    <div className="d-block mb-3">
                        <label className="form-label my-1"><i className="fa-solid fa-comment me-2"></i>Ucapan &amp; Doa</label>
                        <textarea className="form-control shadow-sm rounded-4" rows="4" placeholder="Tulis Ucapan dan Doa"></textarea>
                    </div>

                    <div className="d-grid">
                        <button className="btn btn-primary btn-sm rounded-4 shadow-sm m-1 py-2" style={{ letterSpacing: '0.05em' }}>
                            <i className="fa-solid fa-paper-plane me-2"></i>Kirim Ucapan
                        </button>
                    </div>
                </div>
            </div>
        </section>
    );
}

export function FooterSection({ data }) {
    return (
        <section className="bg-white-black py-2 no-gap-bottom">
            <div className="container text-center">
                <p className="pb-2 pt-4 font-garamond fst-italic" style={{ fontSize: '1.05rem', lineHeight: 1.8 }}>
                    {data.closing_message || 'Terima kasih atas perhatian dan doa restu Anda, yang menjadi kebahagiaan serta kehormatan besar bagi kami.'}
                </p>

                <h2 className="font-esthetic" style={{ fontSize: '2rem' }}>Wassalamualaikum Warahmatullahi Wabarakatuh</h2>
                <h2 className="font-arabic pt-4" style={{ fontSize: '2rem' }}>اَلْحَمْدُ لِلّٰهِ رَبِّ الْعٰلَمِيْنَۙ</h2>

                <hr className="my-3" />

                <div className="pb-3 text-center">
                    <small style={{ fontSize: '0.7rem', letterSpacing: '0.1em', opacity: 0.4 }}>Made with <i className="fa-solid fa-heart mx-1 text-danger"></i> by Digital Invitation</small>
                </div>
            </div>
        </section>
    );
}
