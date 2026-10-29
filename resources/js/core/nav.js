import { gsap, lenis, reducedMotion } from './motion.js';

export function initNav() {
    const nav = document.querySelector('[data-nav]');
    const toggle = document.querySelector('[data-menu-toggle]');
    const menu = document.querySelector('[data-mobile-menu]');
    if (!nav) return;

    // Glass background once scrolled. The bar slides away while scrolling down
    // and comes back as soon as scrolling stops (or on any scroll up).
    let lastY = window.scrollY;
    let idleTimer = 0;
    const show = () => nav.classList.remove('is-hidden');
    const onScroll = () => {
        const y = window.scrollY;
        nav.classList.toggle('is-scrolled', y > 20);
        if (y > 160 && y > lastY + 2 && !document.body.classList.contains('menu-open')) nav.classList.add('is-hidden');
        else if (y < lastY) show();
        lastY = y;
        clearTimeout(idleTimer);
        idleTimer = setTimeout(show, 260);
    };
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();

    initIndicator(nav);

    if (!toggle || !menu) return;
    const links = menu.querySelectorAll('li, .mobile-menu__foot');

    const open = () => {
        menu.hidden = false;
        toggle.setAttribute('aria-expanded', 'true');
        toggle.setAttribute('aria-label', 'Close menu');
        document.body.classList.add('menu-open');
        lenis?.stop();
        if (!reducedMotion) {
            gsap.fromTo(menu, { clipPath: 'circle(0% at 100% 0%)' }, { clipPath: 'circle(150% at 100% 0%)', duration: 0.7, ease: 'power3.inOut' });
            gsap.fromTo(links, { opacity: 0, y: 30 }, { opacity: 1, y: 0, duration: 0.6, stagger: 0.05, delay: 0.25, ease: 'power3.out' });
        }
        menu.querySelector('a')?.focus({ preventScroll: true });
    };

    const close = (returnFocus = true) => {
        toggle.setAttribute('aria-expanded', 'false');
        toggle.setAttribute('aria-label', 'Open menu');
        document.body.classList.remove('menu-open');
        lenis?.start();
        const done = () => (menu.hidden = true);
        if (reducedMotion) done();
        else gsap.to(menu, { clipPath: 'circle(0% at 100% 0%)', duration: 0.5, ease: 'power3.inOut', onComplete: done });
        if (returnFocus) toggle.focus({ preventScroll: true });
    };

    toggle.addEventListener('click', () => (menu.hidden ? open() : close()));
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && !menu.hidden) close();
    });
    window.matchMedia('(min-width: 1100px)').addEventListener('change', (e) => {
        if (e.matches && !menu.hidden) close(false);
    });
}

/** A pill that sits behind the active link and glides to whichever link is hovered. */
function initIndicator(nav) {
    const list = nav.querySelector('[data-nav-links]');
    const pill = nav.querySelector('[data-nav-indicator]');
    if (!list || !pill) return;
    const links = [...list.querySelectorAll('.nav-link')];
    const active = links.find((l) => l.classList.contains('is-active'));

    const moveTo = (link) => {
        if (!link) {
            pill.style.opacity = '0';
            return;
        }
        pill.style.opacity = '1';
        pill.style.width = `${link.offsetWidth}px`;
        pill.style.transform = `translateX(${link.offsetLeft}px)`;
    };

    links.forEach((link) => {
        link.addEventListener('pointerenter', () => moveTo(link));
        link.addEventListener('focus', () => moveTo(link));
    });
    list.addEventListener('pointerleave', () => moveTo(active));
    // Fonts change link widths, so position once they've loaded.
    (document.fonts?.ready || Promise.resolve()).then(() => moveTo(active));
    window.addEventListener('resize', () => moveTo(active));
}

export function initThemeToggle() {
    const button = document.querySelector('[data-theme-toggle]');
    if (!button) return;
    const root = document.documentElement;

    const label = () => button.setAttribute('aria-label', root.dataset.theme === 'light' ? 'Switch to dark theme' : 'Switch to light theme');
    label();

    button.addEventListener('click', () => {
        const next = root.dataset.theme === 'light' ? 'dark' : 'light';
        root.dataset.theme = next;
        try {
            localStorage.setItem('raza-theme', next);
        } catch {
            /* storage unavailable — theme still applies for this page */
        }
        label();
        document.dispatchEvent(new CustomEvent('themechange', { detail: next }));
    });
}
