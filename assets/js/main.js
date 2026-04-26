// ===== SMARTUAE Main JavaScript =====

function initAutoSlider(id, cardWidth, intervalMs) {
    var el = document.getElementById(id);
    if (!el) return;
    var paused = false;
    intervalMs = intervalMs || 3000;
    function scrollNext() {
        if (el.scrollLeft >= el.scrollWidth - el.clientWidth - 10) {
            el.scrollTo({ left: 0, behavior: 'smooth' });
        } else {
            el.scrollBy({ left: cardWidth, behavior: 'smooth' });
        }
    }
    var timer = setInterval(function() { if (!paused) scrollNext(); }, intervalMs);
    el.addEventListener('mouseenter', function() { paused = true; });
    el.addEventListener('mouseleave', function() { paused = false; });
    el.addEventListener('touchstart', function() { paused = true; }, { passive: true });
    el.addEventListener('touchend', function() { setTimeout(function() { paused = false; }, 2500); }, { passive: true });
    var prevBtn = document.getElementById(id + '-prev');
    var nextBtn = document.getElementById(id + '-next');
    if (prevBtn) prevBtn.addEventListener('click', function() {
        paused = true;
        el.scrollBy({ left: -cardWidth, behavior: 'smooth' });
        setTimeout(function() { paused = false; }, 2500);
    });
    if (nextBtn) nextBtn.addEventListener('click', function() {
        paused = true; scrollNext();
        setTimeout(function() { paused = false; }, 2500);
    });
}

document.addEventListener('DOMContentLoaded', function() {

    // ===== Image Slider =====
    var slider = document.querySelector('.slider-track');
    var dots = document.querySelectorAll('.slider-dots .dot');
    if (slider) {
        var slides = slider.querySelectorAll('.slide');
        var current = 0;
        var total = slides.length;

        function goToSlide(index) {
            current = index;
            slider.style.transform = 'translateX(-' + (current * 100) + '%)';
            dots.forEach(function(d, i) { d.classList.toggle('active', i === current); });
        }

        var interval = setInterval(function() {
            goToSlide((current + 1) % total);
        }, 4000);

        dots.forEach(function(dot, i) {
            dot.addEventListener('click', function() {
                clearInterval(interval);
                goToSlide(i);
                interval = setInterval(function() { goToSlide((current + 1) % total); }, 4000);
            });
        });

        var startX = 0;
        slider.addEventListener('touchstart', function(e) { startX = e.touches[0].clientX; });
        slider.addEventListener('touchend', function(e) {
            var diff = startX - e.changedTouches[0].clientX;
            if (Math.abs(diff) > 50) {
                clearInterval(interval);
                if (diff > 0 && current < total - 1) goToSlide(current + 1);
                else if (diff < 0 && current > 0) goToSlide(current - 1);
                interval = setInterval(function() { goToSlide((current + 1) % total); }, 4000);
            }
        });
    }

    // ===== Search =====
    var searchInput = document.getElementById('globalSearch');
    if (searchInput) {
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                var query = this.value.trim();
                if (query) {
                    window.location.href = SITE_URL + '/pages/categories.php?search=' + encodeURIComponent(query);
                }
            }
        });
    }

    // ===== Scroll Reveal =====
    var style = document.createElement('style');
    style.textContent = '.scroll-reveal{opacity:0;transform:translateY(16px);transition:opacity 0.45s ease,transform 0.45s ease;}.scroll-reveal.visible{opacity:1;transform:translateY(0);}';
    document.head.appendChild(style);

    var revealEls = document.querySelectorAll(
        '.classified-card, .biz-card, .job-card, .classified-list-item, ' +
        '.pop-cat-card, .feature-card, .cv-section, .gallery-item, ' +
        '.cat-icon-item, .promo-card, .stats-banner, .hero-banner'
    );

    revealEls.forEach(function(el, i) {
        el.classList.add('scroll-reveal');
        el.style.transitionDelay = (i % 8) * 0.04 + 's';
    });

    var observer = new IntersectionObserver(function(entries) {
        entries.forEach(function(entry) {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.08, rootMargin: '0px 0px -30px 0px' });

    revealEls.forEach(function(el) { observer.observe(el); });

    // ===== Topbar shadow on scroll =====
    var topbar = document.querySelector('.topbar');
    if (topbar) {
        window.addEventListener('scroll', function() {
            if (window.scrollY > 40) {
                topbar.style.boxShadow = '0 4px 20px rgba(108, 92, 231, 0.08)';
            } else {
                topbar.style.boxShadow = 'none';
            }
        }, { passive: true });
    }

    // ===== Counter animation for stats =====
    document.querySelectorAll('.stat-num').forEach(function(el) {
        var text = el.textContent;
        var match = text.match(/(\d+)/);
        if (!match) return;

        var target = parseInt(match[1]);
        var suffix = text.replace(match[1], '');
        var animated = false;

        var statObserver = new IntersectionObserver(function(entries) {
            if (entries[0].isIntersecting && !animated) {
                animated = true;
                var startTime = null;
                function animate(time) {
                    if (!startTime) startTime = time;
                    var progress = Math.min((time - startTime) / 1200, 1);
                    var eased = 1 - Math.pow(1 - progress, 3);
                    el.textContent = Math.floor(eased * target) + suffix;
                    if (progress < 1) requestAnimationFrame(animate);
                }
                requestAnimationFrame(animate);
                statObserver.unobserve(el);
            }
        }, { threshold: 0.5 });
        statObserver.observe(el);
    });
});
