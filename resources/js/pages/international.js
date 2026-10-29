import { reducedMotion, ScrollTrigger } from '../core/motion.js';

// Scroll story: each region chapter turns the sticky globe to its markets.
// Country cards focus a single market on hover, focus or tap.
export default function international() {
    const globe = document.querySelector('.globe-stage[data-scene="globe"]');
    const chapters = [...document.querySelectorAll('[data-region]')];
    const dots = [...document.querySelectorAll('[data-story-dot]')];
    const cards = [...document.querySelectorAll('[data-market]')];
    if (!globe) return;

    // Also stored on the element, so a globe that boots later can pick it up.
    const focus = (detail) => {
        globe.dataset.focus = JSON.stringify(detail);
        globe.dispatchEvent(new CustomEvent('globe:focus', { detail }));
    };
    let current = 0;

    const activate = (index) => {
        current = index;
        chapters.forEach((c, i) => c.classList.toggle('is-active', i === index));
        dots.forEach((d, i) => d.classList.toggle('is-active', i === index));
        focus(JSON.parse(chapters[index].dataset.region));
    };

    chapters.forEach((chapter, i) => {
        ScrollTrigger.create({
            trigger: chapter,
            start: 'top 60%',
            end: 'bottom 40%',
            onToggle: (self) => self.isActive && activate(i),
        });
    });
    if (chapters.length) activate(0);

    cards.forEach((card) => {
        const market = Number(card.dataset.market);
        card.addEventListener('pointerenter', () => {
            card.classList.add('is-active');
            focus(market);
        });
        card.addEventListener('pointerleave', () => {
            card.classList.remove('is-active');
            focus(JSON.parse(chapters[current].dataset.region));
        });
        card.addEventListener('focus', () => focus(market));
        card.addEventListener('click', () => {
            cards.forEach((c) => c.classList.toggle('is-active', c === card));
            focus(market);
        });
    });

    if (reducedMotion) chapters.forEach((c) => c.classList.add('is-active'));
}
