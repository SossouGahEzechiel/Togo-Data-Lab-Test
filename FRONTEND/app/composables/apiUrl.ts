export const ApiUrl = {
	LOGIN: "/auth/login",
	REGISTER: "/register",
	LOGOUT: "/auth/logout",

	FORGOT_PASSWORD: "/auth/forgot-password",
	RESET_PASSWORD: "/auth/reset-password",
	CONFIGURE_PASSWORD: "/auth/configure-password",

	VEHICLES: "/vehicles",
	VEHICLE_BY_ID: "/vehicles/:id",
	VEHICLE_CHECK_AVAILABILITY: "/vehicles/:id/check-availability",

	MISSIONS: "/missions",
	MISSION_BY_ID: "/missions/:id",

	RESERVATIONS: "/reservations",
	RESERVATION_BY_ID: "/reservations/:id",
	RESERVATION_DECISION: "/reservations/:id/apply-decision",

	USERS: "/users",
	USER_BY_ID: "/users/:id",

	parameterized: (
		item: string,
		parameters: Record<string, string | number> | string | number,
	) => {
		if (typeof parameters === "string" || typeof parameters === "number") {
			return item.replace(":id", parameters.toString());
		}

		Object.entries(parameters).forEach(([key, value]) => {
			item = item.replace(`:${key}`, value.toString());
		});

		return item;
	},

	queryable: (
		item: string,
		queries: Record<string, string | number> | string | number,
	) => {
		if (typeof queries === "string" || typeof queries === "number") {
			return `${item}?q=${encodeURIComponent(queries.toString())}`;
		}
		const queryString = Object.entries(queries)
			.map(
				([key, value]) =>
					`${encodeURIComponent(key)}=${encodeURIComponent(value.toString())}`,
			)
			.join("&");

		return queryString ? `${item}?${queryString}` : item;
	},
} as const;
