import { AdditiveBlending, Color, NormalBlending, PerspectiveCamera, Scene, WebGLRenderer } from 'three';

export const prefersReducedMotion = () => window.matchMedia('(prefers-reduced-motion: reduce)').matches;

export const isLowPower = () =>
    window.matchMedia('(max-width: 767px)').matches ||
    (navigator.hardwareConcurrency || 8) <= 4 ||
    Boolean(navigator.connection && navigator.connection.saveData);

export function supportsWebGL() {
    try {
        const canvas = document.createElement('canvas');
        return Boolean(window.WebGLRenderingContext && (canvas.getContext('webgl2') || canvas.getContext('webgl')));
    } catch {
        return false;
    }
}

/** Reads the current theme's colour tokens so scenes match the page. */
export function themeColors() {
    const style = getComputedStyle(document.documentElement);
    const read = (name) => new Color(style.getPropertyValue(name).trim() || '#ffffff');

    const light = document.documentElement.dataset.theme === 'light';

    return {
        light,
        accent: read('--accent'),
        // Accent for 3D marks: the lime glows on dark; on the light theme a
        // deeper but still vivid green reads better than the muted text ink.
        vivid: light ? new Color('#5c9e0f') : read('--accent'),
        // Base violet for solid 3D bars/shapes: lifted on light so they don't look heavy.
        solid: light ? read('--iris').lerp(new Color(0xffffff), 0.35) : read('--iris'),
        // Additive blending glows on dark backgrounds but washes out to white on light ones.
        blend: light ? NormalBlending : AdditiveBlending,
        accentInk: read('--accent-ink'),
        iris: read('--iris'),
        coral: read('--coral'),
        text: read('--text'),
        muted: read('--muted'),
        bg: read('--bg'),
    };
}

/**
 * Creates a renderer/scene/camera bound to `host` (an element containing a
 * <canvas>). Handles sizing, DPR caps, pausing when off-screen or the tab is
 * hidden, pointer smoothing, theme changes and reduced motion (static frame).
 */
export function createStage(host, { fov = 45, z = 10 } = {}) {
    const canvas = host.querySelector('canvas');
    const lowPower = isLowPower();
    const reduced = prefersReducedMotion();

    const renderer = new WebGLRenderer({
        canvas,
        alpha: true,
        antialias: !lowPower,
        powerPreference: 'high-performance',
    });
    renderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, lowPower ? 1.25 : 1.75));
    renderer.setClearColor(0x000000, 0);

    const scene = new Scene();
    const camera = new PerspectiveCamera(fov, 1, 0.1, 200);
    camera.position.z = z;

    const pointer = { x: 0, y: 0, sx: 0, sy: 0 };
    const frameFns = [];
    const resizeFns = [];
    const themeFns = [];
    let lastTime = performance.now();
    let colors = themeColors();
    let visible = false;
    let rafId = 0;
    let size = { width: 1, height: 1 };
    let elapsed = 0;

    const render = () => renderer.render(scene, camera);

    const tick = () => {
        rafId = 0;
        const now = performance.now();
        const dt = Math.min((now - lastTime) / 1000, 0.05);
        lastTime = now;
        elapsed += dt;
        pointer.sx += (pointer.x - pointer.sx) * Math.min(1, dt * 4);
        pointer.sy += (pointer.y - pointer.sy) * Math.min(1, dt * 4);
        frameFns.forEach((fn) => fn(elapsed, dt));
        render();
        loop();
    };

    const loop = () => {
        if (reduced || !visible || document.hidden || rafId) return;
        rafId = requestAnimationFrame(tick);
    };

    const stop = () => {
        if (rafId) cancelAnimationFrame(rafId);
        rafId = 0;
    };

    const resize = () => {
        const width = host.clientWidth || 1;
        const height = host.clientHeight || 1;
        size = { width, height };
        renderer.setSize(width, height, false);
        camera.aspect = width / height;
        camera.updateProjectionMatrix();
        resizeFns.forEach((fn) => fn(size));
        if (reduced || !rafId) render();
    };

    const onPointerMove = (event) => {
        pointer.x = (event.clientX / window.innerWidth) * 2 - 1;
        pointer.y = -((event.clientY / window.innerHeight) * 2 - 1);
    };

    const onVisibility = () => {
        if (document.hidden) stop();
        else {
            lastTime = performance.now();
            loop();
        }
    };

    const onTheme = () => {
        colors = themeColors();
        themeFns.forEach((fn) => fn(colors));
        render();
    };

    const resizeObserver = new ResizeObserver(resize);
    const intersectionObserver = new IntersectionObserver(([entry]) => {
        visible = entry.isIntersecting;
        if (visible) {
            lastTime = performance.now();
            loop();
        } else stop();
    });

    if (!reduced) window.addEventListener('pointermove', onPointerMove, { passive: true });
    document.addEventListener('visibilitychange', onVisibility);
    document.addEventListener('themechange', onTheme);

    return {
        host,
        scene,
        camera,
        renderer,
        pointer,
        lowPower,
        reduced,
        get colors() {
            return colors;
        },
        get size() {
            return size;
        },
        onFrame: (fn) => frameFns.push(fn),
        onResize: (fn) => resizeFns.push(fn),
        onTheme: (fn) => themeFns.push(fn),
        render,
        /** Call once the scene is built. */
        start() {
            resize();
            resizeObserver.observe(host);
            intersectionObserver.observe(host);
            // In reduced-motion mode, draw a single representative frame.
            if (reduced) {
                frameFns.forEach((fn) => fn(4, 0));
                render();
            }
            requestAnimationFrame(() => host.classList.add('is-ready'));
        },
        dispose() {
            stop();
            resizeObserver.disconnect();
            intersectionObserver.disconnect();
            window.removeEventListener('pointermove', onPointerMove);
            document.removeEventListener('visibilitychange', onVisibility);
            document.removeEventListener('themechange', onTheme);
            renderer.dispose();
        },
    };
}

