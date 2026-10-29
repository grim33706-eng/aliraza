import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import Lenis from 'lenis';

gsap.registerPlugin(ScrollTrigger);

export const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
const finePointer = window.matchMedia('(hover: hover) and (pointer: fine)').matches;

export let lenis = null;

/** Lenis smooth scrolling, driven by GSAP's ticker so ScrollTrigger stays in sync. */
export function initSmoothScroll() {
    if (reducedMotion) return null;

    lenis = new Lenis({ lerp: 0.11, wheelMultiplier: 1, touchMultiplier: 1.2 });
    lenis.on('scroll', ScrollTrigger.update);
    gsap.ticker.add((time) => lenis.raf(time * 1000));
    gsap.ticker.lagSmoothing(0);

    // Same-page anchors scroll smoothly and respect the fixed nav.
    document.addEventListener('click', (event) => {
        const link = event.target.closest('a[href*="#"]');
        if (!link || link.hasAttribute('data-scroll-top') || link.origin !== location.origin || link.pathname !== location.pathname || !link.hash) return;
        const target = link.hash === '#main' ? 0 : document.querySelector(link.hash);
        if (target === null) return;
        event.preventDefault();
        lenis.scrollTo(target, { offset: -90, duration: 1.1 });
        history.replaceState(null, '', link.hash === '#main' ? location.pathname : link.hash);
    });

    return lenis;
}

/**
 * Wraps each word in a mask so headlines can rise into view. Words inside
 * <em> keep the gradient accent via the `is-em` class.
 */
function splitWords(el) {
    if (el.dataset.splitDone) return [];
    el.dataset.splitDone = '1';
    const escape = (s) => s.replace(/&/g, '&amp;').replace(/</g, '&lt;');
    const words = [];
    el.childNodes.forEach((node) => {
        const em = node.nodeType === Node.ELEMENT_NODE;
        node.textContent
            .split(/ +/)
            .filter(Boolean)
            .forEach((w) => words.push({ w, em }));
    });
    el.setAttribute('aria-label', el.textContent.replace(/\s+/g, ' ').trim());
    el.innerHTML = words
        .map(({ w, em }) => `<span class="split-line" aria-hidden="true"><span class="split-word${em ? ' is-em' : ''}">${escape(w)}</span></span>`)
        .join(' ');
    return [...el.querySelectorAll('.split-word')];
}

/** Hero entrance timeline (runs after the page curtain lifts). */
export function heroIntro() {
    const hero = document.querySelector('[data-hero]');
    if (!hero) return;
    const title = hero.querySelector('[data-split]');
    const items = hero.querySelectorAll('[data-hero-item]');

    if (reducedMotion) {
        gsap.set([title, ...items], { opacity: 1 });
        return;
    }

    const words = title ? splitWords(title) : [];
    const tl = gsap.timeline({ defaults: { ease: 'power4.out' }, delay: 0.1 });
    if (title) {
        tl.set(title, { opacity: 1 }).from(words, { yPercent: 115, rotationX: -50, transformOrigin: '50% 100%', duration: 1.15, stagger: 0.05 });
    }
    tl.fromTo(items, { opacity: 0, y: 30, filter: 'blur(6px)' }, { opacity: 1, y: 0, filter: 'blur(0px)', duration: 0.9, stagger: 0.08, clearProps: 'filter' }, title ? '-=0.8' : 0);

    // Hero copy drifts up and fades as you scroll past it.
    hero.querySelectorAll(':scope > .container').forEach((inner) => {
        gsap.to(inner, {
            yPercent: -10,
            opacity: 0.2,
            ease: 'none',
            scrollTrigger: { trigger: hero, start: 'top top', end: 'bottom top', scrub: true },
        });
    });
}

/** 3D reveals for [data-reveal] and staggered children of [data-reveal-stagger]. */
export function initReveals() {
    if (reducedMotion) return;

    gsap.utils.toArray('[data-reveal]').forEach((el) => {
        gsap.fromTo(
            el,
            { opacity: 0, y: 50, rotationX: -12, transformPerspective: 1200, transformOrigin: '50% 100%' },
            { opacity: 1, y: 0, rotationX: 0, duration: 1.1, ease: 'power3.out', scrollTrigger: { trigger: el, start: 'top 88%', once: true } },
        );
    });

    gsap.utils.toArray('[data-reveal-stagger]').forEach((group) => {
        gsap.fromTo(
            group.children,
            { opacity: 0, y: 60, z: -80, rotationX: -24, transformPerspective: 1200, transformOrigin: '50% 0%' },
            { opacity: 1, y: 0, z: 0, rotationX: 0, duration: 1, ease: 'power3.out', stagger: 0.09, scrollTrigger: { trigger: group, start: 'top 86%', once: true } },
        );
    });

    // Giant outline words drift sideways as you scroll.
    gsap.utils.toArray('[data-watermark]').forEach((el) => {
        gsap.fromTo(el, { xPercent: -42 }, { xPercent: -58, ease: 'none', scrollTrigger: { trigger: el.parentElement, start: 'top top', end: 'bottom top', scrub: true } });
    });
    const wordmark = document.querySelector('[data-wordmark]');
    if (wordmark) {
        // Rises into place, finishing when it is fully on screen (a point the page can always reach).
        gsap.from(wordmark, {
            yPercent: 60,
            opacity: 0,
            ease: 'none',
            scrollTrigger: { trigger: wordmark, start: 'top bottom', end: 'bottom bottom', scrub: true },
        });
    }
}

