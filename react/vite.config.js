import { defineConfig } from "vite";
import react from "@vitejs/plugin-react";
import tailwindcss from "@tailwindcss/vite";
import path from "path";

// Production: build → public_html (Apache). base "/" with hashed assets.
export default defineConfig({
  plugins: [react(), tailwindcss()],
  base: "/",
  build: {
    outDir: "dist",
    emptyOutDir: true,
    manifest: true,
    rollupOptions: {
      input: path.resolve(__dirname, "index.html"),
      output: {
        entryFileNames: "assets/[name]-[hash].js",
        chunkFileNames: "assets/[name]-[hash].js",
        assetFileNames: "assets/[name]-[hash][extname]",
      },
    },
  },
  server: {
    port: 5173,
    watch: {
      // Windows often locks newly copied image files; avoid crashing the watcher.
      ignored: ["**/public/images/**"],
    },
    proxy: {
      "/api": "http://127.0.0.1:8000",
      "/t": "http://127.0.0.1:8000",
      "/media": "http://127.0.0.1:8000",
      "/up": "http://127.0.0.1:8000",
      "/health": "http://127.0.0.1:8000",
      "/django-admin": "http://127.0.0.1:8000",
    },
  },
});
