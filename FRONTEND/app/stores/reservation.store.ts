import { defineStore } from "pinia";
import { ReservationStatus, VehicleStatus, type Reservation } from "~/types";
import { api } from "~/composables/api";

interface ReservationState {
	reservations: Reservation[];
	currentReservation: Reservation | null;
	isLoading: boolean;
	filters: {
		status?: ReservationStatus;
		userId?: string;
		vehicleId?: string;
	};
}

export const useReservationStore = defineStore("reservation", {
	state: (): ReservationState => ({
		reservations: [],
		currentReservation: null,
		isLoading: false,
		filters: {},
	}),

	actions: {
		async fetchReservations() {
			this.isLoading = true;
			try {
				const response = await api.getReservations();
				if (response.data) {
					this.reservations = response.data;
				}
			} catch (error) {
				console.error("Failed to fetch reservations:", error);
				throw error;
			} finally {
				this.isLoading = false;
			}
		},

		async fetchReservation(id: string) {
			this.isLoading = true;
			try {
				const response = await api.getReservation(id);
				if (response.data) {
					this.currentReservation = response.data;
				}
			} catch (error) {
				console.error("Failed to fetch reservation:", error);
				throw error;
			} finally {
				this.isLoading = false;
			}
		},

		async createReservation(data: any) {
			try {
				const response = await api.createReservation(data);
				if (response.data) {
					this.reservations.unshift(response.data);
					return { success: true, data: response.data };
				}
			} catch (error: any) {
				return {
					success: false,
					error: error.errors || error.message,
				};
			}
		},

		async assignDriver(reservationId: string, driverId: string) {
			try {
				const response = await api.assignDriver(reservationId, driverId);
				if (response.data) {
					const index = this.reservations.findIndex(
						(r) => r.id === reservationId,
					);
					if (index !== -1) {
						this.reservations[index] = response.data;
					}
					if (this.currentReservation?.id === reservationId) {
						this.currentReservation = response.data;
					}
					return { success: true, data: response.data };
				}
			} catch (error: any) {
				return {
					success: false,
					error: error.message || "Failed to assign driver",
				};
			}
		},

		async applyDecision(
			reservationId: string,
			status: ReservationStatus.VALIDATED | ReservationStatus.REJECTED,
		) {
			try {
				const response = await api.applyDecision(reservationId, status);
				if (response.data) {
					const index = this.reservations.findIndex(
						(r) => r.id === reservationId,
					);
					if (index !== -1) {
						this.reservations[index] = response.data;
					}
					if (this.currentReservation?.id === reservationId) {
						this.currentReservation = response.data;
					}
					return { success: true, data: response.data };
				}
			} catch (error: any) {
				return {
					success: false,
					error: error.message || "Failed to apply decision",
				};
			}
		},

		async markAsPassed(
			reservationId: string,
			vehicleStatus: VehicleStatus,
			reason?: string,
		) {
			try {
				const response = await api.markAsPassed(
					reservationId,
					vehicleStatus,
					reason,
				);
				if (response.data) {
					const index = this.reservations.findIndex(
						(r) => r.id === reservationId,
					);
					if (index !== -1) {
						this.reservations[index] = response.data;
					}
					if (this.currentReservation?.id === reservationId) {
						this.currentReservation = response.data;
					}
					return { success: true, data: response.data };
				}
			} catch (error: any) {
				return {
					success: false,
					error: error.message || "Failed to mark as passed",
				};
			}
		},

		setFilters(filters: Partial<ReservationState["filters"]>) {
			this.filters = { ...this.filters, ...filters };
		},

		clearFilters() {
			this.filters = {};
		},
	},

	getters: {
		filteredReservations(): Reservation[] {
			return this.reservations.filter((reservation) => {
				if (this.filters.status && reservation.status !== this.filters.status) {
					return false;
				}
				if (this.filters.userId && reservation.userId !== this.filters.userId) {
					return false;
				}
				if (
					this.filters.vehicleId &&
					reservation.vehicleId !== this.filters.vehicleId
				) {
					return false;
				}
				return true;
			});
		},

		pendingReservations(): Reservation[] {
			return this.reservations.filter(
				(r) => r.status === ReservationStatus.PENDING,
			);
		},

		myReservations(): Reservation[] {
			const authStore = useAuthStore();
			return this.reservations.filter((r) => r.userId === authStore.user?.id);
		},

		reservationStatuses(): {
			value: ReservationStatus;
			label: string;
			color: string;
		}[] {
			return [
				{
					value: ReservationStatus.PENDING,
					label: "En attente",
					color: "yellow",
				},
				{
					value: ReservationStatus.VALIDATED,
					label: "Validée",
					color: "green",
				},
				{ value: ReservationStatus.REJECTED, label: "Rejetée", color: "red" },
				{ value: ReservationStatus.PASSED, label: "Terminée", color: "gray" },
			];
		},
	},
});
