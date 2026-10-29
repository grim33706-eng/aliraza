// Portfolio — a curved wall of the real Ads Manager screenshots and
// recordings. Scrolling through the section sweeps the wall past the viewer.
import { CanvasTexture, Color, Group, Mesh, PlaneGeometry, ShaderMaterial, SRGBColorSpace, Sprite, SpriteMaterial, TextureLoader, Vector2 } from 'three';
import { css, textTexture, viewportProgress } from '../engine.js';

const vertexShader = /* glsl */ `varying vec2 vUv; void main(){ vUv = uv; gl_Position = projectionMatrix * modelViewMatrix * vec4(position, 1.0); }`;
const fragmentShader = /* glsl */ `
    uniform sampler2D map; uniform bool uUseMap; uniform vec3 uColor;
    uniform vec2 uSize; uniform vec2 uRepeat; uniform float uRadius; uniform float uOpacity; uniform float uFade;
    varying vec2 vUv;
    float rbox(vec2 p, vec2 b, float r) { vec2 q = abs(p) - b + r; return length(max(q, 0.0)) + min(max(q.x, q.y), 0.0) - r; }
    void main() {
        float d = rbox((vUv - 0.5) * uSize, uSize * 0.5, uRadius);
        float a = 1.0 - smoothstep(-0.015, 0.015, d);
        vec4 c = uUseMap ? texture2D(map, vec2(vUv.x * uRepeat.x, 1.0 - (1.0 - vUv.y) * uRepeat.y)) : vec4(uColor, 1.0);
        // uFade > 0: reflection that fades out downward.
        float fade = uFade > 0.0 ? smoothstep(0.0, 1.0, vUv.y) * uFade : 1.0;
        gl_FragColor = vec4(c.rgb, a * uOpacity * fade);
        #include <colorspace_fragment>
    }`;

const panelMaterial = (uniforms) =>
    new ShaderMaterial({
        transparent: true,
        depthWrite: false,
        vertexShader,
        fragmentShader,
        uniforms: {
            map: { value: null },
            uUseMap: { value: false },
            uColor: { value: new Color(1, 1, 1) },
            uSize: { value: new Vector2(1, 1) },
            uRepeat: { value: new Vector2(1, 1) },
            uRadius: { value: 0.14 },
            uOpacity: { value: 1 },
            uFade: { value: 0 },
            ...uniforms,
        },
    });

export default async function build(stage, host) {
    const { scene, camera, pointer, lowPower } = stage;
    const items = JSON.parse(host.dataset.images || '[]');
    const loader = new TextureLoader();
    const textures = await Promise.all(
        items.map((item) =>
            loader.loadAsync(item.src).then((tex) => {
                tex.colorSpace = SRGBColorSpace;
                tex.anisotropy = 4;
                return tex;
            }),
        ),
    );

    const R = 10;
    const H = 3;
    const step = 0.5;
    const wall = new Group();
    scene.add(wall);

    const panels = items.map((item, i) => {
        const aspect = item.w / item.h;
        const W = Math.min(H * aspect, 5.4);
        const group = new Group();
        const angle = (i - (items.length - 1) / 2) * step;
        // On a circle of radius R around the camera, facing inward.
        group.position.set(Math.sin(angle) * R, 0, -Math.cos(angle) * R);
        group.rotation.y = -angle;

        const frame = new Mesh(new PlaneGeometry(W + 0.12, H + 0.12), panelMaterial({ uColor: { value: stage.colors.iris.clone() }, uSize: { value: new Vector2(W + 0.12, H + 0.12) }, uRadius: { value: 0.18 }, uOpacity: { value: 0.5 } }));
        frame.position.z = -0.01;
        const repeat = new Vector2(Math.min(1, W / H / aspect), 1);
        const image = new Mesh(new PlaneGeometry(W, H), panelMaterial({ map: { value: textures[i] }, uUseMap: { value: true }, uSize: { value: new Vector2(W, H) }, uRepeat: { value: repeat } }));
        group.add(frame, image);

        if (!lowPower) {
            const reflection = new Mesh(new PlaneGeometry(W, H), panelMaterial({ map: { value: textures[i] }, uUseMap: { value: true }, uSize: { value: new Vector2(W, H) }, uRepeat: { value: repeat }, uFade: { value: 0.16 } }));
            reflection.scale.y = -1;
            reflection.position.y = -H - 0.25;
            group.add(reflection);
        }

        const caption = textTexture(CanvasTexture, item.title, { size: 36, weight: 600 });
        const label = new Sprite(new SpriteMaterial({ map: caption.texture, transparent: true, depthWrite: false }));
        label.scale.set(0.26 * caption.aspect, 0.26, 1);
        label.position.set(0, H / 2 + 0.36, 0);
        label.userData.text = caption;
        group.add(label);

        wall.add(group);
        return { group, frame, label, angle };
    });

    const applyColors = (c) => {
        panels.forEach((p) => {
            p.frame.material.uniforms.uColor.value.copy(c.light ? c.iris : c.accent);
            p.frame.material.uniforms.uOpacity.value = c.light ? 0.35 : 0.28;
            p.label.userData.text.redraw(css(c.text));
        });
    };
    applyColors(stage.colors);
    stage.onTheme(applyColors);

    stage.onResize(({ width }) => {
        camera.position.set(0, 0.6, width < 768 ? 2.5 : 1.5);
        camera.fov = width < 768 ? 60 : 45;
        camera.updateProjectionMatrix();
    });

    const span = (items.length - 1) * step;
    let sweep = -span / 2;
    stage.onFrame((t, dt) => {
        // Scrolling through the section sweeps from the first panel to the last.
        const progress = Math.min(1, Math.max(0, (viewportProgress(host) - 0.15) / 0.7));
        const target = -span / 2 + progress * span;
        sweep += (target - sweep) * Math.min(1, dt * 4);
        wall.rotation.y = sweep;
        wall.rotation.x = pointer.sy * -0.03;
        camera.position.x = pointer.sx * 0.35;
        camera.lookAt(0, 0.25, -R);

        panels.forEach((p) => {
            // The panel in front comes forward; the others recede.
            const facing = Math.cos(p.angle - sweep);
            const focus = Math.max(0, (facing - 0.9) / 0.1);
            p.group.scale.setScalar(0.86 + focus * 0.14);
            p.group.position.y = Math.sin(t * 0.8 + p.angle * 4) * 0.06;
            p.label.material.opacity = 0.2 + focus * 0.8;
        });
    });
}
