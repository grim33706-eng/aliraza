// Home — "signal core": a faceted core inside a wireframe shell, orbit rings
// carrying data packets, and a drifting particle field.
import {
    AdditiveBlending,
    AmbientLight,
    BufferAttribute,
    BufferGeometry,
    CanvasTexture,
    Color,
    DirectionalLight,
    EdgesGeometry,
    Group,
    IcosahedronGeometry,
    LineBasicMaterial,
    LineSegments,
    Mesh,
    MeshBasicMaterial,
    MeshStandardMaterial,
    NormalBlending,
    PointLight,
    Points,
    PointsMaterial,
    SphereGeometry,
    TorusGeometry,
} from 'three';
import { circleSprite } from '../engine.js';

export default function build(stage) {
    const { scene, camera, pointer, lowPower } = stage;
    const sprite = circleSprite(CanvasTexture);
    const root = new Group();
    scene.add(root);

    // Core
    const core = new Mesh(
        new IcosahedronGeometry(1.55, 1),
        new MeshStandardMaterial({ flatShading: true, metalness: 0.35, roughness: 0.3 }),
    );
    root.add(core);

    // Wire shell + vertex dots
    const shellGeo = new IcosahedronGeometry(2.35, 1);
    const shell = new LineSegments(new EdgesGeometry(shellGeo), new LineBasicMaterial({ transparent: true, opacity: 0.55 }));
    const shellDots = new Points(shellGeo, new PointsMaterial({ size: 0.12, map: sprite, transparent: true, depthWrite: false }));
    root.add(shell, shellDots);

    // Orbit rings with packets
    const rings = [];
    const ringMat = new MeshBasicMaterial({ transparent: true, opacity: 0.22 });
    [
        { r: 3.2, rx: 1.15, ry: 0.2, speed: 0.55 },
        { r: 3.75, rx: 1.9, ry: -0.5, speed: -0.38 },
        { r: 4.3, rx: 1.4, ry: 0.9, speed: 0.26 },
    ].forEach((cfg) => {
        const pivot = new Group();
        pivot.rotation.set(cfg.rx, cfg.ry, 0);
        pivot.add(new Mesh(new TorusGeometry(cfg.r, 0.01, 6, lowPower ? 96 : 180), ringMat));
        const packet = new Mesh(new SphereGeometry(0.08, 12, 12), new MeshBasicMaterial());
        pivot.add(packet);
        root.add(pivot);
        rings.push({ ...cfg, pivot, packet, offset: Math.random() * Math.PI * 2 });
    });

    // Particle field
    const count = lowPower ? 450 : 1100;
    const positions = new Float32Array(count * 3);
    for (let i = 0; i < count; i++) {
        const r = 5 + Math.random() * 9;
        const theta = Math.random() * Math.PI * 2;
        const phi = Math.acos(2 * Math.random() - 1);
        positions.set([r * Math.sin(phi) * Math.cos(theta), r * Math.sin(phi) * Math.sin(theta) * 0.7, r * Math.cos(phi) - 3], i * 3);
    }
    const fieldGeo = new BufferGeometry();
    fieldGeo.setAttribute('position', new BufferAttribute(positions, 3));
    const field = new Points(fieldGeo, new PointsMaterial({ size: 0.06, map: sprite, transparent: true, depthWrite: false, blending: AdditiveBlending }));
    scene.add(field);

    // Lights
    const ambient = new AmbientLight(0xffffff, 0.45);
    const key = new DirectionalLight(0xffffff, 2.2);
    key.position.set(4, 5, 6);
    const rim = new PointLight(0xffffff, 40, 20);
    rim.position.set(-4, -2, 3);
    scene.add(ambient, key, rim);

    const applyColors = (c) => {
        // Light theme: a softer, airier core instead of a heavy indigo block.
        core.material.color.copy(c.iris).lerp(c.light ? new Color(0xffffff) : c.bg, c.light ? 0.35 : 0.15);
        core.material.emissive.copy(c.iris).multiplyScalar(c.light ? 0.12 : 0.18);
        core.material.metalness = c.light ? 0.1 : 0.35;
        ambient.intensity = c.light ? 1.1 : 0.45;
        shell.material.color.copy(c.vivid);
        shellDots.material.color.copy(c.vivid);
        ringMat.color.copy(c.light ? c.iris : c.text);
        rings.forEach((ring, i) => ring.packet.material.color.copy(i === 1 ? c.coral : c.vivid));
        field.material.color.copy(c.light ? c.iris : c.text);
        field.material.opacity = c.light ? 0.5 : 0.55;
        field.material.blending = c.light ? NormalBlending : AdditiveBlending;
        field.material.needsUpdate = true;
        key.color.copy(c.light ? new Color(0xffffff) : c.accent);
        rim.color.copy(c.iris);
    };
    applyColors(stage.colors);
    stage.onTheme(applyColors);

    // Layout: sit to the right of the copy on wide screens, behind it on small ones.
    stage.onResize(({ width }) => {
        const wide = width >= 992;
        const phone = width < 576;
        // On phones it tucks into the top corner so the headline stays readable.
        root.position.set(wide ? 3.2 : phone ? 1.5 : 2.2, wide ? 0.2 : phone ? 3.1 : 2.4, 0);
        root.scale.setScalar(wide ? 1 : phone ? 0.42 : 0.6);
        shell.material.opacity = wide ? 0.55 : 0.32;
    });

    stage.onFrame((t) => {
        const scroll = Math.min(window.scrollY / window.innerHeight, 1.5);
        root.rotation.y = t * 0.12 + pointer.sx * 0.35 + scroll * 0.8;
        root.rotation.x = pointer.sy * -0.2 + scroll * 0.3;
        core.rotation.set(t * -0.2, t * 0.25, 0);
        core.scale.setScalar(1 + Math.sin(t * 1.4) * 0.03);
        shell.rotation.y = shellDots.rotation.y = t * -0.06;
        rings.forEach((ring) => {
            const a = t * ring.speed + ring.offset;
            ring.packet.position.set(Math.cos(a) * ring.r, Math.sin(a) * ring.r, 0);
        });
        field.rotation.y = t * 0.015;
        camera.position.x = pointer.sx * 0.5;
        camera.position.y = pointer.sy * 0.35 - scroll * 0.6;
        camera.lookAt(0, -scroll * 0.6, 0);
    });
}
