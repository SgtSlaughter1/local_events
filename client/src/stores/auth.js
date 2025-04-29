import { defineStore } from 'pinia'
import api from '@/services/api'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    token: localStorage.getItem('token') || null,
    loading: false,
    error: null
  }),

  getters: {
    isAuthenticated: (state) => !!state.token,
    getUser: (state) => state.user,
    getError: (state) => state.error,
    isLoading: (state) => state.loading
  },

  actions: {
    async login(credentials) {
      this.loading = true
      this.error = null
      try {
        const response = await api.post('/api/login', credentials)
        this.token = response.data.token
        this.user = response.data.user
        localStorage.setItem('token', this.token)
        return { success: true, data: response.data }
      } catch (error) {
        this.error = error.response?.data?.message || 'An error occurred during login'
        return { success: false, error: this.error }
      } finally {
        this.loading = false
      }
    },

    async register(userData) {
      this.loading = true
      this.error = null
      try {
        const response = await api.post('/api/register', userData)
        return { success: true, data: response.data }
      } catch (error) {
        this.error = error.response?.data?.message || 'An error occurred during registration'
        return { success: false, error: this.error }
      } finally {
        this.loading = false
      }
    },

    async logout() {
      this.loading = true
      this.error = null
      try {
        await api.post('/api/logout')
        this.token = null
        this.user = null
        localStorage.removeItem('token')
        return { success: true }
      } catch (error) {
        this.error = error.response?.data?.message || 'An error occurred during logout'
        return { success: false, error: this.error }
      } finally {
        this.loading = false
      }
    },

    async fetchUser() {
      this.loading = true
      this.error = null
      try {
        const response = await api.get('/api/user')
        this.user = response.data
        return { success: true, data: response.data }
      } catch (error) {
        this.error = error.response?.data?.message || 'An error occurred while fetching user data'
        return { success: false, error: this.error }
      } finally {
        this.loading = false
      }
    }
  }
})