/** Animated number counters; markup already holds the final value for no-JS/SEO. */
export function initCounters() {
    gsap.utils.toArray('[data-count]').forEach((el) => {
        const target = parseFloat(el.dataset.count);
        const decimals = parseInt(el.dataset.decimals || '0', 10);
        const prefix = el.dataset.prefix || '';
        const suffix = el.dataset.suffix || '';
        const format = (v) => prefix + v.toLocaleString('en-US', { minimumFractionDigits: decimals, maximumFractionDigits: decimals }) + suffix;
        if (reducedMotion || Number.isNaN(target)) return;

        const state = { v: 0 };
        el.textContent = format(0);
        const tween = () => gsap.to(state, { v: target, duration: target > 100 ? 2.2 : 1.6, ease: 'power2.out', onUpdate: () => (el.textContent = format(state.v)) });

        // Hero figures count up with the intro; the rest when scrolled into view.
        if (el.closest('[data-hero]')) {
            gsap.delayedCall(1, tween);
            return;
        }
        ScrollTrigger.create({ trigger: el, start: 'top 92%', once: true, onEnter: tween });
    });
}

/** Cursor spotlight on every card, plus 3D tilt on [data-tilt] — fine pointers only. */
export function initTilt() {
    if (!finePointer) return;

    document.querySelectorAll('.card').forEach((card) => {
        card.addEventListener('pointermove', (e) => {
            const r = card.getBoundingClientRect();
            card.style.setProperty('--mx', `${e.clientX - r.left}px`);
            card.style.setProperty('--my', `${e.clientY - r.top}px`);
        });
    });

    if (reducedMotion) return;
    document.querySelectorAll('[data-tilt]').forEach((card) => {
        const max = parseFloat(card.dataset.tiltMax || '7');
        gsap.set(card, { transformPerspective: 1000 });
        const rx = gsap.quickTo(card, 'rotationX', { duration: 0.6, ease: 'power3.out' });
        const ry = gsap.quickTo(card, 'rotationY', { duration: 0.6, ease: 'power3.out' });

        card.addEventListener('pointermove', (e) => {
            const r = card.getBoundingClientRect();
            ry(((e.clientX - r.left) / r.width - 0.5) * max * 2);
            rx((0.5 - (e.clientY - r.top) / r.height) * max * 2);
        });
        card.addEventListener('pointerleave', () => {
            rx(0);
            ry(0);
        });
    });
}

/** Buttons that lean toward the cursor, and hero chips with pointer parallax. */
export function initMagnetic() {
    if (reducedMotion || !finePointer) return;

    document.querySelectorAll('[data-magnetic]').forEach((el) => {
        const x = gsap.quickTo(el, 'x', { duration: 0.4, ease: 'power3.out' });
        const y = gsap.quickTo(el, 'y', { duration: 0.4, ease: 'power3.out' });
        el.addEventListener('pointermove', (e) => {
            const r = el.getBoundingClientRect();
            x((e.clientX - r.left - r.width / 2) * 0.25);
            y((e.clientY - r.top - r.height / 2) * 0.35);
        });
        el.addEventListener('pointerleave', () => {
            x(0);
            y(0);
        });
    });

    const depthEls = [...document.querySelectorAll('[data-depth]')];
    if (!depthEls.length) return;
    const movers = depthEls.map((el) => ({
        depth: parseFloat(el.dataset.depth),
        x: gsap.quickTo(el, 'x', { duration: 1, ease: 'power3.out' }),
        y: gsap.quickTo(el, 'y', { duration: 1, ease: 'power3.out' }),
        r: gsap.quickTo(el, 'rotationY', { duration: 1, ease: 'power3.out' }),
    }));
    window.addEventListener(
        'pointermove',
        (e) => {
            const nx = e.clientX / window.innerWidth - 0.5;
            const ny = e.clientY / window.innerHeight - 0.5;
            movers.forEach((m) => {
                m.x(nx * -30 * m.depth);
                m.y(ny * -22 * m.depth);
                m.r(nx * 14 * m.depth);
            });
        },
        { passive: true },
    );
}

/** Thin gradient bar showing how far down the page you are. */
export function initScrollProgress() {
    const bar = document.querySelector('[data-scroll-progress]');
    if (!bar) return;
    const set = gsap.quickSetter(bar, 'scaleX');
    ScrollTrigger.create({ start: 0, end: 'max', onUpdate: (self) => set(self.progress) });
}

export { gsap, ScrollTrigger };
