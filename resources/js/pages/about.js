import { gsap, reducedMotion } from '../core/motion.js';

export default function about() {
    if (reducedMotion) return;
    initTimeline();
    initEducationTrack();
}

// Education: the 2021 → 2025 track fills in and each year lights up in turn.
function initEducationTrack() {
    const track = document.querySelector('[data-edu-track]');
    if (!track) return;
    const tl = gsap.timeline({ scrollTrigger: { trigger: track, start: 'top 85%', once: true } });
    tl.from('[data-edu-fill]', { scaleX: 0, duration: 1.6, ease: 'power2.inOut' }).from(
        track.querySelectorAll('[data-edu-year]'),
        { opacity: 0, y: 10, scale: 0.6, duration: 0.5, stagger: 0.32, ease: 'back.out(2)' },
        0,
    );
}

// Career timeline: the rail fills as you scroll and each role swings in, in 3D, from its side.
function initTimeline() {
    const timeline = document.querySelector('[data-timeline]');
    if (!timeline) return;

    gsap.to('[data-timeline-progress]', {
        scaleY: 1,
        ease: 'none',
        scrollTrigger: { trigger: timeline, start: 'top 65%', end: 'bottom 65%', scrub: 0.6 },
    });

    const desktop = window.matchMedia('(min-width: 992px)').matches;
    timeline.querySelectorAll('[data-timeline-item]').forEach((item, i) => {
        const fromLeft = !desktop || i % 2 === 0;
        gsap.fromTo(
            item.querySelector('.timeline__card'),
            { opacity: 0, x: fromLeft ? -50 : 50, rotationY: fromLeft ? 35 : -35, transformOrigin: fromLeft ? '100% 50%' : '0% 50%' },
            { opacity: 1, x: 0, rotationY: 0, duration: 1.1, ease: 'power3.out', scrollTrigger: { trigger: item, start: 'top 82%', once: true } },
        );
        gsap.from(item.querySelector('.timeline__dot'), {
            scale: 0,
            duration: 0.6,
            ease: 'back.out(3)',
            scrollTrigger: { trigger: item, start: 'top 70%', once: true },
        });
    });
}
