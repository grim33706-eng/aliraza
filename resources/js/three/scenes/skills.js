// About — a rotating sphere of skill labels with a wireframe core.
// Labels come from the host's data-labels JSON. Drag/pointer steers it.
import {
    AdditiveBlending,
    CanvasTexture,
    EdgesGeometry,
    Group,
    IcosahedronGeometry,
    LineBasicMaterial,
    LineSegments,
    Sprite,
    SpriteMaterial,
    Vector3,
} from 'three';
import { css, textTexture } from '../engine.js';

export default function build(stage, host) {
    const { scene, camera, pointer } = stage;
    const labels = JSON.parse(host.dataset.labels || '[]');
    const root = new Group();
    scene.add(root);

    const core = new LineSegments(new EdgesGeometry(new IcosahedronGeometry(1.25, 1)), new LineBasicMaterial({ transparent: true, opacity: 0.35, blending: AdditiveBlending }));
    root.add(core);

    const radius = 2.7;
    const golden = Math.PI * (3 - Math.sqrt(5));
    const sprites = labels.map((label, i) => {
        const y = 1 - (i / Math.max(1, labels.length - 1)) * 2;
        const r = Math.sqrt(1 - y * y);
        const text = textTexture(CanvasTexture, label.text, { size: 44, weight: 600 });
        const sprite = new Sprite(new SpriteMaterial({ map: text.texture, transparent: true, depthWrite: false }));
        sprite.position.set(Math.cos(golden * i) * r * radius, y * radius, Math.sin(golden * i) * r * radius);
        sprite.scale.set(0.26 * text.aspect, 0.26, 1);
        sprite.userData = { text, group: label.group };
        root.add(sprite);
        return sprite;
    });

    const tones = (c) => [c.vivid, c.iris, c.light ? c.text : c.text];
    const applyColors = (c) => {
        core.material.color.copy(c.iris);
        core.material.blending = c.blend;
        core.material.opacity = c.light ? 0.5 : 0.35;
        core.material.needsUpdate = true;
        const palette = tones(c);
        sprites.forEach((s) => s.userData.text.redraw(css(palette[s.userData.group % palette.length])));
    };
    applyColors(stage.colors);
    stage.onTheme(applyColors);

    stage.onResize(({ width }) => {
        camera.position.z = width < 480 ? 12 : 10;
    });

    // Drag to spin; otherwise drift with the pointer.
    let spin = { x: 0, y: 0 };
    let velocity = 0.12;
    let dragging = null;
    host.addEventListener('pointerdown', (e) => {
        dragging = { x: e.clientX, y: e.clientY };
        host.setPointerCapture(e.pointerId);
    });
    host.addEventListener('pointermove', (e) => {
        if (!dragging) return;
        spin.y += (e.clientX - dragging.x) * 0.008;
        spin.x += (e.clientY - dragging.y) * 0.006;
        velocity = (e.clientX - dragging.x) * 0.02;
        dragging = { x: e.clientX, y: e.clientY };
    });
    const end = () => (dragging = null);
    host.addEventListener('pointerup', end);
    host.addEventListener('pointercancel', end);

    const world = new Vector3();
    stage.onFrame((t, dt) => {
        if (!dragging) {
            velocity += (0.12 - velocity) * Math.min(1, dt * 1.5);
            spin.y += velocity * dt;
        }
        root.rotation.y = spin.y + pointer.sx * 0.3;
        root.rotation.x = spin.x * 0.5 + pointer.sy * -0.2;
        core.rotation.y = -t * 0.2;
        // Depth cue: labels at the back fade and shrink slightly.
        sprites.forEach((s) => {
            s.getWorldPosition(world);
            const front = (world.z / radius + 1) / 2;
            s.material.opacity = 0.18 + front * 0.82;
        });
    });
}
