export default defineNuxtRouteMiddleware((to, from) => {
	const authStore = useAuthStore();

	// Initialiser l'auth
	authStore.initialize();

	// Pages protégées
	const protectedRoutes = ["/profile", "/my-reservations", "/reservations/new"];
	const adminRoutes = ["/admin", "/vehicles/new", "/missions/new"];

	// Vérifier l'authentification pour les routes protégées
	if (protectedRoutes.some((route) => to.path.startsWith(route))) {
		if (!authStore.isAuthenticated) {
			return navigateTo("/login");
		}
	}

	// Vérifier les droits admin
	if (adminRoutes.some((route) => to.path.startsWith(route))) {
		if (!authStore.isAdmin && !authStore.isModerator) {
			return navigateTo("/");
		}
	}

	// Rediriger les utilisateurs connectés depuis login/register
	if (["/login", "/register"].includes(to.path) && authStore.isAuthenticated) {
		return navigateTo("/");
	}
});
