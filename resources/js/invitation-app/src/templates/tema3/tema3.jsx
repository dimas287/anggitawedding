import React, { useState, useEffect, useRef } from 'react';
import { WaveSeparator } from './components.jsx';
import { 
    HomeSection, BrideGroomSection, FirmanSection, LoveStorySection, 
    WeddingDateSection, GallerySection, GiftSection, CommentSection, FooterSection 
} from './sections.jsx';
import { resolveMediaUrl } from './utils.js';
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap/dist/js/bootstrap.bundle.min.js';
import '@fortawesome/fontawesome-free/css/all.min.css';
import 'aos/dist/aos.css';
import AOS from 'aos';

import './styles.css';

export default function Tema3({ invitation }) {
    const [opened, setOpened] = useState(false);
    const [playing, setPlaying] = useState(false);
    const [modalImg, setModalImg] = useState(null);
    const audioRef = useRef(null);

    // Default Preview Data
    const searchParams = new URLSearchParams(window.location.search);
    const toName = searchParams.get('to') || 'Tamu Undangan';

    const defaultData = {
        groom_name: 'Wahyu',
        bride_name: 'Riski',
        groom_short_name: 'Wahyu',
        bride_short_name: 'Riski',
        groom_father: 'Bapak',
        groom_mother: 'Ibu',
        bride_father: 'Bapak',
        bride_mother: 'Ibu',
        akad_datetime: '2025-12-12T08:00:00',
        akad_venue: 'Masjid Agung',
        akad_address: 'Jl. Merdeka',
        reception_datetime: '2025-12-12T11:00:00',
        reception_venue: 'Gedung Serbaguna',
        reception_address: 'Jl. Pemuda',
        maps_link: '#',
        love_story: 'Kisah cinta kami berawal dari pertemuan biasa dan saling mengerti.',
        closing_message: 'Terima kasih atas doa dan restunya.',
        music_file_url: null,
        media_files: {},
        ...invitation?.content
    };

    const data = defaultData;
    const hasMusic = !!data.music_file_url;

    useEffect(() => {
        if (hasMusic && audioRef.current) {
            audioRef.current.volume = 0.5;
            audioRef.current.loop = true;
        }

        AOS.init({ once: true });

        // Apply dark theme initially as per index.html `data-bs-theme="auto"`
        // The script may rely on this:
        document.documentElement.setAttribute('data-bs-theme', 'dark');
        
        return () => {
            document.documentElement.removeAttribute('data-bs-theme');
            document.body.style.overflow = 'auto'; // ensure scroll is enabled on exit
        };
    }, [hasMusic]);

    const handleOpen = () => {
        setOpened(true);
        if (hasMusic && audioRef.current) {
            audioRef.current.play().then(() => setPlaying(true)).catch(e => console.log("Audio block", e));
        }
    };

    const togglePlay = () => {
        if (!audioRef.current) return;
        if (playing) {
            audioRef.current.pause();
            setPlaying(false);
        } else {
            audioRef.current.play();
            setPlaying(true);
        }
    };

    const openModal = (src) => {
        setModalImg(src);
    };

    // Wave SVGs
    const wave1 = "M0,160L48,144C96,128,192,96,288,106.7C384,117,480,171,576,165.3C672,160,768,96,864,96C960,96,1056,160,1152,154.7C1248,149,1344,75,1392,37.3L1440,0L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z";
    const wave2 = "M0,192L40,181.3C80,171,160,149,240,149.3C320,149,400,171,480,165.3C560,160,640,128,720,128C800,128,880,160,960,186.7C1040,213,1120,235,1200,218.7C1280,203,1360,149,1400,122.7L1440,96L1440,0L1400,0C1360,0,1280,0,1200,0C1120,0,1040,0,960,0C880,0,800,0,720,0C640,0,560,0,480,0C400,0,320,0,240,0C160,0,80,0,40,0L0,0Z";
    const wave3 = "M0,96L30,106.7C60,117,120,139,180,154.7C240,171,300,181,360,186.7C420,192,480,192,540,181.3C600,171,660,149,720,154.7C780,160,840,192,900,208C960,224,1020,224,1080,208C1140,192,1200,160,1260,138.7C1320,117,1380,107,1410,101.3L1440,96L1440,320L1410,320C1380,320,1320,320,1260,320C1200,320,1140,320,1080,320C1020,320,960,320,900,320C840,320,780,320,720,320C660,320,600,320,540,320C480,320,420,320,360,320C300,320,240,320,180,320C120,320,60,320,30,320L0,320Z";
    const wave4 = "M0,224L34.3,234.7C68.6,245,137,267,206,266.7C274.3,267,343,245,411,234.7C480,224,549,224,617,213.3C685.7,203,754,181,823,197.3C891.4,213,960,267,1029,266.7C1097.1,267,1166,213,1234,192C1302.9,171,1371,181,1406,186.7L1440,192L1440,320L1405.7,320C1371.4,320,1303,320,1234,320C1165.7,320,1097,320,1029,320C960,320,891,320,823,320C754.3,320,686,320,617,320C548.6,320,480,320,411,320C342.9,320,274,320,206,320C137.1,320,69,320,34,320L0,320Z";

    // Set scroll lock when not opened
    useEffect(() => {
        if (!opened) {
            document.body.style.overflow = 'hidden';
        } else {
            document.body.style.overflow = '';
        }
    }, [opened]);

    const bgImgMain = resolveMediaUrl(data?.media_files?.cover) || data.photo_prewedding_url || resolveMediaUrl(data?.media_files?.main_background) || resolveMediaUrl(data.thumbnail);

    return (
        <div className="tema3-wrapper" style={{ fontFamily: "'Josefin Sans', sans-serif" }}>

            {/* Welcome Page Cover */}
            {!opened && (
                <div className="loading-page bg-white-black" style={{ opacity: 1, zIndex: 1060 }}>
                    <div className="d-flex justify-content-center align-items-center vh-100 overflow-y-auto">
                        <div className="d-flex flex-column text-center">
                            <h2 className="font-esthetic mb-4" style={{ fontSize: '2.25rem' }}>The Wedding Of</h2>
                            <img src={bgImgMain} alt="cover" className="img-center-crop rounded-circle border border-3 border-light shadow mb-4 mx-auto" />
                            <h2 className="font-esthetic mb-4" style={{ fontSize: '2.25rem' }}>
                                {data.groom_short_name || data.groom_name?.split(' ')[0]} &amp; {data.bride_short_name || data.bride_name?.split(' ')[0]}
                            </h2>
                            <div>
                                <p style={{ margin: 0, color: 'rgba(255,255,255,0.6)', fontSize: '0.9rem' }}>Kepada Yth Bapak/Ibu/Saudara/i</p>
                                <p style={{ fontSize: '1.25rem', marginTop: '0.5rem' }}>{toName}</p>
                            </div>
                            <button type="button" className="btn btn-light shadow rounded-4 mt-3 mx-auto" onClick={handleOpen}>
                                <i className="fa-solid fa-envelope-open fa-bounce me-2"></i>Open Invitation
                            </button>
                        </div>
                    </div>
                </div>
            )}

            {/* Main Content Rendered once opened (to keep DOM simple) */}
            {opened && (
                <div className="row m-0 p-0" style={{ opacity: 1 }}>
                    {hasMusic && <audio ref={audioRef} src={data.music_file_url} />}

                    {/* Desktop Split View Mode */}
                    <div className="sticky-top vh-100 d-none d-sm-block col-sm-5 col-md-6 col-lg-7 col-xl-8 col-xxl-9 overflow-y-hidden m-0 p-0">
                        <div className="position-relative bg-white-black d-flex justify-content-center align-items-center vh-100">
                            <div className="d-flex position-absolute w-100 h-100">
                                <div className="position-relative overflow-hidden vw-100">
                                    <img src={bgImgMain} alt="bg" className="bg-cover-home" style={{ opacity: 0.3, width: '100%', height: '100%', objectFit: 'cover' }} />
                                </div>
                            </div>

                            <div className="text-center p-4 bg-overlay-auto rounded-5 z-1">
                                <h2 className="font-esthetic mb-4" style={{ fontSize: '2rem' }}>{data.groom_short_name} &amp; {data.bride_short_name}</h2>
                            </div>
                        </div>
                    </div>

                    {/* Smartphone scroll mode */}
                    <div className="col-sm-7 col-md-6 col-lg-5 col-xl-4 col-xxl-3 m-0 p-0">
                        <main data-bs-spy="scroll" data-bs-target="#navbar-menu" tabIndex="0">
                            
                            <HomeSection data={data} openModal={openModal} />
                            <WaveSeparator pathData={wave1} />
                            
                            <BrideGroomSection data={data} openModal={openModal} />
                            <WaveSeparator pathData={wave2} />
                            
                            <FirmanSection />
                            <LoveStorySection data={data} />
                            <WaveSeparator pathData={wave3} />
                            
                            <WeddingDateSection data={data} />
                            
                            <GallerySection data={data} openModal={openModal} />
                            <WaveSeparator pathData={wave3} />
                            
                            <GiftSection data={data} />
                            
                            <CommentSection />
                            <WaveSeparator pathData={wave4} />
                            
                            <FooterSection data={data} />
                        </main>

                        {/* Navbar Bottom */}
                        <nav className="navbar navbar-expand sticky-bottom rounded-top-4 border-top p-0 bg-white-black" id="navbar-menu">
                            <ul className="navbar-nav nav-justified w-100 align-items-center flex-row">
                                <li className="nav-item">
                                    <a className="nav-link" href="#home"><i className="fa-solid fa-house"></i><span className="d-block" style={{ fontSize: '0.7rem' }}>Home</span></a>
                                </li>
                                <li className="nav-item">
                                    <a className="nav-link" href="#bride"><i className="fa-solid fa-user-group"></i><span className="d-block" style={{ fontSize: '0.7rem' }}>Mempelai</span></a>
                                </li>
                                <li className="nav-item">
                                    <a className="nav-link" href="#wedding-date"><i className="fa-solid fa-calendar-check"></i><span className="d-block" style={{ fontSize: '0.7rem' }}>Tanggal</span></a>
                                </li>
                                <li className="nav-item">
                                    <a className="nav-link" href="#gallery"><i className="fa-solid fa-images"></i><span className="d-block" style={{ fontSize: '0.7rem' }}>Galeri</span></a>
                                </li>
                                <li className="nav-item">
                                    <a className="nav-link" href="#comment"><i className="fa-solid fa-comments"></i><span className="d-block" style={{ fontSize: '0.7rem' }}>Ucapan</span></a>
                                </li>
                            </ul>
                        </nav>
                    </div>

                    {/* Modal Image purely in React state */}
                    {modalImg && (
                        <div className="modal fade show d-block" style={{ background: 'rgba(0,0,0,0.8)' }} tabIndex="-1" aria-hidden="true" onClick={() => setModalImg(null)}>
                            <div className="modal-dialog modal-dialog-centered" onClick={(e) => e.stopPropagation()}>
                                <div className="modal-content rounded-4 border border-0">
                                    <div className="modal-body p-0 position-relative">
                                        <button className="btn d-flex justify-content-center align-items-center bg-overlay-auto p-2 m-1 rounded-circle border shadow-sm position-absolute top-0 end-0 z-1" onClick={() => setModalImg(null)}>
                                            <i className="fa-solid fa-circle-xmark"></i>
                                        </button>
                                        <img src={modalImg} className="img-fluid w-100 rounded-4" alt="zoom" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    )}
                </div>
            )}

            {/* Floating Music Button */}
            {opened && hasMusic && (
                <div className="d-flex position-fixed flex-column" style={{ bottom: '10vh', right: '2vh', zIndex: 1030 }}>
                    <button type="button" className="btn bg-light-dark border btn-sm rounded-circle shadow-sm mt-3" style={{ width: 40, height: 40 }} onClick={togglePlay}>
                        <i className={`fa-solid ${playing ? 'fa-music spin-button' : 'fa-pause'}`}></i>
                    </button>
                </div>
            )}
        </div>
    );
}
