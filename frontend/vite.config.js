import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';
import path from 'node:path';

export default defineConfig({
  plugins: [
      vue(),
  ],
  resolve: { alias: { '@': path.resolve(__dirname, './src') } },
  base: '/assets/',

  build: {
    outDir: path.resolve(__dirname, '../public/assets'),
    assetsDir: '',
    emptyOutDir: true,
    manifest: true,
    rollupOptions: { input: './index.html' }
  },

});
