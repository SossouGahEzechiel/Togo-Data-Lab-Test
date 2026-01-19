import { defineNuxtRouteMiddleware, navigateTo } from "nuxt/app";
import { AppUrl } from "@/composables/appUrl";
import { useAuthStore } from "../stores/AuthStore";

export default defineNuxtRouteMiddleware((to) => {
  const auth = useAuthStore();

  const publicRoutes = [
    AppUrl.HOME,
    AppUrl.LOGIN,
    AppUrl.REGISTER,
    AppUrl.FORGOT_PASSWORD,
    // NE PAS mettre CONFIRM ici si tu veux qu’elle soit protégée
  ];

  // Normaliser le path (optionnel si tu as parfois un trailing slash)
  const basePath = to.path.replace(/\/+$/, '') || '/';

  const isAuthenticated = auth.isAuthenticated;
  const hasConfirmedPassword = !!auth.user?.hasConfiguredPassword;

  // 1) Utilisateur NON authentifié : ne PAS vérifier hasConfirmedPassword
  if (!isAuthenticated) {
    if (publicRoutes.includes(basePath as any)) {
      return; // laisser passer home/login/register/forgot
    }
    return navigateTo(AppUrl.LOGIN);
  }

  // 2) Utilisateur authentifié : exiger la confirmation si nécessaire
  if (!hasConfirmedPassword && basePath !== AppUrl.CONFIRM_MY_PASSWORD) {
    return navigateTo(AppUrl.CONFIRM_MY_PASSWORD);
  }

  // Si déjà confirmé et essaie d'aller sur /confirm, renvoyer vers dashboard
  if (hasConfirmedPassword && basePath === AppUrl.CONFIRM_MY_PASSWORD) {
    return navigateTo(AppUrl.DASHBOARD);
  }

  // 3) Utilisateur authentifié sur une route "publique" (ex: /login) → rediriger
  if (publicRoutes.includes(basePath as any)) {
    return navigateTo(hasConfirmedPassword ? AppUrl.DASHBOARD : AppUrl.CONFIRM_MY_PASSWORD);
  }

  // sinon laisser passer
});
