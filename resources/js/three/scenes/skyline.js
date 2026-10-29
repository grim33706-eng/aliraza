// Results — a skyline of bars that grows out of the ground like a live chart.
import { AmbientLight, BoxGeometry, Color, DirectionalLight, InstancedMesh, MeshStandardMaterial, Object3D } from 'three';

export default function build(stage) {
    const { scene, camera, pointer, lowPower } = stage;
    const cols = lowPower ? 10 : 16;
    const rows = lowPower ? 6 : 9;
    const gap = 0.95;
    const count = cols * rows;

    const geometry = new BoxGeometry(0.62, 1, 0.62);
    geometry.translate(0, 0.5, 0); // grow from the base
    const material = new MeshStandardMaterial({ metalness: 0.2, roughness: 0.45 });
    const bars = new InstancedMesh(geometry, material, count);
    scene.add(bars);

    const cells = [];
    for (let x = 0; x < cols; x++) {
        for (let z = 0; z < rows; z++) {
            cells.push({ x: (x - cols / 2) * gap, z: (z - rows / 2) * gap, seed: Math.random() * 10, trend: x / cols });
        }
    }

    const ambient = new AmbientLight(0xffffff, 0.55);
    const key = new DirectionalLight(0xffffff, 2.2);
    key.position.set(6, 10, 6);
    const back = new DirectionalLight(0xffffff, 1);
    back.position.set(-6, 4, -6);
    scene.add(ambient, key, back);

    const dummy = new Object3D();
    const tmp = new Color();
    let palette = stage.colors;

    const heightAt = (cell, t) => {
        // Upward trend left→right, with gentle "live data" motion.
        const base = 0.4 + cell.trend * 2.6;
        return Math.max(0.12, base + Math.sin(t * 0.8 + cell.seed) * 0.45 + Math.cos(t * 0.5 + cell.z) * 0.3);
    };

    const update = (t) => {
        const intro = Math.min(1, t / 2.2);
        const ease = 1 - Math.pow(1 - intro, 3);
        cells.forEach((cell, i) => {
            const h = heightAt(cell, t) * ease;
            dummy.position.set(cell.x, 0, cell.z);
            dummy.scale.set(1, h, 1);
            dummy.updateMatrix();
            bars.setMatrixAt(i, dummy.matrix);
            tmp.copy(palette.solid).lerp(palette.vivid, Math.min(1, h / 3.2));
            bars.setColorAt(i, tmp);
        });
        bars.instanceMatrix.needsUpdate = true;
        if (bars.instanceColor) bars.instanceColor.needsUpdate = true;
    };

    const applyColors = (c) => {
        palette = c;
        back.color.copy(c.iris);
        update(4);
    };
    applyColors(stage.colors);
    stage.onTheme(applyColors);

    stage.onResize(({ width }) => {
        const wide = width >= 992;
        bars.position.set(wide ? 4.5 : 0, wide ? -2.2 : -3.6, wide ? 0 : -3);
    });

    camera.position.set(0, 6.5, 13);
    camera.lookAt(0, 0, 0);

    stage.onFrame((t) => {
        update(t);
        bars.rotation.y = -0.55 + t * 0.04 + pointer.sx * 0.2;
        camera.position.y = 6.5 + pointer.sy * 0.6 + window.scrollY * 0.003;
        camera.lookAt(0, 0, 0);
    });
}
