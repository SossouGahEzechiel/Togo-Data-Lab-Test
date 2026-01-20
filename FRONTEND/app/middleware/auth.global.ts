import { defineNuxtRouteMiddleware, navigateTo } from "nuxt/app";
import { AppUrl } from "~/composables/appUrl";
import { useAuthStore } from "~/stores/AuthStore";
import { sidebarMenu } from "~/utils/sidebarConfig";
import { UserRoleEnum } from "~/types/UserRoleEnum";

export default defineNuxtRouteMiddleware((to, from) => {
	// Éviter les boucles
	if (to.path === from.path) {
		return;
	}

	const auth = useAuthStore();

	const publicRoutes = [AppUrl.HOME, AppUrl.LOGIN];
	const basePath = to.path.replace(/\/+$/, "") || "/";

	const isAuthenticated = auth.isAuthenticated;
	const hasConfirmedPassword = !!auth.user?.hasConfiguredPassword;
	const isAdmin = auth.user?.role === UserRoleEnum.ADMIN;

	// Route par défaut selon le rôle
	const getDefaultRoute = () => {
		return isAdmin ? AppUrl.DASHBOARD : AppUrl.RESERVATION_CREATE;
	};

	// 1) Routes publiques
	if (publicRoutes.includes(basePath as any)) {
		if (isAuthenticated) {
			const destination = hasConfirmedPassword
				? getDefaultRoute()
				: AppUrl.CONFIRM_MY_PASSWORD;

			if (basePath !== destination) {
				return navigateTo(destination, { replace: true });
			}
		}
		return;
	}

	// 2) Page de confirmation
	if (basePath === AppUrl.CONFIRM_MY_PASSWORD) {
		if (!isAuthenticated) {
			return navigateTo(AppUrl.LOGIN, { replace: true });
		}
		if (hasConfirmedPassword) {
			return navigateTo(getDefaultRoute(), { replace: true });
		}
		return;
	}

	// 3) Authentification requise
	if (!isAuthenticated) {
		return navigateTo(AppUrl.LOGIN, { replace: true });
	}

	// 4) Mot de passe non configuré
	if (!hasConfirmedPassword) {
		return navigateTo(AppUrl.CONFIRM_MY_PASSWORD, { replace: true });
	}

	// 5) Vérification des permissions admin
	const currentMenuItem = sidebarMenu.find((item) => item.url === basePath);

	if (currentMenuItem?.isForAdmin && !isAdmin) {
		const destination = AppUrl.RESERVATION_CREATE;

		if (basePath !== destination) {
			return navigateTo(destination, { replace: true });
		}
	}

	// Tout est ok
});
