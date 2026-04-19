<script>
(function () {
    if (window.AnggitaLazyLoad) return;

    const applyNativeLazy = () => {
        document.querySelectorAll('img').forEach((img) => {
            if (!img.hasAttribute('loading')) {
                img.setAttribute('loading', 'lazy');
            }
        });
    };

    const hydrateDataSrc = (img) => {
        if (img.dataset && img.dataset.src && !img.dataset.lazyLoaded) {
            img.src = img.dataset.src;
            img.dataset.lazyLoaded = 'true';
        }
    };

    const initObservers = () => {
        const lazyImages = Array.from(document.querySelectorAll('img[data-src]'));
        if (!lazyImages.length) return;

        if ('IntersectionObserver' in window) {
            const io = new IntersectionObserver((entries, observer) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        hydrateDataSrc(entry.target);
                        observer.unobserve(entry.target);
                    }
                });
            }, { rootMargin: '120px 0px' });

            lazyImages.forEach((img) => io.observe(img));
        } else {
            lazyImages.forEach(hydrateDataSrc);
        }
    };

    const refresh = () => {
        applyNativeLazy();
        initObservers();
    };

    document.addEventListener('DOMContentLoaded', refresh);

    window.AnggitaLazyLoad = { refresh };
})();
</script>
