<div id="global-loader" class="global-loader" aria-hidden="true" role="status">
    <div class="cute-loader-content">
        <div class="sparkling-heart">
            <div class="sparkle s1">✨</div>
            <div class="sparkle s2">🌟</div>
            <div class="sparkle s3">✨</div>
            <div class="sparkle s4">⭐</div>
            
            <div class="heart-wrapper">
                <svg class="main-heart-svg" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M16 28.526l-1.422-1.294C7.41 20.73 2.666 16.43 2.666 11.127 2.666 6.824 6.043 3.447 10.346 3.447c2.434 0 4.773 1.135 6.136 3.136h.374c1.363-2.001 3.702-3.136 6.136-3.136 4.303 0 7.68 3.377 7.68 7.68 0 5.304-4.745 9.604-11.912 16.126L16 28.526z" fill="url(#cute-gradient)"/>
                    <defs>
                        <!-- A vibrant, modern, cute gradient -->
                        <linearGradient id="cute-gradient" x1="2" y1="3" x2="30" y2="28" gradientUnits="userSpaceOnUse">
                            <stop stop-color="#111111"/>
                            <stop offset="1" stop-color="#C5A059"/>
                        </linearGradient>
                    </defs>
                </svg>
            </div>
            <div class="heart-shadow"></div>
        </div>
        
        <p class="cute-loader-text">Menyiapkan harimu<span class="dot-anim"></span></p>
    </div>
</div>

<style>
    :root {
        --loader-bg-cute: rgba(255, 255, 255, 0.98);
        --text-cute: #111111;
        --color-gold: #C5A059;
    }

    .dark {
        --loader-bg-cute: rgba(10, 10, 10, 0.98);
        --text-cute: #FAF9F6;
    }

    .global-loader {
        position: fixed;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--loader-bg-cute);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        z-index: 99999;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .global-loader.is-visible {
        opacity: 1;
        pointer-events: all;
    }

    body.loading {
        pointer-events: none;
        overflow: hidden;
    }

    .cute-loader-content {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 2rem;
        transform: translateY(15px) scale(0.95);
        transition: transform 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    .global-loader.is-visible .cute-loader-content {
        transform: translateY(0) scale(1);
    }

    .sparkling-heart {
        position: relative;
        width: 80px;
        height: 100px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: flex-end;
    }

    .heart-wrapper {
        width: 60px;
        height: 60px;
        transform-origin: bottom center;
        animation: cuteBounce 1.2s cubic-bezier(0.28, 0.84, 0.42, 1) infinite;
        z-index: 2;
    }

    .main-heart-svg {
        width: 100%;
        height: 100%;
        filter: drop-shadow(0 8px 12px rgba(197, 160, 89, 0.3));
    }

    .heart-shadow {
        width: 40px;
        height: 8px;
        background: rgba(0, 0, 0, 0.1);
        border-radius: 50%;
        margin-top: 5px;
        animation: cuteShadow 1.2s cubic-bezier(0.28, 0.84, 0.42, 1) infinite;
    }

    .sparkle {
        position: absolute;
        z-index: 1;
        pointer-events: none;
        user-select: none;
        opacity: 0;
    }

    .s1 { top: 10px; left: -10px; font-size: 1.2rem; animation: floatUp 1.5s infinite 0s; }
    .s2 { top: 30px; right: -15px; font-size: 1rem; animation: floatUpRight 1.6s infinite 0.3s; }
    .s3 { top: -5px; right: 10px; font-size: 1.1rem; animation: floatUp 1.4s infinite 0.7s; }
    .s4 { bottom: 30px; left: -20px; font-size: 1.3rem; animation: floatUpLeft 1.7s infinite 0.5s; }

    .cute-loader-text {
        font-family: 'Space Grotesk', sans-serif;
        font-size: 1rem;
        font-weight: 500;
        letter-spacing: 0.2em;
        text-transform: uppercase;
        margin: 0;
        display: flex;
        align-items: center;
        background: linear-gradient(to right, #111111, #C5A059);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        animation: pulseText 2s ease-in-out infinite;
    }

    .dot-anim::after {
        content: '.';
        animation: dots 1.5s steps(4, end) infinite;
        display: inline-block;
        width: 1.5em;
        text-align: left;
        -webkit-text-fill-color: #C5A059; /* Use gold for dots */
    }

    /* Keyframes */
    @keyframes cuteBounce {
        0%, 100% {
            transform: translateY(0) scale(1.15, 0.85); /* Squashed on the ground */
        }
        40% {
            transform: translateY(-35px) scale(0.9, 1.1); /* Stretched moving up */
        }
        50% {
            transform: translateY(-40px) scale(1, 1); /* Apex */
        }
        60% {
            transform: translateY(-35px) scale(0.95, 1.05); /* Falling */
        }
    }

    @keyframes cuteShadow {
        0%, 100% {
            transform: scale(1.2);
            opacity: 0.4;
        }
        50% {
            transform: scale(0.5);
            opacity: 0.1;
        }
    }

    @keyframes floatUp {
        0% { transform: translateY(15px) scale(0) rotate(0deg); opacity: 0; }
        20% { opacity: 1; transform: translateY(5px) scale(1) rotate(10deg); }
        80% { opacity: 0.8; transform: translateY(-20px) scale(1.1) rotate(30deg); }
        100% { transform: translateY(-30px) scale(0) rotate(45deg); opacity: 0; }
    }
    @keyframes floatUpRight {
        0% { transform: translate(0, 15px) scale(0) rotate(-10deg); opacity: 0; }
        30% { opacity: 1; transform: translate(10px, 0) scale(1) rotate(10deg); }
        100% { transform: translate(25px, -20px) scale(0) rotate(40deg); opacity: 0; }
    }
    @keyframes floatUpLeft {
        0% { transform: translate(0, 10px) scale(0); opacity: 0; }
        30% { opacity: 1; transform: translate(-10px, -5px) scale(1) rotate(-10deg); }
        100% { transform: translate(-25px, -25px) scale(0) rotate(-30deg); opacity: 0; }
    }

    @keyframes dots {
        0%, 20% { content: ''; }
        40% { content: '.'; }
        60% { content: '..'; }
        80%, 100% { content: '...'; }
    }

    @keyframes pulseText {
        0%, 100% { filter: brightness(1); opacity: 0.8; }
        50% { filter: brightness(1.2); opacity: 1; }
    }

    @media (max-width: 640px) {
        .heart-wrapper {
            width: 50px;
            height: 50px;
        }
        .cute-loader-text {
            font-size: 1rem;
        }
        .sparkling-heart {
            height: 90px;
        }
    }
</style>

<script>
    (function () {
        if (window.AnggitaLoader) return;
        const loaderEl = document.getElementById('global-loader');
        if (!loaderEl) return;

        let skipNextLoader = false;

        const show = () => {
            if (skipNextLoader) {
                skipNextLoader = false;
                return;
            }
            loaderEl.classList.add('is-visible');
            document.body.classList.add('loading');
        };

        const hide = () => {
            loaderEl.classList.remove('is-visible');
            document.body.classList.remove('loading');
        };

        window.AnggitaLoader = { show, hide };

        const isPdfLink = (link) => {
            const href = link?.getAttribute('href') || '';
            return href.toLowerCase().includes('pdf') || href.toLowerCase().endsWith('.pdf');
        };

        const notify = (type, message, headline) => {
            window.AnggitaStatusModal?.show({ type, message, headline });
        };

        const makeIdempotencyKey = () => {
            if (window.crypto?.randomUUID) {
                return window.crypto.randomUUID();
            }
            return `idem-${Date.now()}-${Math.random().toString(16).slice(2)}`;
        };

        const ensureFormIdempotencyKey = (form) => {
            if (!form) return null;
            let input = form.querySelector('input[name="_idempotency_key"]');
            if (!input) {
                input = document.createElement('input');
                input.type = 'hidden';
                input.name = '_idempotency_key';
                form.appendChild(input);
            }
            if (!input.value) {
                input.value = makeIdempotencyKey();
            }
            return input.value;
        };

        const lockSubmitButtons = (form) => {
            form.querySelectorAll('button[type="submit"], input[type="submit"]').forEach((btn) => {
                if (!btn.dataset.originalLabel) {
                    btn.dataset.originalLabel = btn.tagName === 'BUTTON' ? btn.innerHTML : btn.value;
                }
                btn.disabled = true;
                btn.classList.add('opacity-60', 'cursor-not-allowed');
                if (btn.tagName === 'BUTTON') {
                    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Memproses...';
                } else {
                    btn.value = 'Memproses...';
                }
            });
        };

        document.querySelectorAll('form').forEach((form) => ensureFormIdempotencyKey(form));

        const inflightFetches = new Map();
        const nativeFetch = window.fetch ? window.fetch.bind(window) : null;
        if (nativeFetch && !window.AnggitaFetchIdempotent) {
            window.fetch = (input, init = {}) => {
                const method = ((init.method || 'GET') + '').toUpperCase();
                if (!['POST', 'PUT', 'PATCH', 'DELETE'].includes(method)) {
                    return nativeFetch(input, init);
                }

                const url = typeof input === 'string' ? input : (input?.url || window.location.href);
                const bodySig = typeof init.body === 'string'
                    ? init.body
                    : (init.body instanceof FormData ? JSON.stringify(Array.from(init.body.entries())) : '');
                const signature = `${method}::${url}::${bodySig}`;

                if (inflightFetches.has(signature)) {
                    return inflightFetches.get(signature);
                }

                const headers = new Headers(init.headers || {});
                if (!headers.has('X-Idempotency-Key')) {
                    headers.set('X-Idempotency-Key', makeIdempotencyKey());
                }

                const requestPromise = nativeFetch(input, {
                    ...init,
                    headers,
                }).finally(() => {
                    inflightFetches.delete(signature);
                });

                inflightFetches.set(signature, requestPromise);
                return requestPromise;
            };
            window.AnggitaFetchIdempotent = true;
        }

        const shouldIgnoreLink = (link) => {
            return !link ||
                link.hasAttribute('data-no-loader') ||
                link.target === '_blank' ||
                link.hasAttribute('download') ||
                isPdfLink(link) ||
                link.getAttribute('href')?.startsWith('#') ||
                link.getAttribute('href')?.startsWith('javascript:');
        };

        const markSkipOnce = () => {
            skipNextLoader = true;
            window.requestAnimationFrame(() => hide());
        };

        document.addEventListener('click', (event) => {
            const link = event.target.closest('a[href]');
            if (!link) return;
            if (link.dataset.processing === '1') {
                event.preventDefault();
                event.stopImmediatePropagation();
                notify('info', 'Aksi yang sama sedang diproses, mohon tunggu.');
                return;
            }

            link.dataset.processing = '1';
            window.setTimeout(() => {
                delete link.dataset.processing;
            }, 4000);

            if (shouldIgnoreLink(link)) {
                if (link.hasAttribute('data-no-loader') || link.hasAttribute('download') || isPdfLink(link)) {
                    markSkipOnce();
                }
                if (isPdfLink(link)) {
                    notify('success', 'File PDF sedang diunduh.');
                }
                return;
            }
            const url = new URL(link.href, window.location.href);
            if (url.origin !== window.location.origin) return;
            window.requestAnimationFrame(() => show());
        }, true);

        document.addEventListener('submit', (event) => {
            if (event.defaultPrevented) return;
            const form = event.target;
            if (!(form instanceof HTMLFormElement)) return;

            ensureFormIdempotencyKey(form);

            if (form.dataset.submitting === '1') {
                event.preventDefault();
                event.stopImmediatePropagation();
                notify('info', 'Permintaan sedang diproses, mohon tunggu.');
                return;
            }

            form.dataset.submitting = '1';
            lockSubmitButtons(form);
            markSkipOnce();
        }, true);

        window.addEventListener('beforeunload', () => {
            if (skipNextLoader) {
                skipNextLoader = false;
                return;
            }
            show();
        });

        window.addEventListener('pageshow', () => {
            hide();
        });

        if (document.readyState === 'complete') {
            hide();
        } else {
            window.addEventListener('load', () => hide());
            // Safety timeout: Hide loader after 6 seconds anyway to ensure interactivity
            setTimeout(() => hide(), 6000);
        }
    })();
</script>
