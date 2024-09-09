import {defineConfig} from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/js/app.js',
                'resources/css/lib/lib-bootstrap.scss',
                'resources/js/home-and-map.js',
                'resources/css/casinos.css',
                'resources/js/lib-bootstrap.js'
            ],
            refresh: true,
        }),
    ],
    build: {
        rollupOptions: {
            output: {
                // Diviser les dépendances des node_modules en chunks séparés
                manualChunks(id) {
                    if (id.includes('node_modules')) {
                        return id.toString().split('node_modules/')[1].split('/')[0].toString();
                    }
                },
            },
        },
        // Augmentez le nombre de workers ou la mémoire pour gérer les gros fichiers
        chunkSizeWarningLimit: 1000, // Ajustez la limite de taille de chunk si nécessaire
    },
});
