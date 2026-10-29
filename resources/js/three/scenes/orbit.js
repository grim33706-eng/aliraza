// Services toolkit — platform and tool names orbiting a faceted core on
// three tilted rings.
import {
    AmbientLight,
    CanvasTexture,
    DirectionalLight,
    Group,
    IcosahedronGeometry,
    Mesh,
    MeshBasicMaterial,
    MeshStandardMaterial,
    PointLight,
    Sprite,
    SpriteMaterial,
    TorusGeometry,
    Vector3,
} from 'three';
import { css, textTexture } from '../engine.js';

export default function build(stage, host) {
    const { scene, camera, pointer, lowPower } = stage;
    const tools = JSON.parse(host.dataset.labels || '[]');
    const root = new Group();
    root.rotation.x = 0.35;
    scene.add(root);

    const core = new Mesh(new IcosahedronGeometry(1.1, 1), new MeshStandardMaterial({ flatShading: true, metalness: 0.4, roughness: 0.25 }));
    root.add(core);
    scene.add(new AmbientLight(0xffffff, 0.5));
    const key = new DirectionalLight(0xffffff, 2.2);
    key.position.set(3, 4, 5);
    const glow = new PointLight(0xffffff, 30, 12);
    glow.position.set(-3, -2, 2);
    scene.add(key, glow);

    const ringDefs = [
        { r: 2.4, tilt: [0.15, 0, 0.2], speed: 0.22 },
        { r: 3.4, tilt: [-0.25, 0, -0.35], speed: -0.15 },
        { r: 4.4, tilt: [0.1, 0, 0.5], speed: 0.1 },
    ];
    const ringMat = new MeshBasicMaterial({ transparent: true, opacity: 0.25 });
    const rings = ringDefs.map((def) => {
        const pivot = new Group();
        pivot.rotation.set(...def.tilt);
        const ring = new Mesh(new TorusGeometry(def.r, 0.008, 6, lowPower ? 100 : 200), ringMat);
        ring.rotation.x = Math.PI / 2;
        pivot.add(ring);
        const spinner = new Group();
        pivot.add(spinner);
        root.add(pivot);
        return { ...def, pivot, spinner };
    });

    const sprites = tools.map((tool, i) => {
        const ring = rings[i % rings.length];
        const onRing = tools.filter((_, j) => j % rings.length === i % rings.length).length;
        const slot = Math.floor(i / rings.length);
        const angle = (slot / onRing) * Math.PI * 2 + (i % rings.length);
        const text = textTexture(CanvasTexture, tool, { size: 40, weight: 600, pill: 'rgba(0,0,0,0.5)' });
        const sprite = new Sprite(new SpriteMaterial({ map: text.texture, transparent: true, depthWrite: false }));
        sprite.position.set(Math.cos(angle) * ring.r, 0, Math.sin(angle) * ring.r);
        sprite.scale.set(0.36 * text.aspect, 0.36, 1);
        sprite.userData.text = text;
        ring.spinner.add(sprite);
        return sprite;
    });

    const applyColors = (c) => {
        core.material.color.copy(c.vivid);
        core.material.emissive.copy(c.iris).multiplyScalar(c.light ? 0.05 : 0.25);
        ringMat.color.copy(c.light ? c.iris : c.text);
        glow.color.copy(c.iris);
        sprites.forEach((s) => s.userData.text.redraw(c.light ? '#0e101a' : '#eef0f7', c.light ? 'rgba(255,255,255,0.9)' : css(c.bg, 0.78)));
    };
    applyColors(stage.colors);
    stage.onTheme(applyColors);

    stage.onResize(({ width }) => {
        camera.position.z = width < 576 ? 14 : 11.5;
    });

    const world = new Vector3();
    stage.onFrame((t) => {
        rings.forEach((ring) => (ring.spinner.rotation.y = t * ring.speed));
        core.rotation.set(t * 0.3, t * 0.4, 0);
        root.rotation.y = pointer.sx * 0.4;
        root.rotation.x = 0.35 + pointer.sy * -0.15;
        sprites.forEach((s) => {
            s.getWorldPosition(world);
            s.material.opacity = 0.35 + Math.max(0, Math.min(1, (world.z + 4) / 8)) * 0.65;
        });
    });
}
