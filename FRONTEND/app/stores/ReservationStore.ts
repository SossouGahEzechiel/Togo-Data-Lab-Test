import { defineStore } from "pinia";
import type { Reservation } from "~/types/Reservation";

export const useReservationStore = defineStore("ReservationStore", {
	state: () => ({
		reservations: [] as Reservation[],
		isLoading: false,
		errors: {} as ValidationErrors,
		isPersisting: false,
	}),

	actions: {
		async findAll() {
			this.isLoading = true;
			try {
				const { data } = await useApi().get<Reservation[]>(
					ApiUrl.RESERVATIONS
				);
				this.reservations = data;
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
				const { data } = await useApi().get<Reservation>(
					ApiUrl.parameterized(ApiUrl.RESERVATION_BY_ID, id)
				);
				return data;
			} catch (error) {
				this.errors = useValidationErrors(error);
				throw error;
			} finally {
				this.isLoading = false;
			}
		},

		async store(reservation: Reservation) {
			this.isPersisting = true;
			try {
				const { data } = await useApi().post<Reservation>(
					ApiUrl.RESERVATIONS,
					reservation
				);
				this.reservations.push(data);
				return data;
			} catch (error) {
				this.errors = useValidationErrors(error);
				throw error;
			} finally {
				this.isPersisting = false;
			}
		},

		async update(reservation: Reservation) {
			this.isPersisting = true;
			try {
				const { data } = await useApi().put<Reservation>(
					ApiUrl.parameterized(ApiUrl.RESERVATION_BY_ID, reservation.id),
					reservation
				);
				const index = this.reservations.findIndex((_) => _.id === reservation.id);
				if (index !== -1) {
					this.reservations[index] = data;
				}
				return data;
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
				await useApi().del(
					ApiUrl.parameterized(ApiUrl.RESERVATION_BY_ID, id)
				);
				this.reservations = this.reservations.filter((_) => _.id !== id);
			} catch (error) {
				this.errors = useValidationErrors(error);
				throw error;
			} finally {
				this.isPersisting = false;
			}
		},

		cleanStorage() {
			this.reservations = [];
			this.isLoading = false;
			this.errors = {};
			this.isPersisting = false;
		},
	},
	persist: {
		storage: secureLsStorage,
		key: "reservation-store",
		pick: ["reservations"],
	},
});