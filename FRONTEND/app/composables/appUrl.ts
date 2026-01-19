export const AppUrl = {
	// Url Publiques
	HOME: "/",
	LOGIN: "/me-connecter",
	REGISTER: "/register",
	FORGOT_PASSWORD: "/forgot-password",
	CONFIRM_MY_PASSWORD: "/confirmer-mon-mot-de-passe",

	// Tout le reste a besoin d'authentification
	DASHBOARD: "/dashboard",


	MISSIONS_LIST: "gestion-des-missions",
	MISSION_DETAILS: "details-d-une-mission/:id",

	VEHICLES_LISTE: "gestion-des-vehicules",
	VEHICLE_DETAILS: "details-d-un-vehicule/:id",

	USERS_LIST: "gestion-des-utilisateurs",
	USER_DETAILS: "details-d-un-utilisateur/:id",

	RESERVATIONS_LIST: "gestion-des-reservations",
	RESERVATION_DETAILS: "details-d-une-reservation/:id",
	RESERVATION_CREATE: "ajouter-une-reservation",
	RESERVATION_EDIT: "modifier-une-reservation/:id",

	parameterize: (item: string, id: string) => item.replace(":id", id),
} as const;
