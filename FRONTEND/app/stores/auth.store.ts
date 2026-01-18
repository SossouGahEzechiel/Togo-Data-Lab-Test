import { defineStore } from 'pinia'
import type { User } from '~/types'
import { api } from '~/composables/api'

interface AuthState {
  user: User | null
  token: string | null
  isAuthenticated: boolean
}

export const useAuthStore = defineStore('auth', {
  state: (): AuthState => ({
    user: null,
    token: null,
    isAuthenticated: false
  }),

  actions: {
    async login(credentials: { email: string; password: string }) {
      try {
        const response = await api.login(credentials)
        if (response.data) {
          this.user = response.data
          this.token = response.data.token
          this.isAuthenticated = true

          // Save to localStorage
          localStorage.setItem('token', this.token!)
          localStorage.setItem('user', JSON.stringify(this.user))

          return { success: true }
        }
      } catch (error: any) {
        return {
          success: false,
          error: error.message || 'Échec de la connexion'
        }
      }
    },

    async logout() {
      try {
        await api.logout()
      } catch (error) {
        console.error('Logout error:', error)
      } finally {
        this.$reset()
        localStorage.removeItem('token')
        localStorage.removeItem('user')
        navigateTo('/login')
      }
    },

    initialize() {
      const token = localStorage.getItem('token')
      const userStr = localStorage.getItem('user')

      if (token && userStr) {
        this.token = token
        this.user = JSON.parse(userStr)
        this.isAuthenticated = true
      }
    }
  },

  getters: {
    fullName: (state) =>
      state.user ? `${state.user.firstName} ${state.user.lastName}` : '',

    isAdmin: (state) => state.user?.role === 'admin',

    isModerator: (state) => state.user?.role === 'moderator',

    canManageVehicles: (state) =>
      ['admin', 'moderator'].includes(state.user?.role || ''),

    canManageReservations: (state) =>
      ['admin', 'moderator'].includes(state.user?.role || '')
  }
})
