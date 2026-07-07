import { defineConfig } from 'vite'
import tailwindcss from '@tailwindcss/vite'
import fs from 'node:fs'
import path from 'node:path'
import { fileURLToPath } from 'node:url'

const __dirname = path.dirname(fileURLToPath(import.meta.url))

// Lit app.baseURL depuis le .env (non versionné) pour éviter de coder le domaine en dur ici
function getHmrHost() {
    try {
        const env   = fs.readFileSync(path.resolve(__dirname, '.env'), 'utf-8')
        const match = env.match(/^app\.baseURL\s*=\s*['"]?https?:\/\/([^'"/\s]+)/m)
        return match ? match[1] : 'localhost'
    } catch {
        return 'localhost'
    }
}

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
            host: getHmrHost(),
            port: 5173,
        },
    },
})
