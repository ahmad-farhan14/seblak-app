import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        tailwindcss(),
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    // TAMBAHKAN BLOK SERVER INI DI BAWAH PLUGINS AGAR WEBSOCKET VITE BERJALAN AMAN VIA HTTPS
    server: {
        hmr: {
            protocol: 'wss',
        },
    },
});