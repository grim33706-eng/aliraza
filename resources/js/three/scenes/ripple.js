// Contact — a signal broadcasting outward: rippling rings of points around a
// glowing source.
import {
    AdditiveBlending,
    BufferAttribute,
    BufferGeometry,
    DoubleSide,
    Group,
    Mesh,
    MeshBasicMaterial,
    NormalBlending,
    Points,
    RingGeometry,
    ShaderMaterial,
    SphereGeometry,
} from 'three';

export default function build(stage) {
    const { scene, camera, pointer, lowPower } = stage;
    const root = new Group();
    scene.add(root);

    const rings = lowPower ? 20 : 34;
    const perRing = lowPower ? 70 : 120;
    const positions = new Float32Array(rings * perRing * 3);
    let k = 0;
    for (let r = 1; r <= rings; r++) {
        for (let i = 0; i < perRing; i++) {
            const a = (i / perRing) * Math.PI * 2;
            const radius = r * 0.42;
            positions[k++] = Math.cos(a) * radius;
            positions[k++] = 0;
            positions[k++] = Math.sin(a) * radius;
        }
    }
    const geometry = new BufferGeometry();
    geometry.setAttribute('position', new BufferAttribute(positions, 3));

    const material = new ShaderMaterial({
        transparent: true,
        depthWrite: false,
        uniforms: {
            uTime: { value: 0 },
            uColorA: { value: stage.colors.iris.clone() },
            uColorB: { value: stage.colors.accent.clone() },
            uSize: { value: 2.4 * stage.renderer.getPixelRatio() },
            uOpacity: { value: 0.9 },
        },
        vertexShader: /* glsl */ `
            uniform float uTime;
            uniform float uSize;
            varying float vWave;
            varying float vDist;
            void main() {
                vec3 p = position;
                float d = length(p.xz);
                float wave = sin(d * 1.1 - uTime * 2.2);
                p.y = wave * 0.35 * exp(-d * 0.08);
                vWave = wave;
                vDist = d;
                vec4 mv = modelViewMatrix * vec4(p, 1.0);
                gl_PointSize = uSize * (12.0 / -mv.z);
                gl_Position = projectionMatrix * mv;
            }`,
        fragmentShader: /* glsl */ `
            uniform vec3 uColorA;
            uniform vec3 uColorB;
            uniform float uOpacity;
            varying float vWave;
            varying float vDist;
            void main() {
                float r = length(gl_PointCoord - 0.5);
                if (r > 0.5) discard;
                vec3 color = mix(uColorA, uColorB, smoothstep(0.2, 1.0, vWave));
                float fade = 1.0 - smoothstep(6.0, 14.0, vDist);
                gl_FragColor = vec4(color, (1.0 - r * 2.0) * fade * uOpacity);
            }`,
    });
    const field = new Points(geometry, material);
    root.add(field);

    // Glowing source + expanding halo rings.
    const source = new Mesh(new SphereGeometry(0.28, 32, 32), new MeshBasicMaterial());
    source.position.y = 0.4;
    root.add(source);
    const halos = [0, 1, 2].map((i) => {
        const halo = new Mesh(new RingGeometry(0.98, 1, 96), new MeshBasicMaterial({ transparent: true, side: DoubleSide, depthWrite: false }));
        halo.rotation.x = -Math.PI / 2;
        halo.position.y = 0.05;
        halo.userData.offset = i / 3;
        root.add(halo);
        return halo;
    });

    camera.position.set(0, 5.2, 10);
    camera.lookAt(0, 0, 0);

    const applyColors = (c) => {
        material.uniforms.uColorA.value.copy(c.iris);
        material.uniforms.uColorB.value.copy(c.vivid);
        material.blending = c.light ? NormalBlending : AdditiveBlending;
        material.uniforms.uOpacity.value = c.light ? 0.7 : 0.9;
        material.needsUpdate = true;
        source.material.color.copy(c.vivid);
        halos.forEach((h) => h.material.color.copy(c.vivid));
    };
    applyColors(stage.colors);
    stage.onTheme(applyColors);

    stage.onResize(({ width }) => {
        const wide = width >= 992;
        root.position.set(wide ? 4.2 : 0, wide ? -0.6 : -1.6, 0);
    });

    stage.onFrame((t) => {
        material.uniforms.uTime.value = t;
        source.scale.setScalar(1 + Math.sin(t * 2.2) * 0.12);
        halos.forEach((halo) => {
            const p = (t * 0.25 + halo.userData.offset) % 1;
            halo.scale.setScalar(0.5 + p * 7);
            halo.material.opacity = (1 - p) * 0.6;
        });
        root.rotation.y = t * 0.05 + pointer.sx * 0.2;
        root.rotation.x = pointer.sy * -0.08;
    });
}
