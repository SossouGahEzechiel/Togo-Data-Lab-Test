import { defineStore } from "pinia";
import {
	isAdmin,
	type ConfigurePasswordCredential,
	type LoginCredential,
	type User,
} from "~/types/User";

export const useAuthStore = defineStore("AuthStore", {
	state: () => ({
		user: null as User | null,
		token: null as string | null,
		errors: {} as ValidationErrors,
	}),
	getters: {
		isAuthenticated: (state) => {
			return !!(state.user && state.token);
		},

		fullName: (state) => {
			return `${state.user?.firstName} ${state.user?.lastName}`;
		},

		userInitials: (state) => {
			if (!state.user) {
				return "";
			}

			const firstName = state.user.firstName?.trim();
			const lastName = state.user.lastName?.trim();

			if (!firstName || !lastName) {
				return "";
			}

			return `${firstName.charAt(0)}${lastName.charAt(0)}`.toUpperCase();
		},

		isAdmin: (state) => {
			return isAdmin(state.user);
		},

		userRole: (state) => {
			return state.user?.role || null;
		},
	},
	actions: {
		async login(credentials: LoginCredential) {
			const api = useApi();
			try {
				const { data } = await api.post<User>(ApiUrl.LOGIN, credentials);
				this.user = data;
				this.token = data.token;
			} catch (error) {
				this.errors = useValidationErrors(error);
				console.log("General error:", this.errors._general);
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
				this.cleanOtherStorages();
				navigateTo(AppUrl.LOGIN);
			}
		},
		async configurePassword(credentials: ConfigurePasswordCredential) {
			try {
				const { data } = await useApi().put<User>(ApiUrl.CONFIGURE_PASSWORD, {
					...credentials,
				});
				this.user = data;
			} catch (error: any) {
				this.errors = useValidationErrors(error);
				throw error;
			}
		},
		cleanStorage() {
			this.user = null;
			this.token = null;
			this.errors = {};
		},

		cleanOtherStorages() {
			useMissionStore().cleanStorage();
			useReservationStore().cleanStorage();
			useUserStore().cleanStorage();
			useVehicleStore().cleanStorage();
		},
	},
	persist: {
		storage: secureLsStorage,
		key: "auth-store",
		pick: ["user", "token"],
	},
});
