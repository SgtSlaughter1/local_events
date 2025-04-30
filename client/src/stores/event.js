import { defineStore } from 'pinia'
import api from '../services/api'

export const useEventStore = defineStore('event', {
  state: () => ({
    events: [],
    loading: false,
    error: null,
    currentEvent: null,
  }),

  getters: {
    getEvents: (state) => state.events,
    getEventById: (state) => (id) => state.events.find((event) => event.id === id),
    isLoading: (state) => state.loading,
    getError: (state) => state.error,
  },

  actions: {
    // Fetch all events
    async fetchEvents() {
      this.loading = true
      this.error = null
      try {
        const response = await api.get('/api/events')
        this.events = response.data.events.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Error fetching events'
        throw error
      } finally {
        this.loading = false
      }
    },

    // Fetch a single event
    async fetchEvent(id) {
      this.loading = true
      this.error = null
      try {
        const response = await api.get(`/api/events/${id}`)
        this.currentEvent = response.data.data
        return this.currentEvent
      } catch (error) {
        this.error = error.response?.data?.message || 'Error fetching event'
        throw error
      } finally {
        this.loading = false
      }
    },

    // Create a new event
    async createEvent(eventData) {
      this.loading = true
      this.error = null
      try {
        const response = await api.post('/api/events', eventData)
        this.events.push(response.data.data)
        return response.data.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Error creating event'
        throw error
      } finally {
        this.loading = false
      }
    },

    // Update an event
    async updateEvent(id, eventData) {
      this.loading = true
      this.error = null
      try {
        const response = await api.put(`/api/events/${id}`, eventData)
        const index = this.events.findIndex((event) => event.id === id)
        if (index !== -1) {
          this.events[index] = response.data.data
        }
        return response.data.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Error updating event'
        throw error
      } finally {
        this.loading = false
      }
    },

    // Delete an event
    async deleteEvent(id) {
      this.loading = true
      this.error = null
      try {
        await api.delete(`/api/events/${id}`)
        this.events = this.events.filter((event) => event.id !== id)
      } catch (error) {
        this.error = error.response?.data?.message || 'Error deleting event'
        throw error
      } finally {
        this.loading = false
      }
    },
  },
})
