import { defineStore } from 'pinia'
import api from '@/services/api'

export const useRegistrationStore = defineStore('registration', {
  state: () => ({
    registrations: [],
    currentRegistration: null,
    loading: false,
    error: null,
    availableTickets: null
  }),

  getters: {
    getUserRegistrations: (state) => state.registrations,
    getCurrentRegistration: (state) => state.currentRegistration,
    isLoading: (state) => state.loading,
    getError: (state) => state.error,
    getAvailableTickets: (state) => state.availableTickets
  },

  actions: {
    // Get available tickets for an event
    async fetchAvailableTickets(eventId) {
      this.loading = true
      this.error = null
      try {
        const response = await api.get(`/events/${eventId}/tickets`)
        this.availableTickets = response.data.data
        return response.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to fetch available tickets'
        throw error
      } finally {
        this.loading = false
      }
    },

    // Register for an event
    async registerForEvent(eventId, registrationData) {
      this.loading = true
      this.error = null
      try {
        const response = await api.post(`/events/${eventId}/register`, registrationData)
        this.currentRegistration = response.data.data
        return response.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Registration failed'
        throw error
      } finally {
        this.loading = false
      }
    },

    // Get user's registrations
    async fetchUserRegistrations() {
      this.loading = true
      this.error = null
      try {
        const response = await api.get('/user/registrations')
        this.registrations = response.data.data
        return response.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to fetch registrations'
        throw error
      } finally {
        this.loading = false
      }
    },

    // Get specific registration details
    async fetchRegistrationDetails(registrationId) {
      this.loading = true
      this.error = null
      try {
        const response = await api.get(`/registrations/${registrationId}`)
        this.currentRegistration = response.data.data
        return response.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to fetch registration details'
        throw error
      } finally {
        this.loading = false
      }
    },

    // Cancel registration
    async cancelRegistration(registrationId, reason) {
      this.loading = true
      this.error = null
      try {
        const response = await api.post(`/registrations/${registrationId}/cancel`, { reason })
        // Update the registration in the list
        const index = this.registrations.findIndex(r => r.id === registrationId)
        if (index !== -1) {
          this.registrations[index] = response.data.data
        }
        return response.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to cancel registration'
        throw error
      } finally {
        this.loading = false
      }
    },

    // Process payment
    async processPayment(registrationId, paymentData) {
      this.loading = true
      this.error = null
      try {
        const response = await api.post(`/registrations/${registrationId}/payment`, paymentData)
        // Update the registration in the list
        const index = this.registrations.findIndex(r => r.id === registrationId)
        if (index !== -1) {
          this.registrations[index] = response.data.data
        }
        return response.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Payment processing failed'
        throw error
      } finally {
        this.loading = false
      }
    },

    // Clear current registration
    clearCurrentRegistration() {
      this.currentRegistration = null
    },

    // Clear error
    clearError() {
      this.error = null
    }
  }
}) 