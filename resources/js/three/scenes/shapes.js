// Services — nine floating low-poly forms, one per service.
import {
    AmbientLight,
    ConeGeometry,
    CylinderGeometry,
    DirectionalLight,
    DodecahedronGeometry,
    Group,
    IcosahedronGeometry,
    Mesh,
    MeshStandardMaterial,
    OctahedronGeometry,
    BoxGeometry,
    TetrahedronGeometry,
    TorusGeometry,
    TorusKnotGeometry,
} from 'three';

export default function build(stage) {
    const { scene, camera, pointer, lowPower } = stage;
    const root = new Group();
    scene.add(root);

    const geometries = [
        new OctahedronGeometry(0.9),
        new TorusGeometry(0.7, 0.26, 10, 28),
        new IcosahedronGeometry(0.85, 0),
        new BoxGeometry(1.1, 1.1, 1.1),
        new ConeGeometry(0.75, 1.3, 6),
        new DodecahedronGeometry(0.8),
        new TorusKnotGeometry(0.55, 0.18, 64, 8),
        new TetrahedronGeometry(0.95),
        new CylinderGeometry(0.55, 0.55, 1.1, 8),
    ];
    // Positions are biased to the right so the headline stays readable.
    const layout = [
        [5.2, 2.2, -1], [8.2, -0.6, -3], [3.6, -2.4, 0.5], [6.8, 3.6, -4], [2.4, 3.2, -3],
        [9.6, 2.6, -5], [6.2, -3.4, -2], [-6.5, -3.4, -5], [-8.5, 3.2, -6],
    ];
    const tones = ['accent', 'iris', 'coral', 'iris', 'accent', 'text', 'accent', 'iris', 'coral'];

    const shapes = geometries.slice(0, lowPower ? 6 : 9).map((geometry, i) => {
        const mesh = new Mesh(geometry, new MeshStandardMaterial({ flatShading: true, metalness: 0.25, roughness: 0.4 }));
        mesh.position.set(...layout[i]);
        mesh.rotation.set(Math.random() * 3, Math.random() * 3, 0);
        mesh.userData = { base: mesh.position.clone(), speed: 0.3 + Math.random() * 0.4, phase: Math.random() * 6, depth: 1 - layout[i][2] / 8, tone: tones[i] };
        root.add(mesh);
        return mesh;
    });

    const ambient = new AmbientLight(0xffffff, 0.6);
    const key = new DirectionalLight(0xffffff, 2.4);
    key.position.set(5, 6, 8);
    const fill = new DirectionalLight(0xffffff, 0.9);
    fill.position.set(-6, -3, 4);
    scene.add(ambient, key, fill);

    const applyColors = (c) => {
        shapes.forEach((mesh) => {
            const tone = mesh.userData.tone === 'accent' ? c.vivid : mesh.userData.tone === 'text' && c.light ? c.muted : c[mesh.userData.tone];
            mesh.material.color.copy(tone);
            mesh.material.emissive.copy(tone).multiplyScalar(c.light ? 0.04 : 0.12);
        });
        fill.color.copy(c.iris);
    };
    applyColors(stage.colors);
    stage.onTheme(applyColors);

    stage.onResize(({ width }) => {
        const narrow = width < 992;
        root.position.set(narrow ? -3.5 : 0, narrow ? 1.5 : 0, narrow ? -4 : 0);
        camera.position.z = 12;
    });

    stage.onFrame((t) => {
        shapes.forEach((mesh) => {
            const { base, speed, phase, depth } = mesh.userData;
            mesh.position.y = base.y + Math.sin(t * speed + phase) * 0.35;
            mesh.position.x = base.x + pointer.sx * 0.6 * depth;
            mesh.rotation.x += 0.003 + speed * 0.004;
            mesh.rotation.y += 0.004;
        });
        root.rotation.y = pointer.sx * 0.08;
        root.rotation.x = pointer.sy * -0.06;
        root.position.y = window.scrollY * 0.004;
    });
}
