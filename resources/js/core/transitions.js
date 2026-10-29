import { gsap, reducedMotion } from './motion.js';

const KEY = 'raza-transition';
const HIDDEN_BELOW = 'inset(100% 0% 0% 0%)';
const SHOWN = 'inset(0% 0% 0% 0%)';
const HIDDEN_ABOVE = 'inset(0% 0% 100% 0%)';

/**
 * Curtain transition between pages. On click the curtain wipes up showing the
 * destination's name and a loading bar; the next page starts covered (flag in
 * sessionStorage, read by the inline <head> script), completes the bar and
 * wipes the curtain away.
 */
export function initPageTransitions(onEntered) {
    const curtain = document.querySelector('.page-curtain');
    const root = document.documentElement;
    if (!curtain) {
        onEntered();
        return;
    }

    const inner = curtain.querySelector('.page-curtain__inner');
    const logo = curtain.querySelector('.page-curtain__logo');
    const label = curtain.querySelector('[data-curtain-label]');
    const bar = curtain.querySelector('[data-curtain-bar]');
    const meta = curtain.querySelector('.page-curtain__meta');

    if (root.classList.contains('is-entering') && !reducedMotion) {
        // Arriving: finish the loading bar, lift the content, wipe the curtain off.
        gsap.timeline({ onComplete: () => root.classList.remove('is-entering') })
            .set(curtain, { clipPath: SHOWN })
            .fromTo(bar, { scaleX: 0.7 }, { scaleX: 1, duration: 0.35, ease: 'power2.out' })
            .to(inner, { y: -30, opacity: 0, duration: 0.4, ease: 'power2.in' }, 0.25)
            .to(curtain, { clipPath: HIDDEN_ABOVE, duration: 0.75, ease: 'power4.inOut' }, 0.3);
        gsap.delayedCall(0.6, onEntered);
    } else {
        root.classList.remove('is-entering');
        onEntered();
    }

    // Returning via back/forward cache: make sure the curtain is lifted.
    window.addEventListener('pageshow', (e) => {
        if (!e.persisted) return;
        gsap.killTweensOf([curtain, inner, logo, label, bar, meta]);
        gsap.set(curtain, { clipPath: HIDDEN_BELOW });
        root.classList.remove('is-entering');
    });

    if (reducedMotion) return;

    // Page names, keyed by path, from the (always present) mobile menu links.
    const labels = new Map([...document.querySelectorAll('[data-page-label]')].map((a) => [new URL(a.href).pathname, a.dataset.pageLabel]));

    document.addEventListener('click', (event) => {
        const link = event.target.closest('a[href]');
        if (
            !link ||
            event.defaultPrevented ||
            event.button !== 0 ||
            event.metaKey || event.ctrlKey || event.shiftKey || event.altKey ||
            link.target === '_blank' ||
            link.hasAttribute('download') ||
            link.origin !== location.origin ||
            (link.pathname === location.pathname && link.search === location.search)
        ) {
            return;
        }
        event.preventDefault();
        try {
            sessionStorage.setItem(KEY, '1');
        } catch {
            /* no storage — next page simply loads without the curtain */
        }

        label.textContent = labels.get(link.pathname) ?? 'Loading';
        gsap.timeline()
            .set(inner, { opacity: 1, y: 0 })
            .fromTo(curtain, { clipPath: HIDDEN_BELOW }, { clipPath: SHOWN, duration: 0.55, ease: 'power4.inOut' })
            .fromTo(logo, { scale: 0.6, opacity: 0 }, { scale: 1, opacity: 1, duration: 0.45, ease: 'back.out(2)' }, 0.2)
            .fromTo(label, { yPercent: 110 }, { yPercent: 0, duration: 0.55, ease: 'power4.out' }, 0.25)
            .fromTo(bar, { scaleX: 0 }, { scaleX: 0.7, duration: 0.6, ease: 'power2.out' }, 0.25)
            .fromTo(meta, { opacity: 0, y: 8 }, { opacity: 1, y: 0, duration: 0.35 }, 0.35)
            .call(() => (window.location.href = link.href), null, 0.62);
    });
}