/** Soft round sprite for point clouds, drawn once on a small canvas. */
export function circleSprite(CanvasTexture) {
    const c = document.createElement('canvas');
    c.width = c.height = 64;
    const ctx = c.getContext('2d');
    const g = ctx.createRadialGradient(32, 32, 0, 32, 32, 32);
    g.addColorStop(0, 'rgba(255,255,255,1)');
    g.addColorStop(0.45, 'rgba(255,255,255,0.85)');
    g.addColorStop(1, 'rgba(255,255,255,0)');
    ctx.fillStyle = g;
    ctx.fillRect(0, 0, 64, 64);
    return new CanvasTexture(c);
}

/**
 * Text label rendered to a canvas texture, for use on a Sprite. Returns the
 * texture plus its world-space aspect so callers can size the sprite.
 * `redraw(color)` repaints it (used on theme change).
 */
export function textTexture(CanvasTexture, text, { color = '#ffffff', size = 64, weight = 600, pill = null } = {}) {
    const canvas = document.createElement('canvas');
    const ctx = canvas.getContext('2d');
    const font = `${weight} ${size}px "Space Grotesk", "Inter", sans-serif`;
    ctx.font = font;
    const padX = pill ? size * 0.7 : size * 0.2;
    canvas.width = Math.ceil(ctx.measureText(text).width + padX * 2);
    canvas.height = Math.ceil(size * (pill ? 1.9 : 1.4));
    const texture = new CanvasTexture(canvas);

    const redraw = (fill, pillFill = pill) => {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        if (pillFill) {
            const r = canvas.height / 2;
            ctx.fillStyle = pillFill;
            ctx.beginPath();
            ctx.roundRect(1, 1, canvas.width - 2, canvas.height - 2, r);
            ctx.fill();
        }
        ctx.font = font;
        ctx.fillStyle = fill;
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';
        ctx.fillText(text, canvas.width / 2, canvas.height / 2 + size * 0.04);
        texture.needsUpdate = true;
    };
    redraw(color);

    return { texture, aspect: canvas.width / canvas.height, redraw };
}

/** CSS colour string from a three.js Color. */
export const css = (color, alpha = 1) => `rgba(${Math.round(color.r * 255)}, ${Math.round(color.g * 255)}, ${Math.round(color.b * 255)}, ${alpha})`;

/** Progress (0 → 1) of `el` travelling through the viewport. */
export function viewportProgress(el) {
    const r = el.getBoundingClientRect();
    const vh = window.innerHeight;
    return Math.min(1, Math.max(0, (vh - r.top) / (vh + r.height)));
}
