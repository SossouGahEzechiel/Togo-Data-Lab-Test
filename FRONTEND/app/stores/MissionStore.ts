import { defineStore } from "pinia";
import type { Mission } from "~/types/Mission";

export const useMissionStore = defineStore("MissionStore", {
	state: () => ({
		missions: [] as Mission[],
		isLoading: false,
		errors: {} as ValidationErrors,
		isPersisting: false,
	}),

	actions: {
		async getAll() {
			this.isLoading = true;
			try {
				const { data } = await useApi().get<Mission[]>(ApiUrl.MISSIONS);
				this.missions = data;
			} catch (error) {
				this.errors = useValidationErrors(error);
				throw error;
			} finally {
				this.isLoading = false;
			}
		},

		async findOne(id: string) {
			this.isLoading = true;
			try {
				const { data } = await useApi().get<Mission>(
					ApiUrl.parameterized(ApiUrl.MISSION_BY_ID, id),
				);
				return data;
			} catch (error) {
				this.errors = useValidationErrors(error);
				throw error;
			} finally {
				this.isLoading = false;
			}
		},

		async store(mission: Mission) {
			this.isPersisting = true;
			try {
				const { data } = await useApi().post<Mission>(
					ApiUrl.MISSIONS,
					mission,
				);
				this.missions.push(data);
			} catch (error) {
				this.errors = useValidationErrors(error);
				throw error;
			} finally {
				this.isPersisting = false;
			}
		},

		async update(mission: Mission) {
			this.isPersisting = true;
			try {
				const { data } = await useApi().put<Mission>(
					ApiUrl.parameterized(ApiUrl.MISSION_BY_ID, mission.id),
					mission,
				);
				const index = this.missions.findIndex((_) => _.id === mission.id);
				this.missions[index] = data;
			} catch (error) {
				this.errors = useValidationErrors(error);
				throw error;
			} finally {
				this.isPersisting = false;
			}
		},

		async delete(id: string) {
			this.isPersisting = true;
			try {
				await useApi().del(ApiUrl.parameterized(ApiUrl.MISSION_BY_ID, id));
				this.missions = this.missions.filter((_) => _.id !== id);
			} catch (error) {
				this.errors = useValidationErrors(error);
				throw error;
			} finally {
				this.isPersisting = false;
			}
		},

		cleanStorage() {
			this.missions = [];
			this.isLoading = false;
			this.errors = {};
			this.isPersisting = false;
		},
	},
	persist: {
		storage: secureLsStorage,
		key: "mission-store",
		pick: ["missions"],
	},
});
