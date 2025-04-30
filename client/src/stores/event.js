import { defineStore } from 'pinia'
import api from '../services/api'

export const useEventStore = defineStore('event', {
  state: () => ({
    events: [],
    categories: [],
    loading: false,
    error: null,
    currentEvent: null,
    filters: {
      price: [],
      date: [],
      customDate: '',
      categories: [],
      format: [],
    },
  }),

  getters: {
    getEvents: (state) => {
      let filteredEvents = [...state.events]

      // Price filter
      if (state.filters.price.length > 0) {
        filteredEvents = filteredEvents.filter((event) => {
          if (state.filters.price.includes('free')) return event.price === 0
          if (state.filters.price.includes('paid')) return event.price > 0
          return true
        })
      }

      // Date filter
      if (state.filters.date.length > 0) {
        const today = new Date()
        const tomorrow = new Date(today)
        tomorrow.setDate(tomorrow.getDate() + 1)

        const thisWeekEnd = new Date(today)
        thisWeekEnd.setDate(today.getDate() + (7 - today.getDay()))

        const weekendStart = new Date(today)
        weekendStart.setDate(today.getDate() + (6 - today.getDay()))
        const weekendEnd = new Date(today)
        weekendEnd.setDate(today.getDate() + (7 - today.getDay()))

        filteredEvents = filteredEvents.filter((event) => {
          const eventDate = new Date(event.date)

          return state.filters.date.some((filter) => {
            switch (filter) {
              case 'today':
                return eventDate.toDateString() === today.toDateString()
              case 'tomorrow':
                return eventDate.toDateString() === tomorrow.toDateString()
              case 'this_week':
                return eventDate <= thisWeekEnd && eventDate >= today
              case 'this_weekend':
                return eventDate >= weekendStart && eventDate <= weekendEnd
              case 'pick_date':
                if (!state.filters.customDate) return true
                return (
                  eventDate.toDateString() === new Date(state.filters.customDate).toDateString()
                )
              default:
                return true
            }
          })
        })
      }

      // Category filter
      if (state.filters.categories.length > 0) {
        filteredEvents = filteredEvents.filter((event) =>
          state.filters.categories.includes(event.category_id),
        )
      }

      // Format filter
      if (state.filters.format.length > 0) {
        filteredEvents = filteredEvents.filter((event) =>
          state.filters.format.includes(event.format),
        )
      }

      return filteredEvents
    },
    getEventById: (state) => (id) => state.events.find((event) => event.id === id),
    isLoading: (state) => state.loading,
    getError: (state) => state.error,
    getCategories: (state) => state.categories,
  },

  actions: {
    setFilters(filters) {
      this.filters = filters
    },

    // Fetch all events
    async fetchEvents() {
      this.loading = true
      this.error = null
      try {
        const response = await api.get('/api/events')
        this.events = response.data.events.data
        this.categories = response.data.categories
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
        this.currentEvent = response.data.event
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
