import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/musical.css',
                'resources/css/astralquimium.css',
                'resources/css/constelacion.css',
                'resources/js/app.js',
                'resources/js/astralquimium.js',
                'resources/js/constelacionJuego.js'
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
