import { Flip } from 'gsap/Flip';
import { gsap, lenis, reducedMotion, ScrollTrigger } from '../core/motion.js';

gsap.registerPlugin(Flip);

export default function portfolio() {
    initFilters();
    initLightbox();
}

function initFilters() {
    const grid = document.querySelector('[data-work-grid]');
    const buttons = [...document.querySelectorAll('.filter-bar [data-filter]')];
    const empty = document.querySelector('[data-work-empty]');
    const status = document.querySelector('[data-filter-status]');
    if (!grid || !buttons.length) return;
    const cards = [...grid.children];

    const apply = (filter) => {
        const state = Flip.getState(cards);
        let shown = 0;
        cards.forEach((card) => {
            const match = filter === 'all' || card.dataset.filters.split(' ').includes(filter);
            card.classList.toggle('is-hidden', !match);
            // The wide first card only spans two columns in the unfiltered view.
            card.classList.toggle('work-card--wide', filter === 'all' && card === cards[0]);
            if (match) shown++;
        });
        buttons.forEach((b) => b.setAttribute('aria-pressed', String(b.dataset.filter === filter)));
        empty.hidden = shown > 0;
        status.textContent = `Showing ${shown} ${shown === 1 ? 'item' : 'items'}`;

        if (reducedMotion) return ScrollTrigger.refresh();
        Flip.from(state, {
            duration: 0.65,
            ease: 'power3.inOut',
            absolute: true,
            nested: true,
            onEnter: (els) => gsap.fromTo(els, { opacity: 0, scale: 0.92 }, { opacity: 1, scale: 1, duration: 0.5, ease: 'power3.out' }),
            onLeave: (els) => gsap.to(els, { opacity: 0, scale: 0.92, duration: 0.35 }),
            onComplete: () => ScrollTrigger.refresh(),
        });
    };

    document.addEventListener('click', (e) => {
        const button = e.target.closest('[data-filter]');
        if (button) apply(button.dataset.filter);
    });
}

function initLightbox() {
    const dialog = document.querySelector('[data-lightbox-dialog]');
    if (!dialog || typeof dialog.showModal !== 'function') return;
    const media = dialog.querySelector('[data-lightbox-media]');
    const title = dialog.querySelector('[data-lightbox-title]');
    let opener = null;

    document.querySelectorAll('[data-lightbox]').forEach((trigger) => {
        trigger.addEventListener('click', () => {
            opener = trigger;
            const { type, src } = trigger.dataset;
            title.textContent = trigger.dataset.title;
            media.replaceChildren();

            if (type === 'video') {
                const video = Object.assign(document.createElement('video'), { src, controls: true, autoplay: true, playsInline: true, preload: 'metadata' });
                video.setAttribute('aria-label', trigger.dataset.title);
                media.append(video);
            } else {
                const img = Object.assign(document.createElement('img'), { src, alt: `Meta Ads Manager screenshot: ${trigger.dataset.title}` });
                if (trigger.dataset.width) {
                    img.width = trigger.dataset.width;
                    img.height = trigger.dataset.height;
                }
                media.append(img);
            }

            lenis?.stop();
            dialog.showModal();
            if (!reducedMotion) gsap.fromTo(dialog, { opacity: 0, y: 30, scale: 0.97 }, { opacity: 1, y: 0, scale: 1, duration: 0.45, ease: 'power3.out' });
        });
    });

    const close = () => dialog.close();
    dialog.querySelector('[data-lightbox-close]').addEventListener('click', close);
    // Click on the backdrop (outside the frame) closes too.
    dialog.addEventListener('click', (e) => {
        if (e.target === dialog) close();
    });
    dialog.addEventListener('close', () => {
        media.querySelector('video')?.pause();
        media.replaceChildren();
        lenis?.start();
        opener?.focus({ preventScroll: true });
    });
}
