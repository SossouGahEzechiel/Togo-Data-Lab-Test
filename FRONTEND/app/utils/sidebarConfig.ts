import { AppUrl } from "~/composables/appUrl";

export interface MenuItem {
	id: string;
	name: string;
	url: string;
	icon: string;
}

// sidebarMenu.js
export const sidebarMenu = [
	{
		id: "dashboard",
		name: "Tableau de bord",
		icon: "HomeIcon", // ou "ChartBarIcon" pour un graphique
		url: AppUrl.DASHBOARD,
	},
	{
		id: "reservations",
		name: "Gestion des réservations",
		icon: "ClipboardDocumentListIcon", // ou "CalendarDaysIcon"
		url: AppUrl.RESERVATIONS_LIST,
	},
	{
		id: "new-reservation",
		name: "Faire une réservation",
		icon: "PlusCircleIcon", // ou "CalendarIcon"
		url: AppUrl.RESERVATION_CREATE,
	},
	{
		id: "historical",
		name: "Mon historique",
		icon: "ClockIcon", // ou "DocumentTextIcon"
		url: AppUrl.USER_DETAILS,
	},
	{
		id: "vehicles",
		name: "Gestion du parc auto-mobile",
		icon: "TruckIcon", // icône de camion/véhicule
		url: AppUrl.VEHICLES_LISTE,
	},
	{
		id: "users",
		name: "Gestion des utilisateurs",
		icon: "UsersIcon", // groupe d'utilisateurs
		url: AppUrl.USERS_LIST,
	},
	{
		id: "missions",
		name: "Gestion des missions",
		icon: "BriefcaseIcon", // ou "MapIcon", "FlagIcon"
		url: AppUrl.MISSIONS_LIST,
	},
] as MenuItem[];
