// International hero — a tilted, dotted world map (real coastlines) with
// light arcs rising from Lahore to each market.
import {
    AdditiveBlending,
    BufferAttribute,
    BufferGeometry,
    CanvasTexture,
    CylinderGeometry,
    DoubleSide,
    Group,
    Line,
    LineBasicMaterial,
    Mesh,
    MeshBasicMaterial,
    NormalBlending,
    Points,
    PointsMaterial,
    QuadraticBezierCurve3,
    RingGeometry,
    ShaderMaterial,
    Vector2,
    Vector3,
} from 'three';
import land from '../data/land-map.json';
import { circleSprite } from '../engine.js';

const SCALE = 0.05;
const CENTRE = { lat: 35, lng: 45 }; // middle of the markets
const TRAIL = 16;

const project = (lat, lng, y = 0) => new Vector3((lng - CENTRE.lng) * SCALE, y, -(lat - CENTRE.lat) * SCALE);

export default function build(stage, host) {
    const { scene, camera, pointer, lowPower } = stage;
    const sprite = circleSprite(CanvasTexture);
    const { home, markets } = JSON.parse(host.dataset.markets);
    const root = new Group();
    scene.add(root);

    // Land dots with a soft vignette around the markets (GPU shader).
    const step = lowPower ? 2 : 1;
    const positions = [];
    for (let i = 0; i < land.length; i += 2 * step) positions.push(...project(land[i], land[i + 1]).toArray());
    const geometry = new BufferGeometry();
    geometry.setAttribute('position', new BufferAttribute(new Float32Array(positions), 3));
    const dotMaterial = new ShaderMaterial({
        transparent: true,
        depthWrite: false,
        uniforms: {
            uTime: { value: 0 },
            uColor: { value: stage.colors.text.clone() },
            uHot: { value: stage.colors.accent.clone() },
            uHome: { value: new Vector2(project(home.lat, home.lng).x, project(home.lat, home.lng).z) },
            uSize: { value: (lowPower ? 3.2 : 2.3) * stage.renderer.getPixelRatio() },
            uOpacity: { value: 0.75 },
        },
        vertexShader: /* glsl */ `
            uniform float uTime; uniform float uSize; uniform vec2 uHome;
            varying float vFade; varying float vHot;
            void main() {
                vec3 p = position;
                float d = length(p.xz);
                float h = distance(p.xz, uHome);
                // A slow pulse ripples outward from Lahore across the map.
                float wave = sin(h * 2.2 - uTime * 1.8);
                p.y += smoothstep(0.6, 1.0, wave) * 0.06;
                vHot = smoothstep(0.75, 1.0, wave) * (1.0 - smoothstep(1.5, 7.0, h));
                vFade = 1.0 - smoothstep(3.5, 9.5, d);
                vec4 mv = modelViewMatrix * vec4(p, 1.0);
                gl_PointSize = uSize * (10.0 / -mv.z);
                gl_Position = projectionMatrix * mv;
            }`,
        fragmentShader: /* glsl */ `
            uniform vec3 uColor; uniform vec3 uHot; uniform float uOpacity;
            varying float vFade; varying float vHot;
            void main() {
                float r = length(gl_PointCoord - 0.5);
                if (r > 0.5) discard;
                vec3 c = mix(uColor, uHot, vHot);
                gl_FragColor = vec4(c, (1.0 - r * 2.0) * vFade * uOpacity * (0.55 + vHot * 0.45));
            }`,
    });
    root.add(new Points(geometry, dotMaterial));

    // Home marker + markets with beams, pulses and arcs.
    const origin = project(home.lat, home.lng);
    const homeMat = new MeshBasicMaterial();
    const homePulse = new Mesh(new RingGeometry(0.12, 0.16, 48), new MeshBasicMaterial({ transparent: true, side: DoubleSide, depthWrite: false }));
    homePulse.rotation.x = -Math.PI / 2;
    homePulse.position.copy(origin);
    const homeBeam = new Mesh(new CylinderGeometry(0.012, 0.035, 0.9, 12, 1, true), homeMat);
    homeBeam.position.copy(origin).add(new Vector3(0, 0.45, 0));
    root.add(homePulse, homeBeam);

    const accentMat = new MeshBasicMaterial({ transparent: true, opacity: 0.85, blending: AdditiveBlending, depthWrite: false });
    const arcMat = new LineBasicMaterial({ transparent: true, opacity: 0.55 });
    const trailMat = new PointsMaterial({ size: 0.16, map: sprite, transparent: true, depthWrite: false, blending: AdditiveBlending });
    const pulseMat = new MeshBasicMaterial({ transparent: true, side: DoubleSide, depthWrite: false });

    const routes = markets
        .filter((m) => Math.abs(m.lat - home.lat) + Math.abs(m.lng - home.lng) > 0.5)
        .map((market) => {
            const end = project(market.lat, market.lng);
            const beam = new Mesh(new CylinderGeometry(0.012, 0.035, 0.7, 10, 1, true), accentMat);
            beam.position.copy(end).add(new Vector3(0, 0.35, 0));
            const pulse = new Mesh(new RingGeometry(0.08, 0.11, 40), pulseMat.clone());
            pulse.rotation.x = -Math.PI / 2;
            pulse.position.copy(end);

            const mid = origin.clone().add(end).multiplyScalar(0.5);
            mid.y = origin.distanceTo(end) * 0.38;
            const curve = new QuadraticBezierCurve3(origin, mid, end);
            const arcGeo = new BufferGeometry().setFromPoints(curve.getPoints(90));
            arcGeo.setDrawRange(0, 0);
            const arc = new Line(arcGeo, arcMat);
            const trailGeo = new BufferGeometry();
            trailGeo.setAttribute('position', new BufferAttribute(new Float32Array(TRAIL * 3), 3));
            const trail = new Points(trailGeo, trailMat);
            root.add(beam, pulse, arc, trail);
            return { beam, pulse, arc, trail, curve, phase: Math.random() };
        });

    const applyColors = (c) => {
        const accent = c.vivid;
        dotMaterial.uniforms.uColor.value.copy(c.light ? c.iris : c.text);
        dotMaterial.uniforms.uHot.value.copy(accent);
        dotMaterial.uniforms.uOpacity.value = c.light ? 0.9 : 0.75;
        dotMaterial.blending = c.light ? NormalBlending : AdditiveBlending;
        dotMaterial.needsUpdate = true;
        accentMat.blending = c.blend;
        accentMat.needsUpdate = true;
        trailMat.blending = c.blend;
        trailMat.needsUpdate = true;
        homeMat.color.copy(c.coral);
        homePulse.material.color.copy(c.coral);
        accentMat.color.copy(accent);
        arcMat.color.copy(accent);
        trailMat.color.copy(c.light ? c.iris : accent);
        routes.forEach((r) => r.pulse.material.color.copy(accent));
    };
    applyColors(stage.colors);
    stage.onTheme(applyColors);

    stage.onResize(({ width }) => {
        const wide = width >= 992;
        // Wide screens: map sits right of the copy. Narrow: behind it, pushed back.
        root.position.set(wide ? 3.3 : 0.4, wide ? -0.2 : 0.8, 0);
        root.userData.z = wide ? 0 : -2;
    });

    camera.position.set(0, 7, 6.2);
    camera.lookAt(0, 0, 0.4);

    const tmp = new Vector3();
    stage.onFrame((t) => {
        dotMaterial.uniforms.uTime.value = t;
        const draw = Math.min(1, Math.max(0, (t - 0.4) / 2));
        const pulse = (t * 0.6) % 1;
        homePulse.scale.setScalar(1 + pulse * 3);
        homePulse.material.opacity = 1 - pulse;
        routes.forEach((r) => {
            r.arc.geometry.setDrawRange(0, Math.floor(91 * draw));
            const p = (t * 0.5 + r.phase) % 1;
            r.pulse.scale.setScalar(1 + p * 2);
            r.pulse.material.opacity = (1 - p) * 0.9;
            r.beam.scale.y = Math.max(0.001, draw);
            const head = (t * 0.28 + r.phase) % 1;
            const arr = r.trail.geometry.attributes.position.array;
            for (let k = 0; k < TRAIL; k++) {
                r.curve.getPoint(Math.max(0, head - k * 0.01), tmp);
                arr.set([tmp.x, tmp.y, tmp.z], k * 3);
            }
            r.trail.geometry.attributes.position.needsUpdate = true;
            r.trail.visible = draw >= 1;
        });
        root.rotation.y = Math.sin(t * 0.12) * 0.12 + pointer.sx * 0.18;
        root.rotation.x = pointer.sy * 0.06;
        root.position.z = (root.userData.z || 0) + window.scrollY * 0.004;
    });
}
