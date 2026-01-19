// Statut du véhicule
export enum VehicleStatusEnum {
	AVAILABLE = "Disponible",
	UNAVAILABLE = "Indisponible",
	UNDER_REPAIR = "En réparation",
	RESERVED = "Réservé",
	SUSPENDED = "Suspendu",
}

// Type de véhicule
export enum VehicleTypeEnum {
	SUV = "Voiture",
	MOTOR_CYCLE = "Moto",
	PICKUP = "Pick up",
	MINIBUS = "Mini bus",
	BUS = "Bus",
}

// Helpers pour obtenir toutes les valeurs
export const vehicleStatuses = Object.values(VehicleStatusEnum);
export const vehicleTypes = Object.values(VehicleTypeEnum);

// Helpers pour obtenir les clés
export const vehicleStatusKeys = Object.keys(VehicleStatusEnum).filter((key) =>
	isNaN(Number(key)),
);
export const vehicleTypeKeys = Object.keys(VehicleTypeEnum).filter((key) =>
	isNaN(Number(key)),
);

// Type guards
export const isVehicleStatus = (value: string): value is VehicleStatusEnum => {
	return vehicleStatuses.includes(value as VehicleStatusEnum);
};

export const isVehicleType = (value: string): value is VehicleTypeEnum => {
	return vehicleTypes.includes(value as VehicleTypeEnum);
};
