import { fileURLToPath, URL } from "node:url";
import { defineConfig } from "vite";
import vue from "@vitejs/plugin-vue";
import vueDevTools from "vite-plugin-vue-devtools";
import { resolve } from "path";
import AutoImport from "unplugin-auto-import/vite";
import { NaiveUiResolver } from "unplugin-vue-components/resolvers";
import Components from "unplugin-vue-components/vite";
import AutoRouter from "unplugin-vue-router/vite"; // 自动路由

// https://vite.dev/config/
export default defineConfig({
	plugins: [
		vue(),
		vueDevTools(),
		AutoRouter({
			// 自动路由
			routesFolder: "src/pages", // 扫描这个目录下的所有文件，自动创建路由
			dts: true,
		}),
		AutoImport({
			imports: [
				"vue",
				{
					"naive-ui": [
						"useDialog",
						"useMessage",
						"useNotification",
						"useLoadingBar",
					],
				},
			],
		}),
		Components({
			resolvers: [NaiveUiResolver()],
		}),
	],
	resolve: {
		alias: {
			"@": fileURLToPath(new URL("./src", import.meta.url)),
		},
	},
	server: {
		proxy: {
			"/api": {
				target: "http://127.0.0.1:8787",
				changeOrigin: true,
			},
		},
	},
	build: {
		outDir: "public",
		assetsDir: "assets",
		emptyOutDir: true,
		rollupOptions: {
			output: {
				// JS、CSS 等资源文件输出到 public/assets
				chunkFileNames: "assets/js/[name]-[hash].js",
				entryFileNames: "assets/js/[name]-[hash].js",
				assetFileNames: "assets/[ext]/[name]-[hash].[ext]",
			},
		},
	},
});
