import type { Image } from "./Image";
import { VehicleStatusEnum, VehicleTypeEnum } from "./VehicleEnums";

export interface Vehicle {
	id: string;
	brand: string;
	model: string;
	type: VehicleTypeEnum;
	registrationNumber: string;
	registrationDate: string;
	seatsCount: number;
	status: VehicleStatusEnum;
	reason: string | null;
	images: Image[];
}

// Fonction helper pour créer un véhicule vide
export const initVehicleForm = (vehicle?: Vehicle): Vehicle => ({
	id: vehicle?.id || "",
	brand: vehicle?.brand || "",
	model: vehicle?.model || "",
	type: vehicle?.type || VehicleTypeEnum.SUV,
	registrationNumber: vehicle?.registrationNumber || "",
	registrationDate: vehicle?.registrationDate || "",
	seatsCount: vehicle?.seatsCount || 1,
	status: vehicle?.status || VehicleStatusEnum.AVAILABLE,
	reason: vehicle?.reason || null,
	images: vehicle?.images || [],
});
