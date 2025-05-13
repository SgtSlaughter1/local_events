import { defineStore } from 'pinia'
import api from '@/services/api'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: JSON.parse(localStorage.getItem('user')) || null,
    token: localStorage.getItem('token') || null,
    loading: false,
    error: null,
  }),

  getters: {
    isAuthenticated: (state) => !!state.token,
    getUser: (state) => state.user,
    getError: (state) => state.error,
    isLoading: (state) => state.loading,
    isAdmin: (state) => state.user?.user_type_id === 1,
    isOrganizer: (state) => state.user?.user_type_id === 2,
    isAttendee: (state) => state.user?.user_type_id === 3,
    isVendor: (state) => state.user?.user_type_id === 4,
  },

  actions: {
    async login(credentials) {
      this.loading = true
      this.error = null
      try {
        const response = await api.post('/api/login', credentials)
        
        // Check if we have the expected response structure
        if (!response.data || !response.data.token) {
          throw new Error('Invalid response from server')
        }

        // Set token and user data
        this.token = response.data.token
        this.user = response.data.user

        // Store in localStorage
        localStorage.setItem('token', this.token)
        localStorage.setItem('user', JSON.stringify(this.user))

        // Set the token in axios defaults for future requests
        api.defaults.headers.common['Authorization'] = `Bearer ${this.token}`

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
        // Clear state
        this.token = null
        this.user = null
        // Clear localStorage
        localStorage.removeItem('token')
        localStorage.removeItem('user')
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
        // Update localStorage
        localStorage.setItem('user', JSON.stringify(this.user))
        return { success: true, data: response.data }
      } catch (error) {
        this.error = error.response?.data?.message || 'An error occurred while fetching user data'
        return { success: false, error: this.error }
      } finally {
        this.loading = false
      }
    },

    // Helper method to check if user has required role
    hasRole(role) {
      const roleMap = {
        admin: 1,
        organizer: 2,
        attendee: 3,
      }
      return this.user?.user_type_id === roleMap[role]
    },

    // Helper method to check if user has any of the required roles
    hasAnyRole(roles) {
      return roles.some((role) => this.hasRole(role))
    },

    async updateProfile(profileData) {
      this.loading = true
      this.error = null
      try {
        const response = await api.put('/api/user/profile', profileData)
        if (response.data && response.data.user) {
          this.user = response.data.user
          // Update localStorage
          localStorage.setItem('user', JSON.stringify(this.user))
          return { success: true, data: response.data.user }
        }
        return { success: false, error: 'Invalid response format' }
      } catch (error) {
        this.error = error.response?.data?.message || 'An error occurred while updating profile'
        return { success: false, error }
      } finally {
        this.loading = false
      }
    },

    async updatePassword(passwordData) {
      this.loading = true
      this.error = null
      try {
        const response = await api.put('/api/user/password', passwordData)
        return { success: true, data: response.data }
      } catch (error) {
        this.error = error.response?.data?.message || 'An error occurred while updating password'
        return { success: false, error }
      } finally {
        this.loading = false
      }
    },

    async googleAuth(googleData) {
      this.loading = true
      this.error = null
      try {
        const response = await api.post('/api/google-auth', googleData)
        
        if (!response.data || !response.data.token) {
          throw new Error('Invalid response from server')
        }

        // Set token and user data
        this.token = response.data.token
        this.user = response.data.user

        // Store in localStorage
        localStorage.setItem('token', this.token)
        localStorage.setItem('user', JSON.stringify(this.user))

        // Set the token in axios defaults for future requests
        api.defaults.headers.common['Authorization'] = `Bearer ${this.token}`

        return { success: true, data: response.data }
      } catch (error) {
        this.error = error.response?.data?.message || 'An error occurred during Google authentication'
        return { success: false, error: this.error }
      } finally {
        this.loading = false
      }
    }
  },
})
