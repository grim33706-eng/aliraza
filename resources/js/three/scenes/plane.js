// Contact — a low-poly paper plane gliding beside the contact details.
// When a message is sent (`contact:sent` on document) it loops away and returns.
import {
    AdditiveBlending,
    AmbientLight,
    BufferAttribute,
    BufferGeometry,
    CanvasTexture,
    Color,
    DirectionalLight,
    DoubleSide,
    EdgesGeometry,
    Group,
    LineBasicMaterial,
    LineSegments,
    Mesh,
    MeshStandardMaterial,
    Points,
    PointsMaterial,
} from 'three';
import { circleSprite } from '../engine.js';

const TRAIL = 40;

function planeGeometry() {
    // nose, left tip, right tip, tail centre, keel
    const nose = [0, 0, 1.4];
    const left = [-1.05, 0.08, -0.7];
    const right = [1.05, 0.08, -0.7];
    const tail = [0, 0, -0.7];
    const keel = [0, -0.42, -0.7];
    const tris = [nose, left, tail, nose, tail, right, nose, keel, tail, nose, tail, keel];
    const geometry = new BufferGeometry();
    geometry.setAttribute('position', new BufferAttribute(new Float32Array(tris.flat()), 3));
    geometry.computeVertexNormals();
    return geometry;
}

export default function build(stage) {
    const { scene, camera, pointer } = stage;
    const sprite = circleSprite(CanvasTexture);

    const plane = new Group();
    const geometry = planeGeometry();
    const body = new Mesh(geometry, new MeshStandardMaterial({ flatShading: true, side: DoubleSide, metalness: 0.1, roughness: 0.5 }));
    const edges = new LineSegments(new EdgesGeometry(geometry), new LineBasicMaterial());
    plane.add(body, edges);
    plane.scale.setScalar(1.25);
    scene.add(plane);

    const trailGeo = new BufferGeometry();
    const trailPos = new Float32Array(TRAIL * 3);
    trailGeo.setAttribute('position', new BufferAttribute(trailPos, 3));
    const trail = new Points(trailGeo, new PointsMaterial({ size: 0.12, map: sprite, transparent: true, depthWrite: false, blending: AdditiveBlending }));
    scene.add(trail);
    const history = [];

    scene.add(new AmbientLight(0xffffff, 0.7));
    const key = new DirectionalLight(0xffffff, 2.2);
    key.position.set(3, 5, 4);
    const fill = new DirectionalLight(0xffffff, 1);
    fill.position.set(-4, -2, 2);
    scene.add(key, fill);

    const applyColors = (c) => {
        // Light theme: a white plane with violet shading and a solid (non-glowing) trail.
        body.material.color.copy(c.light ? new Color(0xffffff) : c.text).lerp(c.iris, c.light ? 0.28 : 0.12);
        edges.material.color.copy(c.vivid);
        trail.material.color.copy(c.light ? c.iris : c.accent);
        trail.material.blending = c.blend;
        trail.material.needsUpdate = true;
        fill.color.copy(c.iris);
    };
    applyColors(stage.colors);
    stage.onTheme(applyColors);

    camera.position.set(0, 0.6, 8);
    camera.lookAt(0, 0, 0);

    // A send launches a 2.4s loop-the-loop away and back.
    let launchAt = -1;
    let clock = 0;
    document.addEventListener('contact:sent', () => {
        launchAt = clock;
    });

    stage.onFrame((t) => {
        clock = t;
        // Lazy figure-of-eight glide.
        let x = Math.sin(t * 0.6) * 2.2;
        let y = Math.sin(t * 1.2) * 0.5 + 0.2;
        let z = Math.cos(t * 0.6) * 0.8;
        let bank = -Math.cos(t * 0.6) * 0.6;

        const since = launchAt >= 0 ? t - launchAt : -1;
        if (since >= 0 && since < 2.4) {
            const p = since / 2.4;
            x += p * 9;
            y += Math.sin(p * Math.PI) * 3;
            z -= Math.sin(p * Math.PI) * 4;
            bank -= p * Math.PI * 2;
        }

        plane.position.set(x + pointer.sx * 0.4, y + pointer.sy * 0.3, z);
        const ahead = { x: Math.cos(t * 0.6) * 1.32, z: -Math.sin(t * 0.6) * 0.48 };
        plane.rotation.set(Math.cos(t * 1.2) * -0.25, Math.atan2(ahead.x, ahead.z), bank);

        history.unshift(plane.position.clone());
        history.length = Math.min(history.length, TRAIL);
        history.forEach((p, i) => trailPos.set([p.x, p.y, p.z], i * 3));
        trailGeo.setDrawRange(0, history.length);
        trailGeo.attributes.position.needsUpdate = true;
    });
}
