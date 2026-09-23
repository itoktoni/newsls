<!DOCTYPE html>
<html class="scroll-smooth" lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>{{ $title ?? config('app.name', 'LARAVEL') }}</title>
    <meta name="description" content="{{ $description ?? config('app.name', 'LARAVEL') }}">
    <meta name="author" content="{{ config('app.name', 'LARAVEL') }}">
    <meta property="og:title" content="{{ $title ?? config('app.name', 'LARAVEL') }}">
    <meta property="og:description" content="{{ $description ?? '' }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="website">
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@600;700;800&family=Hanken+Grotesk:wght@400;500;600&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "secondary-fixed-dim": "#f9bd00",
                        "error-container": "#ffdad6",
                        "outline-variant": "#becabf",
                        "background": "#f7f9fb",
                        "tertiary-fixed-dim": "#b9c7e0",
                        "tertiary": "#455368",
                        "surface-dim": "#d8dadc",
                        "error": "#ba1a1a",
                        "on-secondary-fixed": "#251a00",
                        "surface-container": "#eceef0",
                        "on-surface": "#191c1e",
                        "inverse-on-surface": "#eff1f3",
                        "on-tertiary-fixed-variant": "#3a485c",
                        "primary-fixed-dim": "#79daa4",
                        "on-secondary-fixed-variant": "#5b4300",
                        "secondary": "#785a00",
                        "secondary-fixed": "#ffdf9d",
                        "outline": "#6e7a71",
                        "on-error-container": "#93000a",
                        "primary-container": "#007a4d",
                        "surface": "#f7f9fb",
                        "on-primary-fixed-variant": "#005232",
                        "surface-container-high": "#e6e8ea",
                        "secondary-container": "#fdc008",
                        "on-background": "#191c1e",
                        "primary": "#005f3b",
                        "on-tertiary-container": "#e2ecff",
                        "surface-tint": "#006d44",
                        "on-tertiary": "#ffffff",
                        "on-secondary-container": "#6c5000",
                        "on-primary": "#ffffff",
                        "primary-fixed": "#95f6bf",
                        "on-tertiary-fixed": "#0d1c2f",
                        "inverse-primary": "#79daa4",
                        "tertiary-container": "#5d6b81",
                        "surface-variant": "#e0e3e5",
                        "surface-container-low": "#f2f4f6",
                        "tertiary-fixed": "#d5e3fd",
                        "on-secondary": "#ffffff",
                        "on-error": "#ffffff",
                        "surface-container-highest": "#e0e3e5",
                        "on-primary-fixed": "#002111",
                        "surface-container-lowest": "#ffffff",
                        "surface-bright": "#f7f9fb",
                        "on-primary-container": "#a2ffc9",
                        "on-surface-variant": "#3e4942",
                        "inverse-surface": "#2d3133"
                    },
                    borderRadius: {
                        "DEFAULT": "0.25rem",
                        "lg": "0.5rem",
                        "xl": "0.75rem",
                        "full": "9999px"
                    },
                    spacing: {
                        "unit": "4px",
                        "gutter": "1.5rem",
                        "section-gap": "4rem",
                        "element-gap": "1rem",
                        "container-padding": "2rem"
                    },
                    fontFamily: {
                        "headline-md": ["Manrope"],
                        "headline-xl": ["Manrope"],
                        "label-sm": ["Hanken Grotesk"],
                        "headline-lg": ["Manrope"],
                        "body-lg": ["Hanken Grotesk"],
                        "body-md": ["Hanken Grotesk"],
                        "label-md": ["Hanken Grotesk"]
                    },
                    fontSize: {
                        "headline-md": ["24px", { "lineHeight": "1.3", "letterSpacing": "0.01em", "fontWeight": "600" }],
                        "headline-xl": ["48px", { "lineHeight": "1.1", "letterSpacing": "0.02em", "fontWeight": "700" }],
                        "label-sm": ["12px", { "lineHeight": "1.2", "letterSpacing": "0.05em", "fontWeight": "500" }],
                        "headline-lg": ["32px", { "lineHeight": "1.2", "letterSpacing": "0.015em", "fontWeight": "600" }],
                        "body-lg": ["18px", { "lineHeight": "1.6", "letterSpacing": "0", "fontWeight": "400" }],
                        "body-md": ["16px", { "lineHeight": "1.5", "letterSpacing": "0", "fontWeight": "400" }],
                        "label-md": ["14px", { "lineHeight": "1.2", "letterSpacing": "0.05em", "fontWeight": "600" }]
                    }
                },
            },
        }
    </script>
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.4);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
        }

        .glass-dark {
            background: rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .emerald-gradient {
            background: linear-gradient(135deg, #005f3b 0%, #007a4d 100%);
        }

        .gold-shimmer {
            background: linear-gradient(90deg, transparent, rgba(255, 194, 14, 0.1), transparent);
            background-size: 200% 100%;
            animation: shimmer 3s infinite linear;
        }

        @keyframes shimmer {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }

        /* Hero Slider */
        .hero-main-slider { position: relative; overflow: hidden; height: 100vh; }
        .hero-main-slider .hs-track { display: flex; transition: transform 0.8s cubic-bezier(0.25,0.46,0.45,0.94); height: 100vh; }
        .hero-main-slider .hs-slide { min-width: 100%; height: 100vh; position: relative; display: flex; align-items: center; }
        .hero-main-slider .hs-slide img { width: 100%; height: 100%; object-fit: cover; }
        .hero-main-slider .hs-arrow {
            position: absolute; top: 100px; z-index: 20;
            width: 50px; height: 50px; border-radius: 50%;
            background: rgba(255,255,255,0.9); border: none;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; transition: all 0.3s; box-shadow: 0 4px 16px rgba(0,0,0,0.12);
        }
        .hero-main-slider .hs-arrow:hover { background: #005f3b; color: #fff; }
        .hero-main-slider .hs-arrow:hover .material-symbols-outlined { color: #fff; }
        .hero-main-slider .hs-arrow.hs-prev { right: 80px; }
        .hero-main-slider .hs-arrow.hs-next { right: 24px; }
        .hero-main-slider .hs-dots { position: absolute; bottom: 32px; left: 50%; transform: translateX(-50%); z-index: 20; display: flex; gap: 10px; }
        .hero-main-slider .hs-dot {
            width: 10px; height: 10px; border-radius: 50%; background: rgba(255,255,255,0.5);
            border: 2px solid rgba(255,255,255,0.7); cursor: pointer; transition: all 0.3s;
        }
        .hero-main-slider .hs-dot.active { background: #fdc008; border-color: #fdc008; width: 30px; border-radius: 6px; }
        .hero-main-slider .hs-content { opacity: 0; transform: translateY(24px); transition: opacity 0.6s ease 0.3s, transform 0.6s ease 0.3s; }
        .hero-main-slider .hs-slide.active .hs-content { opacity: 1; transform: translateY(0); }

        /* Client Slider */
        .client-slid { position: relative; overflow: hidden; padding: 8px 0; }
        .client-slid .cl-track { display: flex; transition: transform 0.6s cubic-bezier(0.25,0.46,0.45,0.94); }
        .client-slid .cl-item { min-width: 20%; display: flex; justify-content: center; align-items: center; padding: 16px; box-sizing: border-box; }
        .client-slid .cl-item .cl-card {
            width: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center;
            padding: 24px; border: 1px solid #becabf33; border-radius: 12px; background: rgba(255,255,255,0.5);
            transition: all 0.3s;
        }
        .client-slid .cl-item .cl-card:hover { border-color: #005f3b33; background: #fff; transform: translateY(-4px); box-shadow: 0 8px 24px rgba(0,0,0,0.08); }
        .client-slid .cl-btn {
            position: absolute; top: 50%; transform: translateY(-50%); z-index: 10;
            width: 38px; height: 38px; border-radius: 50%;
            background: #fff; border: 1px solid #becabf; display: flex; align-items: center; justify-content: center;
            cursor: pointer; transition: all 0.25s; box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        }
        .client-slid .cl-btn:hover { background: #005f3b; color: #fff; border-color: #005f3b; }
        .client-slid .cl-btn:hover .material-symbols-outlined { color: #fff; }
        .client-slid .cl-btn.cl-prev { left: 4px; }
        .client-slid .cl-btn.cl-next { right: 4px; }
        .cl-dots { display: flex; justify-content: center; gap: 8px; margin-top: 14px; }
        .cl-dot { width: 8px; height: 8px; border-radius: 50%; background: #becabf; cursor: pointer; transition: all 0.3s; }
        .cl-dot.active { background: #005f3b; width: 24px; border-radius: 4px; }
        @media (max-width: 640px) {
            .client-slid .cl-item { min-width: 100%; }
        }

        /* Competency Slider */
        .cmp-slid { position: relative; }
        .cmp-slid .cmp-track { display: flex; transition: transform 0.6s cubic-bezier(0.25,0.46,0.45,0.94); gap: 24px; }
        .cmp-slid .cmp-item { min-width: calc(33.333% - 16px); flex-shrink: 0; overflow: visible; }
        .cmp-nav { display: flex; align-items: center; gap: 10px; }
        .cmp-nav .cmp-btn {
            width: 44px; height: 44px; border-radius: 50%;
            background: #fff; border: 1px solid #becabf; display: flex; align-items: center; justify-content: center;
            cursor: pointer; transition: all 0.25s; box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }
        .cmp-nav .cmp-btn:hover { background: #005f3b; color: #fff; border-color: #005f3b; }
        .cmp-nav .cmp-btn:hover .material-symbols-outlined { color: #fff; }
        .cmp-dots { display: flex; justify-content: center; gap: 8px; margin-top: 20px; }
        .cmp-dot { width: 8px; height: 8px; border-radius: 50%; background: #becabf; cursor: pointer; transition: all 0.3s; }
        .cmp-dot.active { background: #005f3b; width: 24px; border-radius: 4px; }
        @media (max-width: 1024px) {
            .cmp-slid .cmp-item { min-width: calc(50% - 12px); }
        }
        @media (max-width: 640px) {
            .cmp-slid .cmp-item { min-width: 100%; }
        }

        .clip-path-slant { clip-path: polygon(25% 0%, 100% 0%, 100% 100%, 0% 100%); }
    </style>
</head>

<body class="bg-background text-on-surface font-body-md selection:bg-secondary-container/30">
    @include('cms::frontend.layouts.navigation')

    <main>
        @yield('content')
    </main>

    @include('cms::frontend.layouts.footer')

    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.swiper').forEach(function(el) {
                new Swiper(el, {
                    loop: true,
                    autoplay: { delay: 5000, disableOnInteraction: false },
                    pagination: { el: el.querySelector('.swiper-pagination'), clickable: true },
                    effect: 'slide',
                });
            });
        });

        // Hero Slider
        (function () {
            const slider = document.querySelector('.hero-main-slider');
            if (!slider) return;
            const track = slider.querySelector('.hs-track');
            const slides = slider.querySelectorAll('.hs-slide');
            const dots = slider.querySelectorAll('.hs-dot');
            const total = slides.length;
            let current = 0;
            let autoPlay;
            if (total === 0) return;

            function goTo(n) {
                const from = slides[current];
                if (from) from.classList.remove('active');
                const fromDot = dots[current];
                if (fromDot) fromDot.classList.remove('active');
                current = ((n % total) + total) % total;
                if (track) track.style.transform = `translateX(-${current * 100}%)`;
                const to = slides[current];
                if (to) to.classList.add('active');
                const toDot = dots[current];
                if (toDot) toDot.classList.add('active');
            }

            function startAuto() { stopAuto(); autoPlay = setInterval(() => goTo(current + 1), 5500); }
            function stopAuto() { clearInterval(autoPlay); }

            slider.querySelector('.hs-prev')?.addEventListener('click', () => { goTo(current - 1); startAuto(); });
            slider.querySelector('.hs-next')?.addEventListener('click', () => { goTo(current + 1); startAuto(); });
            dots.forEach(d => d.addEventListener('click', () => { goTo(parseInt(d.dataset.slide)); startAuto(); }));
            slider.addEventListener('mouseenter', stopAuto);
            slider.addEventListener('mouseleave', startAuto);

            let tx = 0;
            slider.addEventListener('touchstart', e => { tx = e.changedTouches[0].screenX; stopAuto(); }, { passive: true });
            slider.addEventListener('touchend', e => { const d = tx - e.changedTouches[0].screenX; if (Math.abs(d) > 50) { goTo(current + (d > 0 ? 1 : -1)); } startAuto(); }, { passive: true });

            const first = slides[0];
            if (first) first.classList.add('active');
            const firstDot = dots[0];
            if (firstDot) firstDot.classList.add('active');
            startAuto();
        })();

        // Client Slider
        (function () {
            const el = document.querySelector('.client-slid');
            if (!el) return;
            const track = el.querySelector('.cl-track');
            const items = el.querySelectorAll('.cl-item');
            const dots = document.querySelectorAll('.cl-dot');
            const total = items.length;
            let cur = 0, perPage = 5, autoPlay;

            function calcPer() { perPage = window.innerWidth < 640 ? 1 : window.innerWidth < 1024 ? 3 : 5; }
            function maxG() { return Math.max(0, total - perPage); }

            function goTo(g) {
                calcPer();
                if (g > maxG()) g = 0; if (g < 0) g = maxG();
                cur = g;
                const w = 100 / perPage;
                track.style.transform = `translateX(-${cur * w}%)`;
                dots.forEach((d, i) => d.classList.toggle('active', i === cur));
            }

            function startA() { stopA(); autoPlay = setInterval(() => goTo(cur + 1), 4000); }
            function stopA() { clearInterval(autoPlay); }

            el.querySelector('.cl-prev')?.addEventListener('click', () => { goTo(cur - 1); startA(); });
            el.querySelector('.cl-next')?.addEventListener('click', () => { goTo(cur + 1); startA(); });
            dots.forEach(d => d.addEventListener('click', () => { goTo(parseInt(d.dataset.group)); startA(); }));

            calcPer(); goTo(0); startA();
        })();

        // Competency Slider
        (function () {
            const el = document.querySelector('.cmp-slid');
            if (!el) return;
            const section = el.closest('section');
            if (!section) return;
            const nav = section.querySelector('.cmp-nav');
            const dotsWrap = section.querySelector('.cmp-dots');
            const track = el.querySelector('.cmp-track');
            if (!track) return;
            const items = el.querySelectorAll('.cmp-item');
            const dots = document.querySelectorAll('.cmp-dot');
            const total = items.length;
            if (total === 0) return;
            let cur = 0, perPage = 4, autoPlay;

            function calcPer() { perPage = window.innerWidth < 640 ? 1 : window.innerWidth < 1024 ? 2 : 3; }
            function maxG() { return Math.max(0, total - perPage); }

            function goTo(g) {
                calcPer();
                if (maxG() === 0) { track.style.transform = 'translateX(0)'; if (nav) nav.style.display = 'none'; if (dotsWrap) dotsWrap.style.display = 'none'; return; }
                if (nav) nav.style.display = ''; if (dotsWrap) dotsWrap.style.display = '';
                if (g > maxG()) g = 0; if (g < 0) g = maxG();
                cur = g;
                const itemW = (items[0]?.offsetWidth ?? 0) + 24;
                track.style.transform = `translateX(-${cur * itemW}px)`;
                dots.forEach((d, i) => d.classList.toggle('active', i === cur));
            }

            function startA() { stopA(); autoPlay = setInterval(() => goTo(cur + 1), 5000); }
            function stopA() { clearInterval(autoPlay); }

            section.querySelector('.cmp-prev')?.addEventListener('click', () => { goTo(cur - 1); startA(); });
            section.querySelector('.cmp-next')?.addEventListener('click', () => { goTo(cur + 1); startA(); });
            dots.forEach(d => d.addEventListener('click', () => { goTo(parseInt(d.dataset.group)); startA(); }));

            calcPer(); goTo(0); startA();
        })();
    </script>
</body>

</html>