import { defineStore } from "pinia";
import type { Reservation } from "~/types/Reservation";
import type { ReservationStatusEnum } from "~/types/ReservationEnum";

export const useReservationStore = defineStore("ReservationStore", {
	state: () => ({
		reservations: [] as Reservation[],
		isLoading: false,
		errors: {} as ValidationErrors,
		isPersisting: false,
	}),

	actions: {
		async findAll(userId?: string) {
			this.isLoading = true;
			try {
				let url = ApiUrl.queryable(ApiUrl.RESERVATIONS, {});
				if (userId) {
					url = ApiUrl.queryable(ApiUrl.RESERVATIONS, { userId });
				}
				const { data } = await useApi().get<Reservation[]>(url);
				this.reservations = data;
				this.errors = {};
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
					ApiUrl.parameterized(ApiUrl.RESERVATION_BY_ID, id),
				);
				this.errors = {};
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
					reservation,
				);
				this.reservations.push(data);
				this.errors = {};
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
					reservation,
				);
				const index = this.reservations.findIndex(
					(_) => _.id === reservation.id,
				);
				if (index !== -1) {
					this.reservations[index] = data;
				}
				this.errors = {};
				return data;
			} catch (error) {
				this.errors = useValidationErrors(error);
				throw error;
			} finally {
				this.isPersisting = false;
			}
		},

		async applyDecision(id: string, status: ReservationStatusEnum) {
			try {
				const { data } = await useApi().patch<Reservation>(
					ApiUrl.parameterized(ApiUrl.RESERVATION_DECISION, id),
					{ status },
				);
				this.reservations = this.reservations.map((_) =>
					_.id === id ? data : _,
				);
				this.errors = {};
			} catch (error) {
				this.errors = useValidationErrors(error);
				throw error;
			}
		},

		async delete(id: string) {
			this.isPersisting = true;
			try {
				await useApi().del(ApiUrl.parameterized(ApiUrl.RESERVATION_BY_ID, id));
				this.reservations = this.reservations.filter((_) => _.id !== id);
				this.errors = {};
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
