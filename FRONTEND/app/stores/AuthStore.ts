import { defineStore } from "pinia";
import type { LoginCredential, User } from "~/types/User";

// interface AuthStoreInterface {
// 	user: User | null;
// 	token: string | null;
// 	validationErrors: {};
// 	newPasswordErrors: {};
// 	resetValidationErrors: {};
// }

export const useAuthStore = defineStore("AuthStore", {
	state: () => ({
		user: null as User | null,
		token: null as string | null,
		validationErrors: {} as ValidationErrors,
		newPasswordErrors: {} as ValidationErrors,
		resetValidationErrors: {} as ValidationErrors,
	}),

	getters: {
		isAuthenticated: (state) => {
			return !!(state.user && state.token);
		},
		fullName: (state) => {
			return `${state.user?.firstName} ${state.user?.lastName}`;
		},
	},

	actions: {
		async login(credentials: LoginCredential) {
			const api = useApi();

			try {
				const { data } = await api.post<User>(ApiUrl.LOGIN, credentials);

				this.user = data;
				this.token = data.token;

				useAlert().showAlert(
					`Ravi de vous revoir ${data.lastName} ${data.firstName}`,
					"success",
				);
			} catch (error) {
				this.validationErrors = useValidationErrors(error);
				console.log("General error:", this.validationErrors._general);

				throw error;
			}
		},

		async logout() {
			try {
				await useApi().post(ApiUrl.LOGOUT, {});
			} catch (error) {
				console.log("Logout error:", error);
			} finally {
				this.cleanStorage();
				navigateTo(AppUrl.LOGIN);
			}
		},

		async initPasswordReset(email: string) {
			try {
				await useApi().post(ApiUrl.FORGOT_PASSWORD, { email });
			} catch (error: any) {
				this.resetValidationErrors = useValidationErrors(error);
				throw new Error(
					error.data?.message ||
						error.message ||
						error.error ||
						"Une erreur est survenue",
				);
			}
		},

		cleanStorage() {
			this.user = null;
			this.token = null;
		},
	},

	// Configuration de la persistance
	persist: {
		storage: secureLsStorage,
		// Optionnel : personnaliser la clé de stockage
		key: "auth-store",
		// Optionnel : choisir quelles propriétés persister
		pick: ["user", "token"],
	},
});
