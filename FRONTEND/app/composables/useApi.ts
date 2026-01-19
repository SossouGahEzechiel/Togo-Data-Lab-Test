interface ApiResponse<T> {
	data: T;
}

interface ApiConfig {
	method?: 'GET' | 'POST' | 'PUT' | 'DELETE' | 'PATCH';
	headers?: HeadersInit;
	body?: any;
	signal?: AbortController['signal'];
}

export const useApi = () => {
	const authStore = useAuthStore();
	const baseUrl = import.meta.env.VITE_API_URL || 'http://localhost:8000/api';

	const getHeaders = (headers: HeadersInit = {}, isFormData: boolean = false) => {
		// Récupérer le token XSRF à chaque requête
		const XSRFToken = useCookie('XSRF-TOKEN').value;

		const defaultHeaders: any = {
			'Accept': 'application/json',
			...(authStore.token && { Authorization: `Bearer ${authStore.token}` }),
			'ngrok-skip-browser-warning': 'true'
		};

		// Ajouter le token XSRF s'il existe
		if (XSRFToken) {
			defaultHeaders['X-XSRF-TOKEN'] = XSRFToken;
		}

		// N'ajouter Content-Type que si ce n'est pas FormData
		if (!isFormData) {
			defaultHeaders['Content-Type'] = 'application/json';
		}

		return {
			...defaultHeaders,
			...headers,
		};
	};

	const request = async <T>(endpoint: string, config: ApiConfig = {}): Promise<ApiResponse<T>> => {
		const { method = 'GET', headers = {}, body, signal } = config;
		const fullUrl = `${baseUrl}${endpoint}`;
		const isFormData = body instanceof FormData;

		try {
			const response = await fetch(fullUrl, {
				method,
				// @ts-ignore
				headers: getHeaders(headers, isFormData),
				body: isFormData ? body : (body ? JSON.stringify(body) : undefined),
				credentials: 'include',
				signal,
			});

			if (response.status === 419) {
				await refreshToken();
				return request<T>(endpoint, config);
			}

			if (response.status === 401 && endpoint !== ApiUrl.LOGOUT) {
				authStore.logout();
			}

			const data = await response.json();

			if (!response.ok) {
				// Créer une erreur avec les détails du backend
				const error = new Error(data.error || `HTTP ${response.status}`);
				// Attacher les données complètes à l'erreur
				(error as any).status = response.status;
				(error as any).data = data;
				(error as any).response = data; // Pour compatibilité
				throw error;
			}

			return { data: data.data };

		} catch (error) {
			console.log("Api error:", error);
			throw error;
		}
	};

	const get = <T>(endpoint: string, config: ApiConfig = {}) =>
		request<T>(endpoint, { method: 'GET', ...config });

	const post = <T>(endpoint: string, body: any, config: ApiConfig = {}) =>
		request<T>(endpoint, { method: 'POST', body, ...config });

	const put = <T>(endpoint: string, body: any, config: ApiConfig = {}) =>
		request<T>(endpoint, { method: 'PUT', body, ...config });

	const del = <T>(endpoint: string, config: ApiConfig = {}) =>
		request<T>(endpoint, { method: 'DELETE', ...config });

	const patch = <T>(endpoint: string, body: any, config: ApiConfig = {}) =>
		request<T>(endpoint, { method: 'PATCH', body, ...config });

	return {
		request,
		get,
		post,
		put,
		del,
		patch,
		getHeaders,
	};
};

const refreshToken = async () => {
	console.log("Refreshing CSRF token...");
	try {
		await $fetch(`${import.meta.env.VITE_API_URL_BASE || 'http://localhost:8000'}/sanctum/csrf-cookie`, {
			method: 'GET',
			credentials: 'include',
			mode: 'cors',
			headers: {
				'Accept': 'application/json',
				'Content-Type': 'application/json',
			}
		});
		console.log("CSRF token refreshed successfully");
	} catch (error) {
		console.error("Error refreshing CSRF token:", error);
		throw error;
	}
}
