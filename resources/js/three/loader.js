// Boots Three.js scenes for every [data-scene] element. Three.js and each
// scene are separate chunks, so pages only download what they render.
// Hero scenes load right after first paint; [data-lazy] ones when scrolled near.

const scenes = {
    core: () => import('./scenes/core.js'),
    constellation: () => import('./scenes/constellation.js'),
    shapes: () => import('./scenes/shapes.js'),
    wave: () => import('./scenes/wave.js'),
    skyline: () => import('./scenes/skyline.js'),
    globe: () => import('./scenes/globe.js'),
    ripple: () => import('./scenes/ripple.js'),
    worldmap: () => import('./scenes/worldmap.js'),
    tunnel: () => import('./scenes/tunnel.js'),
    skills: () => import('./scenes/skills.js'),
    orbit: () => import('./scenes/orbit.js'),
    gallery: () => import('./scenes/gallery.js'),
    radial: () => import('./scenes/radial.js'),
    plane: () => import('./scenes/plane.js'),
};

async function boot(host) {
    const loadScene = scenes[host.dataset.scene];
    if (!loadScene) return;
    try {
        const [{ createStage, supportsWebGL }, { default: build }] = await Promise.all([import('./engine.js'), loadScene()]);
        if (!supportsWebGL()) {
            host.classList.add('no-webgl');
            return;
        }
        const stage = createStage(host, host.dataset.scene === 'globe' ? { fov: 40, z: 7.4 } : {});
        // Label-based scenes draw text to canvases, so wait for web fonts.
        if (document.fonts?.ready) await document.fonts.ready;
        await build(stage, host);
        stage.start();
    } catch (error) {
        // Content never depends on the scene — fall back to the CSS gradient.
        host.classList.add('no-webgl');
        console.warn('3D scene unavailable:', error);
    }
}

export function initScenes() {
    const hosts = [...document.querySelectorAll('[data-scene]')];
    if (!hosts.length) return;

    const lazy = hosts.filter((h) => h.hasAttribute('data-lazy'));
    const eager = hosts.filter((h) => !h.hasAttribute('data-lazy'));

    const idle = window.requestIdleCallback || ((fn) => setTimeout(fn, 120));
    idle(() => eager.forEach(boot), { timeout: 600 });

    if (lazy.length) {
        const observer = new IntersectionObserver(
            (entries) => {
                entries.forEach((entry) => {
                    if (!entry.isIntersecting) return;
                    observer.unobserve(entry.target);
                    boot(entry.target);
                });
            },
            { rootMargin: '300px 0px' },
        );
        lazy.forEach((host) => observer.observe(host));
    }
}
