import { fileURLToPath, URL } from 'node:url'

import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import vueDevTools from 'vite-plugin-vue-devtools'

// https://vite.dev/config/
export default defineConfig({
  plugins: [
    vue(),
    vueDevTools(),
  ],
  resolve: {
    alias: {
      '@': fileURLToPath(new URL('./src', import.meta.url))
    },
  },
  server: {
    host: '0.0.0.0',
    port: 3000,
    strictPort: true,
    open: true,
    cors: true,
    proxy: {
      // Proxy for PHP API
      '/php-api': {
        target: 'http://localhost:8080',
        changeOrigin: true,
        rewrite: (path) => path.replace(/^\/php-api/, '/api'),
      },
      // Proxy for localhost:9851
      '/api/localhost/9851': {
        target: 'http://localhost:9851',
        changeOrigin: true,
        rewrite: (path) => path.replace(/^\/api\/localhost\/9851/, ''),
      },
      // Proxy for localhost:9854  
      '/api/localhost/9854': {
        target: 'http://localhost:9854',
        changeOrigin: true,
        rewrite: (path) => path.replace(/^\/api\/localhost\/9854/, ''),
      },
      // Proxy for your P2P server
      '/api/192.168.178.222/9854': {
        target: 'http://192.168.178.222:9854',
        changeOrigin: true,
        rewrite: (path) => path.replace(/^\/api\/192\.168\.178\.222\/9854/, ''),
      },
    }
  }
})
