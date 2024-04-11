import {defineConfig} from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/js/app.js',
                'resources/css/lib/lib-bootstrap.scss',
                'resources/js/home-and-map.js',
                'resources/css/casinos.css',
                'resources/js/lib-bootstrap.js'],

            refresh: true,
        }),
    ]
});


