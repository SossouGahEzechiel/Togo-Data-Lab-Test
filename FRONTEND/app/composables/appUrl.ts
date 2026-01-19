export const AppUrl = {
	// Url Publiques
	HOME: "/",
	LOGIN: "/me-connecter",
	REGISTER: "/register",
	FORGOT_PASSWORD: "/forgot-password",
	CONFIRM_MY_PASSWORD: "/confirmer-mon-mot-de-passe",

	// Tout le reste a besoin d'authentification
	DASHBOARD: "/dashboard",

	parameterize: (item: string, id: string) => item.replace(":id", id),
} as const;
