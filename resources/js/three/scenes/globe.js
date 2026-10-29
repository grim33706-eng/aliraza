// International (and the home preview) — a dotted Earth (real coastlines),
// light beams on each market and comet arcs from Lahore.
// Focus a market (or several) by dispatching `globe:focus` on the host with
// `detail` = index or array of indices. Drag to rotate when [data-interactive].
import {
    AdditiveBlending,
    BackSide,
    BufferAttribute,
    BufferGeometry,
    CanvasTexture,
    CylinderGeometry,
    DoubleSide,
    FrontSide,
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
    SphereGeometry,
    Sprite,
    SpriteMaterial,
    Vector3,
} from 'three';
import land from '../data/land-globe.json';
import { circleSprite, textTexture } from '../engine.js';

const RADIUS = 2;
const D2R = Math.PI / 180;
const TRAIL = 14;

function latLngToVector(lat, lng, r = RADIUS) {
    const phi = (90 - lat) * D2R;
    const theta = (lng + 180) * D2R;
    return new Vector3(-r * Math.sin(phi) * Math.cos(theta), r * Math.cos(phi), r * Math.sin(phi) * Math.sin(theta));
}

/** Rotation (x, y) that brings a lat/lng to face the camera. */
function rotationFor(lat, lng) {
    const v = latLngToVector(lat, lng);
    return { x: lat * D2R * 0.75, y: -Math.atan2(v.x, v.z) };
}

const glowMaterial = (color, side, fragment) =>
    new ShaderMaterial({
        transparent: true,
        side,
        blending: AdditiveBlending,
        depthWrite: false,
        uniforms: { uColor: { value: color.clone() }, uStrength: { value: 1 } },
        vertexShader: `varying vec3 vN; varying vec3 vV; void main(){ vN = normalize(normalMatrix * normal); vec4 mv = modelViewMatrix * vec4(position,1.0); vV = normalize(-mv.xyz); gl_Position = projectionMatrix * mv; }`,
        fragmentShader: `uniform vec3 uColor; uniform float uStrength; varying vec3 vN; varying vec3 vV; void main(){ ${fragment} }`,
    });

