import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/scss/app.scss', 'resources/js/app.js', 'resources/scss/admin.scss', 'resources/js/admin.js'],
            refresh: true,
        }),
    ],
    css: {
        preprocessorOptions: {
            scss: {
                // Bootstrap 5.3 still uses @import and global Sass functions.
                quietDeps: true,
                silenceDeprecations: ['import', 'global-builtin', 'color-functions', 'if-function'],
            },
        },
    },
    build: {
        // three.js is ~140 kB gzipped; it's lazy-loaded, so the default warning is noise.
        chunkSizeWarningLimit: 600,
        rollupOptions: {
            output: {
                // Keep Three.js in its own long-cached chunk.
                manualChunks: (id) => (id.includes('node_modules/three') ? 'three' : undefined),
            },
        },
    },
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
