import { defineNuxtConfig } from "nuxt/config";

export default defineNuxtConfig({
	compatibilityDate: "2025-07-15",
	devtools: { enabled: true },

	modules: ["@pinia/nuxt", "@nuxt/ui", "@nuxtjs/color-mode", "nuxt-icon"],

	ui: {
		tailwindConfig: {
			configPath: "~/tailwind.config.ts",
		},
	},

	colorMode: {
		classSuffix: "",
		preference: "system",
		fallback: "light",
	},

	runtimeConfig: {
		public: {
			apiBaseUrl: process.env.VITE_API_URL || "http://localhost:8000",
		},
	},

	css: ["~/assets/css/main.css"],
});
