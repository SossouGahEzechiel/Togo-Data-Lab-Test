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
	modules: ["@nuxtjs/tailwindcss", "@nuxtjs/color-mode", "@pinia/nuxt"],

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
});
