export enum ReservationStatusEnum	{
	PENDING = "En attente",
	VALIDATED = "Validée",
	REJECTED = "Rejetée",
	PASSED = "Passée",
}

export const reservationStatuses = Object.values(ReservationStatusEnum);
