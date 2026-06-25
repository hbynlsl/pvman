import vue from "@vitejs/plugin-vue";
import components from "unplugin-vue-components/vite";
import autoImport from "unplugin-auto-import/vite";
import { VarletImportResolver } from "@varlet/import-resolver";
import { defineConfig } from "vite";
import { resolve } from "path";
import Pages from "vite-plugin-pages";

export default defineConfig({
	plugins: [
		vue(),
		components({
			resolvers: [VarletImportResolver()],
		}),
		autoImport({
			resolvers: [VarletImportResolver({ autoImport: true })],
		}),
		Pages({
			// 扫描页面目录
			dirs: "src/pages",
			// 路由基础路径
			baseRoute: "/",
			// 排除文件
			exclude: ["**/components/**", "**/layout/**"],
		}),
	],
	resolve: {
		alias: {
			// 把 @ 映射到 src 文件夹
			"@": resolve(__dirname, "src"),
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
			// input: {
			//   index: resolve(__dirname, 'src/assets/index.html'),
			// },
			output: {
				// JS、CSS 等资源文件输出到 public/assets
				chunkFileNames: "assets/js/[name]-[hash].js",
				entryFileNames: "assets/js/[name]-[hash].js",
				assetFileNames: "assets/[ext]/[name]-[hash].[ext]",
			},
		},
	},
});
