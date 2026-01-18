import { defineStore } from 'pinia'
import { type Vehicle, VehicleStatus, VehicleType } from '~/types'
import { api } from '~/composables/api'

interface VehicleState {
  vehicles: Vehicle[]
  currentVehicle: Vehicle | null
  isLoading: boolean
  filters: {
    type?: VehicleType
    status?: VehicleStatus
    search?: string
  }
}

export const useVehicleStore = defineStore('vehicle', {
  state: (): VehicleState => ({
    vehicles: [],
    currentVehicle: null,
    isLoading: false,
    filters: {}
  }),

  actions: {
    async fetchVehicles() {
      this.isLoading = true
      try {
        const response = await api.getVehicles()
        if (response.data) {
          this.vehicles = response.data
        }
      } catch (error) {
        console.error('Failed to fetch vehicles:', error)
        throw error
      } finally {
        this.isLoading = false
      }
    },

    async fetchVehicle(id: string) {
      this.isLoading = true
      try {
        const response = await api.getVehicle(id)
        if (response.data) {
          this.currentVehicle = response.data
        }
      } catch (error) {
        console.error('Failed to fetch vehicle:', error)
        throw error
      } finally {
        this.isLoading = false
      }
    },

    async createVehicle(formData: FormData) {
      try {
        const response = await api.createVehicle(formData)
        if (response.data) {
          this.vehicles.unshift(response.data)
          return { success: true, data: response.data }
        }
      } catch (error: any) {
        return {
          success: false,
          error: error.errors || error.message
        }
      }
    },

    async updateVehicle(id: string, data: Partial<Vehicle>) {
      try {
        const response = await api.updateVehicle(id, data)
        if (response.data) {
          const index = this.vehicles.findIndex(v => v.id === id)
          if (index !== -1) {
            this.vehicles[index] = response.data
          }
          if (this.currentVehicle?.id === id) {
            this.currentVehicle = response.data
          }
          return { success: true, data: response.data }
        }
      } catch (error: any) {
        return {
          success: false,
          error: error.errors || error.message
        }
      }
    },

    async deleteVehicle(id: string) {
      try {
        await api.deleteVehicle(id)
        this.vehicles = this.vehicles.filter(v => v.id !== id)
        if (this.currentVehicle?.id === id) {
          this.currentVehicle = null
        }
        return { success: true }
      } catch (error: any) {
        return {
          success: false,
          error: error.message || 'Failed to delete vehicle'
        }
      }
    },

    setFilters(filters: Partial<VehicleState['filters']>) {
      this.filters = { ...this.filters, ...filters }
    },

    clearFilters() {
      this.filters = {}
    }
  },

  getters: {
    filteredVehicles(): Vehicle[] {
      return this.vehicles.filter(vehicle => {
        if (this.filters.type && vehicle.type !== this.filters.type) {
          return false
        }
        if (this.filters.status && vehicle.status !== this.filters.status) {
          return false
        }
        if (this.filters.search) {
          const search = this.filters.search.toLowerCase()
          return (
            vehicle.brand.toLowerCase().includes(search) ||
            vehicle.model.toLowerCase().includes(search) ||
            vehicle.registrationNumber.toLowerCase().includes(search)
          )
        }
        return true
      })
    },

    availableVehicles(): Vehicle[] {
      return this.vehicles.filter(v => v.status === VehicleStatus.AVAILABLE)
    },

    vehicleTypes(): { value: VehicleType; label: string }[] {
      return [
        { value: VehicleType.CAR, label: 'Voiture' },
        { value: VehicleType.MOTORCYCLE, label: 'Moto' },
        { value: VehicleType.TRUCK, label: 'Camion' },
        { value: VehicleType.VAN, label: 'Fourgon' },
        { value: VehicleType.BUS, label: 'Bus' }
      ]
    },

    vehicleStatuses(): { value: VehicleStatus; label: string; color: string }[] {
      return [
        { value: VehicleStatus.AVAILABLE, label: 'Disponible', color: 'green' },
        { value: VehicleStatus.RESERVED, label: 'Réservé', color: 'blue' },
        { value: VehicleStatus.SUSPENDED, label: 'Suspendu', color: 'yellow' },
        { value: VehicleStatus.UNDER_REPAIR, label: 'En réparation', color: 'orange' },
        { value: VehicleStatus.UNAVAILABLE, label: 'Indisponible', color: 'red' }
      ]
    }
  }
})
