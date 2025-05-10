import { defineStore } from 'pinia'
import api from '@/services/api'

export const useUserStore = defineStore('user', {
  state: () => ({
    users: [],
    loading: false,
    error: null,
    pagination: {
      current_page: 1,
      total: 0,
      per_page: 15
    }
  }),

  getters: {
    getUsers: (state) => state.users,
    isLoading: (state) => state.loading,
    getError: (state) => state.error,
    getPagination: (state) => state.pagination
  },

  actions: {
    async fetchUsers({ page = 1, search = '', role = '' } = {}) {
      this.loading = true
      this.error = null
      try {
        // Build query parameters
        const params = new URLSearchParams({
          page: page.toString()
        })
        
        if (search) {
          params.append('search', search)
        }
        
        if (role) {
          params.append('role', role)
        }

        const response = await api.get(`/api/admin/users?${params.toString()}`)
        this.users = response.data.users.data
        this.pagination = {
          current_page: response.data.users.current_page,
          total: response.data.users.total,
          per_page: response.data.users.per_page
        }
        return { success: true, data: response.data }
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to fetch users'
        return { success: false, error: this.error }
      } finally {
        this.loading = false
      }
    }
  }
}) 