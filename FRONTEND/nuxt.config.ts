// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
	compatibilityDate: "2025-07-15",
	devtools: { enabled: true },
	app: {
		head: {
			title: "AutoParc TG",
			titleTemplate: "%s | AutoParc TG",
		},
	},
	modules: [
		"@nuxtjs/tailwindcss",
		"@nuxtjs/color-mode",
		"@pinia/nuxt",
		'@pinia-plugin-persistedstate/nuxt',
	],

	css: ["~/assets/css/tailwind.css"],

	tailwindcss: {
		cssPath: "~/assets/css/tailwind.css",
		configPath: "tailwind.config.js",
		exposeConfig: false,
		viewer: true,
	},

	colorMode: {
		classSuffix: "",
	},

	runtimeConfig: {
		public: {
			encryptionSecret: process.env.VITE_NUXT_PUBLIC_ENCRYPTION_SECRET,
		},
	},
	ssr: false,

	// pinia: {
	// 	storesDirs: ["./stores/**"],
	// },
});
