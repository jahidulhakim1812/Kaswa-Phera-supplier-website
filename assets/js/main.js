document.addEventListener('DOMContentLoaded', function () {
    var navToggle = document.getElementById('navToggle');
    var mainNav = document.getElementById('mainNav');
    if (navToggle && mainNav) {
        navToggle.addEventListener('click', function () {
            mainNav.classList.toggle('open');
        });
    }

    // Auto-dismiss alerts
    document.querySelectorAll('.alert').forEach(function (el) {
        setTimeout(function () {
            el.style.transition = 'opacity .4s ease';
            el.style.opacity = '0';
            setTimeout(function () { el.remove(); }, 400);
        }, 5000);
    });

    // ---------- Homepage hero slider ----------
    var slider = document.getElementById('heroSlider');
    if (slider) {
        var slides = Array.prototype.slice.call(slider.querySelectorAll('.hero-slide'));
        var dots = Array.prototype.slice.call(slider.querySelectorAll('.hero-dot'));
        var prevBtn = document.getElementById('heroPrev');
        var nextBtn = document.getElementById('heroNext');
        var current = 0;
        var timer = null;
        var AUTOPLAY_MS = 6000;

        function goTo(index) {
            index = (index + slides.length) % slides.length;
            slides[current].classList.remove('is-active');
            dots[current] && dots[current].classList.remove('is-active');
            current = index;
            slides[current].classList.add('is-active');
            dots[current] && dots[current].classList.add('is-active');
        }

        function next() { goTo(current + 1); }
        function prev() { goTo(current - 1); }

        function startAutoplay() {
            stopAutoplay();
            timer = setInterval(next, AUTOPLAY_MS);
        }
        function stopAutoplay() {
            if (timer) clearInterval(timer);
        }

        if (nextBtn) nextBtn.addEventListener('click', function () { next(); startAutoplay(); });
        if (prevBtn) prevBtn.addEventListener('click', function () { prev(); startAutoplay(); });
        dots.forEach(function (dot, i) {
            dot.addEventListener('click', function () { goTo(i); startAutoplay(); });
        });

        slider.addEventListener('mouseenter', stopAutoplay);
        slider.addEventListener('mouseleave', startAutoplay);

        startAutoplay();
    }

    // Back-to-top floating button
    var backToTop = document.getElementById('backToTop');
    if (backToTop) {
        var toggleBackToTop = function () {
            if (window.scrollY > 400) {
                backToTop.classList.add('is-visible');
            } else {
                backToTop.classList.remove('is-visible');
            }
        };
        toggleBackToTop();
        window.addEventListener('scroll', toggleBackToTop, { passive: true });
        backToTop.addEventListener('click', function () {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }
});
