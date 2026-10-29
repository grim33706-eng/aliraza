import { initDock } from './core/dock.js';
import { heroIntro, initCounters, initMagnetic, initReveals, initScrollProgress, initSmoothScroll, initTilt, lenis, reducedMotion } from './core/motion.js';
import { initNav, initThemeToggle } from './core/nav.js';
import { initPageTransitions } from './core/transitions.js';
import { initScenes } from './three/loader.js';

// Page-specific behaviour, loaded only on the page that needs it.
const pages = {
    about: () => import('./pages/about.js'),
    services: () => import('./pages/services.js'),
    portfolio: () => import('./pages/portfolio.js'),
    results: () => import('./pages/results.js'),
    international: () => import('./pages/international.js'),
    contact: () => import('./pages/contact.js'),
};

const root = document.documentElement;

initSmoothScroll();
initNav();
initDock();
initThemeToggle();
initScenes();
initReveals();
initCounters();
initTilt();
initMagnetic();
initScrollProgress();
initPageTransitions(heroIntro);

pages[document.body.dataset.page]?.().then((module) => module.default());

// Back to top: smooth-scroll to the very top, then move keyboard focus to the content.
document.querySelector('[data-scroll-top]')?.addEventListener('click', (event) => {
    event.preventDefault();
    if (lenis) lenis.scrollTo(0, { duration: 1.4 });
    else window.scrollTo({ top: 0, behavior: reducedMotion ? 'auto' : 'smooth' });
    document.getElementById('main')?.focus({ preventScroll: true });
    history.replaceState(null, '', location.pathname + location.search);
});

root.classList.add('motion-ready');
