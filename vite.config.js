import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/sass/app.scss', 
                'resources/sass/auth.scss',
                'resources/sass/chat.scss',
                'resources/sass/admin-layout.scss',
                'resources/sass/home-layout.scss',
                'resources/sass/employee-layout.scss',
                'resources/js/app.js'
            ],
            refresh: true,
        }),
    ],
});
