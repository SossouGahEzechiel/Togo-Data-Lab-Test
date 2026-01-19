export interface Mission {
	id: string;
	label: string;
	description: string;
	from: string;
	to: string | null;
}

export const initMissionForm = (mission?: Mission): Mission => ({
	id: mission?.id || "",
	label: mission?.label || "",
	description: mission?.description || "",
	from: mission?.from || "",
	to: mission?.to || "",
});
