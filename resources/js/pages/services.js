import { gsap, reducedMotion } from '../core/motion.js';

// Process: the connecting line draws across while the step cards flip up in 3D.
export default function services() {
    const process = document.querySelector('[data-process]');
    if (!process || reducedMotion) return;

    const tl = gsap.timeline({ scrollTrigger: { trigger: process, start: 'top 75%', once: true } });
    tl.to('[data-process-progress]', { scaleX: 1, duration: 1.6, ease: 'power2.inOut' }).fromTo(
        process.querySelectorAll('[data-process-step]'),
        { opacity: 0, y: 40, rotationX: -60, transformOrigin: '50% 100%' },
        { opacity: 1, y: 0, rotationX: 0, duration: 0.9, stagger: 0.3, ease: 'power3.out' },
        0,
    );
}
