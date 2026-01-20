import { AppUrl } from "~/composables/appUrl";
import { useAuthStore } from "~/stores/AuthStore";

export default defineNuxtRouteMiddleware((to, from) => {
	const authStore = useAuthStore();

	// Si pas admin ET la destination n'est pas déjà le dashboard
	if (!authStore.isAdmin && to.path !== AppUrl.DASHBOARD) {
		return navigateTo(AppUrl.DASHBOARD);
	}
});
