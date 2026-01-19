import type { Image } from "./Image";

export interface User {
	id: string;
	firstName: string;
	lastName: string;
	email: string;
	role: string;
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
	password: ""
});

export const initPasswordConfiguration = (): ConfigurePasswordCredential => ({
	password: "",
	password_confirmation: ""
});
