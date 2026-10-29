// Portfolio — an undulating field of points, animated on the GPU.
import { AdditiveBlending, BufferAttribute, BufferGeometry, NormalBlending, Points, ShaderMaterial, Vector2 } from 'three';

export default function build(stage) {
    const { scene, camera, pointer, lowPower } = stage;
    const cols = lowPower ? 80 : 150;
    const rows = lowPower ? 40 : 70;
    const spacing = 0.32;
    const positions = new Float32Array(cols * rows * 3);
    let k = 0;
    for (let x = 0; x < cols; x++) {
        for (let z = 0; z < rows; z++) {
            positions[k++] = (x - cols / 2) * spacing;
            positions[k++] = 0;
            positions[k++] = (z - rows / 2) * spacing;
        }
    }
    const geometry = new BufferGeometry();
    geometry.setAttribute('position', new BufferAttribute(positions, 3));

    const material = new ShaderMaterial({
        transparent: true,
        depthWrite: false,
        uniforms: {
            uTime: { value: 0 },
            uPointer: { value: new Vector2() },
            uColorA: { value: stage.colors.iris.clone() },
            uColorB: { value: stage.colors.accent.clone() },
            uSize: { value: (lowPower ? 3.2 : 2.6) * stage.renderer.getPixelRatio() },
            uOpacity: { value: 0.9 },
        },
        vertexShader: /* glsl */ `
            uniform float uTime;
            uniform vec2 uPointer;
            uniform float uSize;
            varying float vHeight;
            varying float vFade;
            void main() {
                vec3 p = position;
                float d = distance(p.xz, uPointer * vec2(14.0, 8.0));
                p.y = sin(p.x * 0.35 + uTime * 0.9) * 0.55
                    + cos(p.z * 0.45 + uTime * 0.7) * 0.45
                    + sin((p.x + p.z) * 0.2 + uTime * 0.5) * 0.3
                    + exp(-d * 0.35) * 0.9 * sin(d * 1.2 - uTime * 3.0);
                vHeight = p.y;
                vec4 mv = modelViewMatrix * vec4(p, 1.0);
                vFade = smoothstep(-34.0, -8.0, mv.z);
                gl_PointSize = uSize * (12.0 / -mv.z);
                gl_Position = projectionMatrix * mv;
            }`,
        fragmentShader: /* glsl */ `
            uniform vec3 uColorA;
            uniform vec3 uColorB;
            uniform float uOpacity;
            varying float vHeight;
            varying float vFade;
            void main() {
                float r = length(gl_PointCoord - 0.5);
                if (r > 0.5) discard;
                vec3 color = mix(uColorA, uColorB, smoothstep(-0.6, 1.2, vHeight));
                gl_FragColor = vec4(color, (1.0 - r * 2.0) * uOpacity * vFade);
            }`,
    });
    const points = new Points(geometry, material);
    scene.add(points);

    camera.position.set(0, 4.2, 11);
    camera.lookAt(0, -0.5, 0);

    const applyColors = (c) => {
        material.uniforms.uColorA.value.copy(c.iris);
        material.uniforms.uColorB.value.copy(c.vivid);
        material.blending = c.light ? NormalBlending : AdditiveBlending;
        material.uniforms.uOpacity.value = c.light ? 0.75 : 0.9;
        material.needsUpdate = true;
    };
    applyColors(stage.colors);
    stage.onTheme(applyColors);

    stage.onFrame((t) => {
        material.uniforms.uTime.value = t;
        material.uniforms.uPointer.value.set(pointer.sx, -pointer.sy);
        points.rotation.y = Math.sin(t * 0.05) * 0.15 + pointer.sx * 0.08;
        points.position.y = -1 - window.scrollY * 0.003;
    });
}
