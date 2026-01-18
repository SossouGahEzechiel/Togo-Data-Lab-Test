import { defineStore } from "pinia";
import type { Mission } from "~/types";
import { api } from "~/composables/api";

interface MissionState {
	missions: Mission[];
	currentMission: Mission | null;
	isLoading: boolean;
}

export const useMissionStore = defineStore("mission", {
	state: (): MissionState => ({
		missions: [],
		currentMission: null,
		isLoading: false,
	}),

	actions: {
		async fetchMissions() {
			this.isLoading = true;
			try {
				const response = await api.getMissions();
				if (response.data) {
					this.missions = response.data;
				}
			} catch (error) {
				console.error("Failed to fetch missions:", error);
				throw error;
			} finally {
				this.isLoading = false;
			}
		},

		async fetchMission(id: string) {
			this.isLoading = true;
			try {
				const response = await api.getMission(id);
				if (response.data) {
					this.currentMission = response.data;
				}
			} catch (error) {
				console.error("Failed to fetch mission:", error);
				throw error;
			} finally {
				this.isLoading = false;
			}
		},

		async createMission(data: Partial<Mission>) {
			try {
				const response = await api.createMission(data);
				if (response.data) {
					this.missions.unshift(response.data);
					return { success: true, data: response.data };
				}
			} catch (error: any) {
				return {
					success: false,
					error: error.errors || error.message,
				};
			}
		},
	},

	getters: {
		activeMissions(): Mission[] {
			const today = new Date().toISOString().split("T")[0];
			return this.missions.filter(
				(mission) =>
					mission.from >= today! || (mission.to && mission.to >= today!),
			);
		},

		upcomingMissions(): Mission[] {
			const today = new Date().toISOString().split("T")[0];
			return this.missions.filter((mission) => mission.from > today!);
		},
	},
});
