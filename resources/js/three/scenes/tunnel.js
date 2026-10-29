// Footer CTA — a tunnel of particle rings streaming toward the viewer.
import { AdditiveBlending, BufferAttribute, BufferGeometry, NormalBlending, Points, ShaderMaterial } from 'three';

export default function build(stage) {
    const { scene, camera, pointer, lowPower } = stage;
    const rings = lowPower ? 26 : 44;
    const perRing = lowPower ? 60 : 110;
    const depth = 40;

    const positions = new Float32Array(rings * perRing * 3);
    const seeds = new Float32Array(rings * perRing);
    let k = 0;
    for (let r = 0; r < rings; r++) {
        for (let i = 0; i < perRing; i++) {
            const a = (i / perRing) * Math.PI * 2;
            positions.set([Math.cos(a), Math.sin(a), -(r / rings) * depth], k * 3);
            seeds[k++] = Math.random();
        }
    }
    const geometry = new BufferGeometry();
    geometry.setAttribute('position', new BufferAttribute(positions, 3));
    geometry.setAttribute('seed', new BufferAttribute(seeds, 1));

    const material = new ShaderMaterial({
        transparent: true,
        depthWrite: false,
        blending: AdditiveBlending,
        uniforms: {
            uTime: { value: 0 },
            uDepth: { value: depth },
            uA: { value: stage.colors.iris.clone() },
            uB: { value: stage.colors.accent.clone() },
            uSize: { value: 3 * stage.renderer.getPixelRatio() },
            uOpacity: { value: 1 },
        },
        vertexShader: /* glsl */ `
            uniform float uTime; uniform float uDepth; uniform float uSize;
            attribute float seed;
            varying float vT; varying float vSeed;
            void main() {
                float z = mod(position.z + uTime * 4.0, uDepth) - uDepth;
                float t = 1.0 - (-z / uDepth);
                // Tunnel radius breathes and twists with depth.
                float radius = 3.2 + sin(z * 0.25 + uTime) * 0.5;
                float twist = z * 0.06 + uTime * 0.15;
                vec2 dir = mat2(cos(twist), -sin(twist), sin(twist), cos(twist)) * position.xy;
                vec3 p = vec3(dir * radius, z);
                vT = t; vSeed = seed;
                vec4 mv = modelViewMatrix * vec4(p, 1.0);
                gl_PointSize = uSize * (8.0 / -mv.z) * (0.6 + seed);
                gl_Position = projectionMatrix * mv;
            }`,
        fragmentShader: /* glsl */ `
            uniform vec3 uA; uniform vec3 uB; uniform float uOpacity;
            varying float vT; varying float vSeed;
            void main() {
                float r = length(gl_PointCoord - 0.5);
                if (r > 0.5) discard;
                vec3 c = mix(uA, uB, smoothstep(0.35, 1.0, vT + vSeed * 0.2));
                float a = (1.0 - r * 2.0) * smoothstep(0.0, 0.5, vT) * (1.0 - smoothstep(0.92, 1.0, vT));
                gl_FragColor = vec4(c, a * uOpacity);
            }`,
    });
    scene.add(new Points(geometry, material));
    camera.position.set(0, 0, 4);

    const applyColors = (c) => {
        material.uniforms.uA.value.copy(c.iris);
        material.uniforms.uB.value.copy(c.vivid);
        material.blending = c.light ? NormalBlending : AdditiveBlending;
        material.uniforms.uOpacity.value = c.light ? 0.7 : 1;
        material.needsUpdate = true;
    };
    applyColors(stage.colors);
    stage.onTheme(applyColors);

    stage.onFrame((t) => {
        material.uniforms.uTime.value = t;
        camera.position.x = pointer.sx * 0.8;
        camera.position.y = pointer.sy * 0.6;
        camera.lookAt(0, 0, -10);
    });
}
