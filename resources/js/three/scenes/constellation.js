// About — a constellation of nodes; six brighter "milestone" stars are joined
// by a rising path, echoing the career journey.
import {
    AdditiveBlending,
    BufferAttribute,
    BufferGeometry,
    CanvasTexture,
    Group,
    Line,
    LineBasicMaterial,
    LineSegments,
    Points,
    PointsMaterial,
} from 'three';
import { circleSprite } from '../engine.js';

export default function build(stage) {
    const { scene, camera, pointer, lowPower } = stage;
    const sprite = circleSprite(CanvasTexture);
    const root = new Group();
    scene.add(root);

    const count = lowPower ? 55 : 110;
    const nodes = [];
    for (let i = 0; i < count; i++) {
        nodes.push([(Math.random() - 0.5) * 22, (Math.random() - 0.5) * 11, (Math.random() - 0.5) * 8]);
    }

    // Connections between nearby nodes (computed once).
    const linePositions = [];
    const maxDist = lowPower ? 3.4 : 3;
    for (let i = 0; i < count; i++) {
        for (let j = i + 1; j < count; j++) {
            const [a, b] = [nodes[i], nodes[j]];
            if (Math.hypot(a[0] - b[0], a[1] - b[1], a[2] - b[2]) < maxDist) linePositions.push(...a, ...b);
        }
    }
    const nodeGeo = new BufferGeometry();
    nodeGeo.setAttribute('position', new BufferAttribute(new Float32Array(nodes.flat()), 3));
    const nodePoints = new Points(nodeGeo, new PointsMaterial({ size: 0.16, map: sprite, transparent: true, depthWrite: false }));
    const linkGeo = new BufferGeometry();
    linkGeo.setAttribute('position', new BufferAttribute(new Float32Array(linePositions), 3));
    const links = new LineSegments(linkGeo, new LineBasicMaterial({ transparent: true, opacity: 0.14 }));
    root.add(nodePoints, links);

    // Milestone path: six stars rising from left to right.
    const milestones = Array.from({ length: 6 }, (_, i) => [-8 + i * 3.4, -3 + i * 1.25 + (i % 2 ? 0.6 : -0.4), 1.5]);
    const pathGeo = new BufferGeometry();
    pathGeo.setAttribute('position', new BufferAttribute(new Float32Array(milestones.flat()), 3));
    const path = new Line(pathGeo, new LineBasicMaterial({ transparent: true, opacity: 0.85 }));
    const stars = new Points(pathGeo, new PointsMaterial({ size: 0.55, map: sprite, transparent: true, depthWrite: false, blending: AdditiveBlending }));
    root.add(path, stars);
    pathGeo.setDrawRange(0, 0);

    const applyColors = (c) => {
        nodePoints.material.color.copy(c.light ? c.iris : c.text);
        links.material.color.copy(c.light ? c.iris : c.text);
        links.material.opacity = c.light ? 0.2 : 0.14;
        path.material.color.copy(c.vivid);
        stars.material.color.copy(c.vivid);
        stars.material.blending = c.blend;
        stars.material.needsUpdate = true;
    };
    applyColors(stage.colors);
    stage.onTheme(applyColors);

    stage.onResize(({ width }) => {
        camera.position.z = width < 768 ? 16 : 12;
    });

    stage.onFrame((t) => {
        // Path draws itself in over the first ~2.5s.
        pathGeo.setDrawRange(0, Math.min(6, Math.floor(t * 2.4) + 1));
        root.rotation.y = Math.sin(t * 0.08) * 0.25 + pointer.sx * 0.25;
        root.rotation.x = pointer.sy * -0.12;
        stars.material.size = 0.5 + Math.sin(t * 2) * 0.08;
        root.position.y = -window.scrollY * 0.002;
    });
}
