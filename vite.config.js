import { defineConfig } from 'vite'
import tailwindcss from '@tailwindcss/vite'

export default defineConfig({
    plugins: [
        tailwindcss(),
    ],

    publicDir: false,

    build: {
        outDir: 'public/assets',
        assetsDir: '',
        emptyOutDir: true,
        manifest: true,
        rollupOptions: {
            input: 'resources/js/app.js',
        },
    },

    server: {
        host: '0.0.0.0',
        port: 5173,
        strictPort: true,
        cors: true,
        hmr: {
            protocol: 'ws',
            host: 'client.goldron.fr',
            port: 5173,
        },
    },
})
