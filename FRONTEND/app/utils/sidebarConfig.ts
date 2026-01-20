import { AppUrl } from "~/composables/appUrl";

export interface MenuItem {
	id: string;
	name: string;
	url: string;
	icon: string;
	isForAdmin: boolean;
}

// sidebarMenu.js
export const sidebarMenu: MenuItem[] = [
	{
		id: "dashboard",
		name: "Tableau de bord",
		icon: "HomeIcon", // ou "ChartBarIcon" pour un graphique
		url: AppUrl.DASHBOARD,
		isForAdmin: true,
	},
	{
		id: "availability",
		name: "Consulter la disponibilité",
		icon: "CalendarDaysIcon", // ou "CalendarDaysIcon"
		url: AppUrl.RESERVATION_AVAILABILITY,
		isForAdmin: false,
	},
	{
		id: "new-reservation",
		name: "Faire une réservation",
		icon: "PlusCircleIcon", // ou "CalendarIcon"
		url: AppUrl.RESERVATION_CREATE,
		isForAdmin: false,
	},
	{
		id: "historical",
		name: "Mon historique",
		icon: "ClockIcon", // ou "DocumentTextIcon"
		url: AppUrl.HISTORICAL,
		isForAdmin: false,
	},
	{
		id: "reservations",
		name: "Gestion des réservations",
		icon: "ClipboardDocumentListIcon", // ou "CalendarDaysIcon"
		url: AppUrl.RESERVATIONS_LIST,
		isForAdmin: true,
	},
	{
		id: "vehicles",
		name: "Gestion du parc auto-mobile",
		icon: "TruckIcon", // icône de camion/véhicule
		url: AppUrl.VEHICLES_LISTE,
		isForAdmin: true,
	},
	{
		id: "users",
		name: "Gestion des utilisateurs",
		icon: "UsersIcon", // groupe d'utilisateurs
		url: AppUrl.USERS_LIST,
		isForAdmin: true,
	},
	{
		id: "missions",
		name: "Gestion des missions",
		icon: "BriefcaseIcon", // ou "MapIcon", "FlagIcon"
		url: AppUrl.MISSIONS_LIST,
		isForAdmin: true,
	},
];
