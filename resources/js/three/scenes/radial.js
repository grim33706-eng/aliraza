// Results — CTR per campaign (Source B) as a 3D radial bar chart. Bar heights
// are linear in the real values. Dispatch `radial:focus` (detail = index, or
// -1 to clear) on the host to highlight one bar.
import {
    AmbientLight,
    BoxGeometry,
    CanvasTexture,
    CircleGeometry,
    DoubleSide,
    Color,
    DirectionalLight,
    Group,
    Mesh,
    MeshBasicMaterial,
    MeshStandardMaterial,
    RingGeometry,
    Sprite,
    SpriteMaterial,
} from 'three';
import { css, textTexture } from '../engine.js';

export default function build(stage, host) {
    const { scene, camera, pointer } = stage;
    const data = JSON.parse(host.dataset.values || '[]');
    const max = Math.max(...data.map((d) => d.value));
    const root = new Group();
    scene.add(root);

    // Platform: disc + guide rings.
    const disc = new Mesh(new CircleGeometry(3.6, 64), new MeshBasicMaterial({ transparent: true, opacity: 0.35 }));
    disc.rotation.x = -Math.PI / 2;
    const guideMat = new MeshBasicMaterial({ transparent: true, opacity: 0.35, side: DoubleSide });
    const guides = [1.2, 2.4, 3.6].map((r) => {
        const ring = new Mesh(new RingGeometry(r - 0.01, r + 0.01, 96), guideMat);
        ring.rotation.x = -Math.PI / 2;
        ring.position.y = 0.01;
        return ring;
    });
    root.add(disc, ...guides);

    scene.add(new AmbientLight(0xffffff, 0.6));
    const key = new DirectionalLight(0xffffff, 2.4);
    key.position.set(4, 8, 6);
    scene.add(key);

    const geometry = new BoxGeometry(0.5, 1, 0.5);
    geometry.translate(0, 0.5, 0);
    const bars = data.map((d, i) => {
        const angle = (i / data.length) * Math.PI * 2;
        const mesh = new Mesh(geometry, new MeshStandardMaterial({ metalness: 0.2, roughness: 0.35 }));
        mesh.position.set(Math.cos(angle) * 2.4, 0, Math.sin(angle) * 2.4);
        mesh.rotation.y = -angle;
        const height = Math.max(0.08, (d.value / max) * 4.2);
        const text = textTexture(CanvasTexture, `${d.value.toFixed(2)}%`, { size: 40, weight: 600 });
        const label = new Sprite(new SpriteMaterial({ map: text.texture, transparent: true, depthWrite: false }));
        label.scale.set(0.3 * text.aspect, 0.3, 1);
        label.userData.text = text;
        root.add(mesh, label);
        return { mesh, label, height, value: d.value, focus: 0 };
    });

    let focused = -1;
    host.addEventListener('radial:focus', (e) => {
        focused = e.detail;
        if (stage.reduced) {
            update(4, 1);
            stage.render();
        }
    });

    let palette = stage.colors;
    const tmp = new Color();
    const applyColors = (c) => {
        palette = c;
        disc.material.color.copy(c.iris);
        disc.material.opacity = c.light ? 0.08 : 0.1;
        guideMat.color.copy(c.light ? c.iris : c.text);
        guideMat.opacity = c.light ? 0.3 : 0.18;
        bars.forEach((b) => b.label.userData.text.redraw(css(c.text)));
    };
    applyColors(stage.colors);
    stage.onTheme(applyColors);

    const update = (t, dt) => {
        const grow = 1 - Math.pow(1 - Math.min(1, t / 1.8), 3);
        bars.forEach((b, i) => {
            b.focus += ((focused === i ? 1 : 0) - b.focus) * Math.min(1, dt * 8);
            const h = b.height * grow;
            b.mesh.scale.set(1 + b.focus * 0.35, h, 1 + b.focus * 0.35);
            b.label.position.set(b.mesh.position.x, h + 0.35, b.mesh.position.z);
            b.label.material.opacity = grow * (focused === -1 ? 0.9 : 0.25 + b.focus * 0.75);
            tmp.copy(palette.solid).lerp(palette.vivid, Math.min(1, b.value / max + 0.25));
            if (focused !== -1 && focused !== i) tmp.lerp(palette.bg, 0.55);
            b.mesh.material.color.copy(tmp);
            b.mesh.material.emissive.copy(tmp).multiplyScalar(0.15 + b.focus * 0.35);
        });
    };

    stage.onResize(({ width }) => {
        camera.position.set(0, 4.4, width < 480 ? 11 : 9.2);
        camera.lookAt(0, 1.3, 0);
    });

    stage.onFrame((t, dt) => {
        update(t, dt || 1);
        root.rotation.y = t * 0.12 + pointer.sx * 0.3;
        root.rotation.x = pointer.sy * -0.06;
    });
}
