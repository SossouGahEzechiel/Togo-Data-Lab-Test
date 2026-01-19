/**
 * Centralise les URLs des endpoints API pour :
 * - Éviter la duplication des URLs dans le code
 * - Faciliter la maintenance et les modifications des endpoints
 * - Réduire les risques d'erreurs de frappe
 * - Maintenir une cohérence dans l'utilisation des endpoints
 */
export const ApiUrl = {
  /**
   * Endpoint pour l'authentification
   * Méthode: POST
   * Corps: { email: string, password: string }
   * Retour: { token: string, user: User }
   */
  LOGIN: '/auth/login',

	/**
	 * Endpoint pour l'inscription
	 * Méthode: POST
	 * Corps: { email: string, password: string }
	 * Retour: { user: User }
	 */
	REGISTER: '/register',

	/**
	 * Endpoint pour la déconnexion
	 * Méthode: POST
	 * Corps: Vide
	 * Retour: Vide
	 */
	LOGOUT: '/auth/logout',

	FORGOT_PASSWORD: "/auth/forgot-password",
	RESET_PASSWORD: "/auth/reset-password",
	CONFIGURE_PASSWORD: "/auth/configure-password",

	VEHICLES: "/vehicles",
	VEHICLE_BY_ID: "/vehicles/:id",

	MISSIONS: "/missions",
	MISSION_BY_ID: "/missions/:id",


	/**
 * Fonction pour les url à paramètres sous la forme url/:id
 */
	parameterized: (item: string, parameters: Record<string, string | number> | string | number) => {
		// Si on passe directement un string ou un number → c'est l'id par défaut
		if (typeof parameters === "string" || typeof parameters === "number") {
			return item.replace(":id", parameters.toString());
		}

		// Sinon on parcourt l'objet
		Object.entries(parameters).forEach(([key, value]) => {
			item = item.replace(`:${key}`, value.toString());
		});

		return item;
	},

	/**
 * Fonction pour les query strings sous la forme url?key=value
 */
	queryable: (item: string, queries: Record<string, string | number> | string | number) => {
		// Si on passe directement un string ou un number → c'est le paramètre 'id' par défaut
		if (typeof queries === "string" || typeof queries === "number") {
			return `${item}?q=${encodeURIComponent(queries.toString())}`;
		}

		// Sinon on construit les query strings à partir de l'objet
		const queryString = Object.entries(queries)
			.map(([key, value]) => `${encodeURIComponent(key)}=${encodeURIComponent(value.toString())}`)
			.join('&');

		return queryString ? `${item}?${queryString}` : item;
	},
} as const;
