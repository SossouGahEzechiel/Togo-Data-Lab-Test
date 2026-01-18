import type { ApiResponse, CreateReservationData, Mission, Reservation, ReservationStatus, Vehicle, VehicleStatus } from "~/types";

class ApiService {
  private getBaseUrl(): string {
    return useRuntimeConfig().public.apiBaseUrl;
  }

  private async request<T>(
    endpoint: string,
    options: RequestInit = {},
  ): Promise<ApiResponse<T>> {
    const token = useAuthStore().token;
    const headers: HeadersInit = {
      "Content-Type": "application/json",
      Accept: "application/json",
      ...options.headers,
    };

    if (token) {
      //@ts-ignore
      headers["Authorization"] = `Bearer ${token}`;
    }

    const url = `${this.getBaseUrl()}${endpoint}`;

    try {
      const response = await fetch(url, {
        ...options,
        headers,
      });

      const data = await response.json();

      if (!response.ok) {
        if (response.status === 401) {
          useAuthStore().logout();
          navigateTo("/login");
        }
        throw data;
      }

      return data;
    } catch (error) {
      console.error("API Error:", error);
      throw error;
    }
  }

  // Auth
  async login(credentials: { email: string; password: string }) {
    return this.request<{ token: string }>("/login", {
      method: "POST",
      body: JSON.stringify(credentials),
    });
  }

  async logout() {
    return this.request("/logout", {
      method: "POST",
    });
  }

  // Vehicles
  async getVehicles() {
    return this.request<Vehicle[]>("/vehicles");
  }

  async getVehicle(id: string) {
    return this.request<Vehicle>(`/vehicles/${id}`);
  }

  async createVehicle(data: FormData) {
    return this.request<Vehicle>("/vehicles", {
      method: "POST",
      body: data,
      headers: {}, // Let browser set Content-Type for FormData
    });
  }

  async updateVehicle(id: string, data: Partial<Vehicle>) {
    return this.request<Vehicle>(`/vehicles/${id}`, {
      method: "PUT",
      body: JSON.stringify(data),
    });
  }

  async deleteVehicle(id: string) {
    return this.request(`/vehicles/${id}`, {
      method: "DELETE",
    });
  }

  // Missions
  async getMissions() {
    return this.request<Mission[]>("/missions");
  }

  async getMission(id: string) {
    return this.request<Mission>(`/missions/${id}`);
  }

  async createMission(data: Partial<Mission>) {
    return this.request<Mission>("/missions", {
      method: "POST",
      body: JSON.stringify(data),
    });
  }

  // Reservations
  async getReservations() {
    return this.request<Reservation[]>("/reservations");
  }

  async getReservation(id: string) {
    return this.request<Reservation>(`/reservations/${id}`);
  }

  async createReservation(data: CreateReservationData) {
    return this.request<Reservation>("/reservations", {
      method: "POST",
      body: JSON.stringify(data),
    });
  }

  async assignDriver(reservationId: string, driverId: string) {
    return this.request<Reservation>(
      `/reservations/${reservationId}/assign-driver`,
      {
        method: "PUT",
        body: JSON.stringify({ driverId }),
      },
    );
  }

  async applyDecision(
    reservationId: string,
    status: ReservationStatus.VALIDATED | ReservationStatus.REJECTED,
  ) {
    return this.request<Reservation>(
      `/reservations/${reservationId}/apply-decision`,
      {
        method: "PUT",
        body: JSON.stringify({ status }),
      },
    );
  }

  async markAsPassed(
    reservationId: string,
    vehicleStatus: VehicleStatus,
    reason?: string,
  ) {
    return this.request<Reservation>(
      `/reservations/${reservationId}/mark-as-passed`,
      {
        method: "PUT",
        body: JSON.stringify({ vehicleStatus, vehicleStatusReason: reason }),
      },
    );
  }
}

export const api = new ApiService();
