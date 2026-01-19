import type { Mission } from "./Mission";
import type { ReservationStatusEnum } from "./ReservationEnum";
import type { User } from "./User";
import type { Vehicle } from "./Vehicle";

export interface Reservation {
	id: string;
	from: string;
	to: string;
	missionId: string;
	vehicleId: string;
	driverId: string;
	userId: string;
	status: ReservationStatusEnum;
	returnDate: string;
	mission: Mission;
	vehicle: Vehicle;
	driver: User;
	user: User;
}
