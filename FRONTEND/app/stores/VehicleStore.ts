import { defineStore } from "pinia";
import type { Vehicle } from "~/types/Vehicle";

export const useVehicleStore = defineStore("VehicleStore", {
	state: () => ({
		vehicles: [] as Vehicle[],
		isLoading: false,
		errors: {} as ValidationErrors,
		isPersisting: false,
	}),

	actions: {
		async getAll() {
			this.isLoading = true;
			try {
				const { data } = await useApi().get<Vehicle[]>(ApiUrl.VEHICLES);
				this.vehicles = data;
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
				const { data } = await useApi().get<Vehicle>(ApiUrl.parameterized(ApiUrl.VEHICLE_BY_ID, id));
				return data;
			} catch (error) {
				this.errors = useValidationErrors(error);
				throw error;
			} finally {
				this.isLoading = false;
			}
		},

		async store(vehicle: Vehicle) {
			this.isPersisting = true;
			try {
				const { data } = await useApi().post<Vehicle>(ApiUrl.VEHICLES, vehicle);
				this.vehicles.push(data);
			} catch (error) {
				this.errors = useValidationErrors(error);
				throw error;
			} finally {
				this.isPersisting = false;
			}
		},

		async update(vehicle: Vehicle) {
			this.isPersisting = true;
			try {
				const { data } = await useApi().put<Vehicle>(
					ApiUrl.parameterized(ApiUrl.VEHICLE_BY_ID, vehicle.id),
					vehicle
				);
				const index = this.vehicles.findIndex((_) => _.id === vehicle.id);
				this.vehicles[index] = data;
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
				await useApi().del(ApiUrl.parameterized(ApiUrl.VEHICLE_BY_ID, id));
				this.vehicles = this.vehicles.filter((_) => _.id !== id);
			} catch (error) {
				this.errors = useValidationErrors(error);
				throw error;
			} finally {
				this.isPersisting = false;
			}
		},

		cleanStorage() {
			this.vehicles = [];
			this.isLoading = false;
			this.errors = {};
			this.isPersisting = false;
		},
	},
	persist: {
		storage: secureLsStorage,
		key: "vehicle-store",
		pick: ["vehicles"],
	},
});
