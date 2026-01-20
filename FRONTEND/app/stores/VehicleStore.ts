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
		async findAll() {
			if (this.vehicles.length === 0) {
				this.isLoading = true;
			}
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
				const { data } = await useApi().get<Vehicle>(
					ApiUrl.parameterized(ApiUrl.VEHICLE_BY_ID, id),
				);
				return data;
			} catch (error) {
				this.errors = useValidationErrors(error);
				throw error;
			} finally {
				this.isLoading = false;
			}
		},

		async checkAvailability(id: string, from: string, to: string) {
			this.isLoading = true;
			try {
				const { data } = await useApi().post<boolean>(
					ApiUrl.parameterized(ApiUrl.VEHICLE_CHECK_AVAILABILITY, id),
					{ from, to },
				);
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
				const formData = this.prepareForm(vehicle);
				const { data } = await useApi().post<Vehicle>(
					ApiUrl.VEHICLES,
					formData,
				);
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
				const formData = this.prepareForm(vehicle);
				const { data } = await useApi().put<Vehicle>(
					ApiUrl.parameterized(ApiUrl.VEHICLE_BY_ID, vehicle.id),
					formData,
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

		prepareForm(vehicle: Vehicle) {
			const formData = new FormData();
			formData.append("brand", vehicle.brand);
			formData.append("model", vehicle.model);
			formData.append("type", vehicle.type);
			formData.append("registrationNumber", vehicle.registrationNumber);
			formData.append("registrationDate", vehicle.registrationDate);
			formData.append("seatsCount", vehicle.seatsCount.toString());
			formData.append("status", vehicle.status);
			formData.append("reason", vehicle.reason || "");
			// TODO: Revoir le chargement des images
			if (vehicle.images && vehicle.images.length > 0) {
				for (const image of vehicle.images as File[]) {
					formData.append("images[]", image);
				}
			}
			return formData;
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
