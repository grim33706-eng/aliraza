import { gsap, reducedMotion } from '../core/motion.js';

export default function results() {
    initRadialLegend();
    if (reducedMotion) return;

    // Bars grow from the baseline; values fade in after.
    const bars = gsap.utils.toArray('[data-bar] rect');
    if (bars.length) {
        const trigger = { trigger: '.bar-chart', start: 'top 80%', once: true };
        gsap.from(bars, { scaleY: 0, transformOrigin: '50% 100%', duration: 1.1, ease: 'power3.out', stagger: 0.05, scrollTrigger: trigger });
        gsap.from('.bar-chart__value', { opacity: 0, y: 6, duration: 0.4, stagger: 0.05, delay: 0.6, scrollTrigger: trigger });
    }

    const fills = gsap.utils.toArray('[data-hbar]');
    if (fills.length) {
        gsap.from(fills, { scaleX: 0, transformOrigin: '0% 50%', duration: 1.1, ease: 'power3.out', stagger: 0.07, scrollTrigger: { trigger: '.hbar-chart', start: 'top 82%', once: true } });
    }

    // Milestones flip up from the floor like steps being built.
    const steps = gsap.utils.toArray('[data-stair]');
    if (steps.length) {
        gsap.from(steps, {
            rotationX: -90,
            opacity: 0,
            transformOrigin: '50% 100%',
            duration: 1,
            ease: 'back.out(1.4)',
            stagger: 0.12,
            scrollTrigger: { trigger: '[data-stairs]', start: 'top 80%', once: true },
        });
    }
}

// Hovering a campaign in the legend highlights its bar in the 3D chart.
function initRadialLegend() {
    const stage = document.querySelector('[data-scene="radial"]');
    const items = document.querySelectorAll('[data-radial-index]');
    if (!stage || !items.length) return;
    const focus = (i) => stage.dispatchEvent(new CustomEvent('radial:focus', { detail: i }));

    items.forEach((item) => {
        const i = Number(item.dataset.radialIndex);
        const on = () => {
            items.forEach((x) => x.classList.toggle('is-active', x === item));
            focus(i);
        };
        const off = () => {
            item.classList.remove('is-active');
            focus(-1);
        };
        item.addEventListener('pointerenter', on);
        item.addEventListener('focus', on);
        item.addEventListener('pointerleave', off);
        item.addEventListener('blur', off);
    });
}