export default function build(stage, host) {
    const { scene, camera, lowPower, reduced } = stage;
    const sprite = circleSprite(CanvasTexture);
    const raw = JSON.parse(host.dataset.markets || '[]');
    const markets = Array.isArray(raw) ? raw : raw.markets;
    const home = (!Array.isArray(raw) && raw.home) || { lat: 31.55, lng: 74.34 };
    const interactive = host.hasAttribute('data-interactive');
    const labelled = host.hasAttribute('data-labels');

    const globe = new Group();
    scene.add(globe);

    // Opaque core: hides dots on the far side (dots don't write depth).
    const core = new Mesh(new SphereGeometry(RADIUS * 0.985, 64, 64), new MeshBasicMaterial());
    globe.add(core);

    // Land dots (real coastlines) + a sparse ocean grid.
    const step = lowPower ? 2 : 1;
    const landPos = [];
    for (let i = 0; i < land.length; i += 2 * step) landPos.push(...latLngToVector(land[i] / 10, land[i + 1] / 10, RADIUS * 1.003).toArray());
    const landGeo = new BufferGeometry();
    landGeo.setAttribute('position', new BufferAttribute(new Float32Array(landPos), 3));
    const landDots = new Points(landGeo, new PointsMaterial({ size: lowPower ? 0.06 : 0.052, map: sprite, transparent: true, depthWrite: false }));
    globe.add(landDots);

    const oceanCount = lowPower ? 500 : 900;
    const ocean = new Float32Array(oceanCount * 3);
    const golden = Math.PI * (3 - Math.sqrt(5));
    for (let i = 0; i < oceanCount; i++) {
        const y = 1 - (i / (oceanCount - 1)) * 2;
        const r = Math.sqrt(1 - y * y);
        ocean.set([Math.cos(golden * i) * r * RADIUS, y * RADIUS, Math.sin(golden * i) * r * RADIUS], i * 3);
    }
    const oceanGeo = new BufferGeometry();
    oceanGeo.setAttribute('position', new BufferAttribute(ocean, 3));
    const oceanDots = new Points(oceanGeo, new PointsMaterial({ size: 0.022, map: sprite, transparent: true, depthWrite: false }));
    globe.add(oceanDots);

    // Outer atmosphere (back faces: strongest hugging the globe, fading out at the shell edge) + rim light.
    const atmosphere = new Mesh(
        new SphereGeometry(RADIUS * 1.16, 64, 64),
        glowMaterial(stage.colors.iris, BackSide, 'float f = pow(clamp(-dot(vN, vec3(0.0, 0.0, 1.0)) * 1.9, 0.0, 1.0), 2.2); gl_FragColor = vec4(uColor, f * uStrength);'),
    );
    const rim = new Mesh(
        new SphereGeometry(RADIUS * 1.006, 64, 64),
        glowMaterial(stage.colors.iris, FrontSide, 'float f = pow(1.0 - abs(dot(vN, vV)), 3.0); gl_FragColor = vec4(uColor, f * uStrength * 0.6);'),
    );
    scene.add(atmosphere);
    globe.add(rim);

    // Markers: home (coral) and markets (accent) with beams, pulses and arcs.
    const origin = latLngToVector(home.lat, home.lng);
    const homeMarker = new Mesh(new SphereGeometry(0.06, 16, 16), new MeshBasicMaterial());
    homeMarker.position.copy(origin);
    globe.add(homeMarker);

    const markerMat = new MeshBasicMaterial();
    const beamGeo = new CylinderGeometry(0.008, 0.022, 1, 8, 1, true);
    beamGeo.translate(0, 0.5, 0);
    const arcMat = new LineBasicMaterial({ transparent: true, opacity: 0.5 });
    const trailMat = new PointsMaterial({ size: 0.09, map: sprite, transparent: true, depthWrite: false, blending: AdditiveBlending });
    const up = new Vector3(0, 1, 0);

    const items = markets.map((market) => {
        const position = latLngToVector(market.lat, market.lng);
        const normal = position.clone().normalize();
        const marker = new Mesh(new SphereGeometry(0.04, 12, 12), markerMat);
        marker.position.copy(position);

        const beam = new Mesh(beamGeo, new MeshBasicMaterial({ transparent: true, opacity: 0.75, blending: AdditiveBlending, depthWrite: false }));
        beam.position.copy(position);
        beam.quaternion.setFromUnitVectors(up, normal);
        beam.scale.set(1, 0.001, 1);

        const pulse = new Mesh(new RingGeometry(0.05, 0.07, 32), new MeshBasicMaterial({ transparent: true, side: DoubleSide, depthWrite: false }));
        pulse.position.copy(position.clone().multiplyScalar(1.002));
        pulse.lookAt(position.clone().multiplyScalar(2));
        globe.add(marker, beam, pulse);

        let label = null;
        if (labelled) {
            const text = textTexture(CanvasTexture, market.name, { size: 40, weight: 600, pill: 'rgba(8,9,15,0.72)' });
            label = new Sprite(new SpriteMaterial({ map: text.texture, transparent: true, depthWrite: false }));
            label.scale.set(0.2 * text.aspect, 0.2, 1);
            // Stagger label heights so neighbours (London/Paris, the three Gulf markets) don't collide.
            const lift = [0.18, -0.18, 0.42][markets.indexOf(market) % 3];
            label.position.copy(normal.clone().multiplyScalar(RADIUS + 0.5)).add(new Vector3(0, lift, 0));
            label.userData.text = text;
            globe.add(label);
        }

        const distance = origin.distanceTo(position);
        let arc = null;
        let curve = null;
        let trail = null;
        if (distance > 0.05) {
            const mid = origin.clone().add(position).multiplyScalar(0.5);
            mid.setLength(RADIUS + distance * 0.5);
            curve = new QuadraticBezierCurve3(origin, mid, position);
            const arcGeo = new BufferGeometry().setFromPoints(curve.getPoints(80));
            arcGeo.setDrawRange(0, 0);
            arc = new Line(arcGeo, arcMat);
            const trailGeo = new BufferGeometry();
            trailGeo.setAttribute('position', new BufferAttribute(new Float32Array(TRAIL * 3), 3));
            trail = new Points(trailGeo, trailMat);
            globe.add(arc, trail);
        }
        return { market, marker, beam, pulse, label, arc, curve, trail, phase: Math.random(), focus: 0 };
    });

    const applyColors = (c) => {
        const accent = c.vivid;
        const blending = c.light ? NormalBlending : AdditiveBlending;
        core.material.color.copy(c.bg);
        landDots.material.color.copy(c.light ? c.iris : c.text);
        landDots.material.opacity = c.light ? 0.8 : 0.78;
        oceanDots.material.color.copy(c.light ? c.iris : c.muted);
        oceanDots.material.opacity = c.light ? 0.18 : 0.22;
        atmosphere.material.uniforms.uColor.value.copy(c.iris);
        atmosphere.material.uniforms.uStrength.value = c.light ? 0.22 : 0.65;
        [atmosphere, rim].forEach((m) => {
            m.material.blending = blending;
            m.material.needsUpdate = true;
        });
        rim.material.uniforms.uStrength.value = c.light ? 0.3 : 0.35;
        rim.material.uniforms.uColor.value.copy(c.iris);
        homeMarker.material.color.copy(c.coral);
        markerMat.color.copy(accent);
        arcMat.color.copy(accent);
        trailMat.color.copy(c.light ? c.iris : accent);
        trailMat.blending = blending;
        trailMat.needsUpdate = true;
        items.forEach((item) => {
            item.pulse.material.color.copy(accent);
            item.beam.material.color.copy(accent);
            item.beam.material.blending = blending;
            item.beam.material.needsUpdate = true;
            item.label?.userData.text.redraw(c.light ? '#0e101a' : '#eef0f7', c.light ? 'rgba(255,255,255,0.88)' : 'rgba(8,9,15,0.72)');
        });
    };
    applyColors(stage.colors);
    stage.onTheme(applyColors);

    stage.onResize(({ width }) => {
        camera.position.z = width < 420 ? 8 : 7.4;
    });

    // Idle motion sways around `anchor` (the markets) instead of spinning them out of view.
    const start = rotationFor(32, 40);
    const rot = { x: start.x, y: start.y, tx: start.x, ty: start.y, anchor: start.y };
    let dragging = false;
    let idleUntil = 0;
    let focused = [];

    const snap = () => {
        rot.x = rot.tx;
        rot.y = rot.ty;
        globe.rotation.set(rot.x, rot.y, 0);
        stage.render();
    };

    if (interactive) {
        let last = null;
        host.addEventListener('pointerdown', (e) => {
            dragging = true;
            last = { x: e.clientX, y: e.clientY };
            host.setPointerCapture(e.pointerId);
        });
        host.addEventListener('pointermove', (e) => {
            if (!dragging) return;
            rot.ty += (e.clientX - last.x) * 0.006;
            rot.tx = Math.max(-1, Math.min(1, rot.tx + (e.clientY - last.y) * 0.004));
            last = { x: e.clientX, y: e.clientY };
            idleUntil = performance.now() + 3500;
            if (reduced) snap();
        });
        const end = () => {
            dragging = false;
            rot.anchor = rot.ty;
        };
        host.addEventListener('pointerup', end);
        host.addEventListener('pointercancel', end);
    }

    host.addEventListener('globe:focus', (e) => {
        const indices = (Array.isArray(e.detail) ? e.detail : [e.detail]).filter((i) => items[i]);
        if (!indices.length) return;
        focused = indices;
        // Aim at the average position of the focused markets.
        const centre = indices.reduce((v, i) => v.add(latLngToVector(items[i].market.lat, items[i].market.lng)), new Vector3()).normalize();
        const target = rotationFor(Math.asin(centre.y) / D2R, Math.atan2(centre.z, -centre.x) / D2R - 180);
        const twoPi = Math.PI * 2;
        let dy = (target.y - rot.ty) % twoPi;
        if (dy > Math.PI) dy -= twoPi;
        if (dy < -Math.PI) dy += twoPi;
        rot.ty += dy;
        rot.tx = target.x;
        rot.anchor = rot.ty;
        idleUntil = performance.now() + 6000;
        if (reduced) snap();
    });

    // Apply a focus requested before this scene finished loading.
    if (host.dataset.focus) host.dispatchEvent(new CustomEvent('globe:focus', { detail: JSON.parse(host.dataset.focus) }));

    const tmp = new Vector3();
    stage.onFrame((t, dt) => {
        if (!dragging && performance.now() > idleUntil) {
            rot.ty += (rot.anchor + Math.sin(t * 0.3) * 0.4 - rot.ty) * Math.min(1, dt * 0.8);
        }
        rot.x += (rot.tx - rot.x) * Math.min(1, dt * 3.5);
        rot.y += (rot.ty - rot.y) * Math.min(1, dt * 3.5);
        globe.rotation.set(rot.x, rot.y, 0);

        const draw = Math.min(1, Math.max(0, (t - 0.3) / 1.6));
        items.forEach((item, i) => {
            const isFocused = focused.includes(i);
            item.focus += ((isFocused ? 1 : 0) - item.focus) * Math.min(1, dt * 5);
            const p = (t * 0.55 + item.phase) % 1;
            item.pulse.scale.setScalar(1 + p * 1.6 + item.focus);
            item.pulse.material.opacity = (1 - p) * 0.9;
            item.marker.scale.setScalar(1 + item.focus * 0.9);
            item.beam.scale.y = Math.max(0.001, (0.3 + Math.sin(t * 2 + item.phase * 6) * 0.05 + item.focus * 0.4) * draw);
            if (item.label) {
                // Fade labels as they turn to the far side.
                item.label.getWorldPosition(tmp);
                const facing = Math.max(0, Math.min(1, tmp.z * 0.7));
                // Only the focused region's labels show, to keep the globe uncluttered.
                item.label.material.opacity = facing * (focused.length ? item.focus : 0.85);
            }
            if (item.arc) {
                item.arc.geometry.setDrawRange(0, Math.floor(81 * draw));
                const head = (t * 0.3 + item.phase) % 1;
                const arr = item.trail.geometry.attributes.position.array;
                for (let k = 0; k < TRAIL; k++) {
                    item.curve.getPoint(Math.max(0, head - k * 0.012), tmp);
                    arr[k * 3] = tmp.x;
                    arr[k * 3 + 1] = tmp.y;
                    arr[k * 3 + 2] = tmp.z;
                }
                item.trail.geometry.attributes.position.needsUpdate = true;
                item.trail.visible = draw >= 1;
            }
        });
    });
}
