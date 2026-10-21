import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { bunny } from 'laravel-vite-plugin/fonts';
import tailwindcss from '@tailwindcss/vite';
import fs from 'fs';
import path from 'path';

function getPageScripts() {
    const pagesDir = path.resolve('resources/js/pages');
    if (!fs.existsSync(pagesDir)) return [];
    return fs.readdirSync(pagesDir, { recursive: true })
        .filter(file => typeof file === 'string' && file.endsWith('.js'))
        .map(file => `resources/js/pages/${file.replace(/\\/g, '/')}`);
}

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                ...getPageScripts(),
            ],
            refresh: true,
            fonts: [
                bunny('Instrument Sans', {
                    weights: [400, 500, 600],
                }),
            ],
        }),
        tailwindcss(),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
