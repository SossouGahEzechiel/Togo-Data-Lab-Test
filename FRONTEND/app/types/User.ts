import type { Image } from "./Image";
import type { UserRoleEnum } from "./UserRoleEnum";

export interface User {
	id: string;
	firstName: string;
	lastName: string;
	fullName: string;
	email: string;
	role: UserRoleEnum;
	phone: string;
	isActive: boolean;
	hasConfiguredPassword: boolean;
	image: Image;
	token: string;
}

export interface LoginCredential {
	email: string;
	password: string;
}

export interface ConfigurePasswordCredential {
	password: string;
	password_confirmation: string;
}

export const initLoginCredential = (): LoginCredential => ({
	email: "",
	password: "",
});

export const initPasswordConfiguration = (): ConfigurePasswordCredential => ({
	password: "",
	password_confirmation: "",
});
