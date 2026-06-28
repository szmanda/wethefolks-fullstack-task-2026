import { defineConfig } from "vite";
import vue from "@vitejs/plugin-vue";

export default () => {
  return defineConfig({
    server: {
      host: '0.0.0.0',
      port: 3000,
      strictPort: true,
      proxy: {
      '/api': {
        target: 'https://nginx',
        changeOrigin: true,
        secure: false,
      },
      },
    },
    build: {
      outDir: "./public",
    },
    base: "/app/",
    plugins: [vue()],
  });
};