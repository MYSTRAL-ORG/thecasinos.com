import {defineConfig} from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/js/app.js', 'resources/js/home-and-map.js',
                'resources/css/casinos.css',],

            refresh: true,
        }),
    ]
});


