import { defineStore } from 'pinia'
import api from '../services/api'

export const useEventStore = defineStore('event', {
  state: () => ({
    events: [],
    categories: [],
    loading: false,
    error: null,
    currentEvent: null,
    pagination: {
      current_page: 1,
      last_page: 1,
      per_page: 10,
      total: 0,
    },
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
    async fetchEvents(page = 1) {
      this.loading = true
      this.error = null
      try {
        const response = await api.get(`/api/events?page=${page}`)
        this.events = response.data.events.data
        this.categories = response.data.categories
        this.pagination = {
          current_page: response.data.events.current_page,
          last_page: response.data.events.last_page,
          per_page: response.data.events.per_page,
          total: response.data.events.total,
        }
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
        
        // Check if we have a valid event in the response
        if (!response.data || !response.data.event) {
          throw new Error('Invalid response structure from server')
        }

        const newEvent = response.data.event
        // Add the new event to the store
        this.events.push(newEvent)
        return newEvent
      } catch (error) {
        this.error = error.response?.data?.message || error.message || 'Error creating event'
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

    async fetchUserEvents() {
      this.loading = true
      this.error = null
      try {
        const response = await api.get('/api/user/events')
        return { success: true, data: response.data }
      } catch (error) {
        this.error = error.response?.data?.message || 'An error occurred while fetching user events'
        return { success: false, error: this.error }
      } finally {
        this.loading = false
      }
    },

    async fetchUserRegistrations() {
      this.loading = true
      this.error = null
      try {
        const response = await api.get('/api/user/registrations')
        return { success: true, data: response.data }
      } catch (error) {
        this.error =
          error.response?.data?.message || 'An error occurred while fetching user registrations'
        return { success: false, error: this.error }
      } finally {
        this.loading = false
      }
    },

    async fetchCategories() {
      try {
        const res = await api.get('/api/categories')
        // console.log('Fetched categories:', res.data.categories)
        return res.data.categories
      } catch (e) {
        console.error('Failed to fetch categories:', e)
        return []
      }
    },

    async fetchMyEvents() {
      try {
        const res = await api.get('/api/my-events')
        // Returns { created_events: [...], registered_events: [...] }
        return res.data.created_events || []
      } catch (e) {
        console.error('Failed to fetch my events:', e)
        return []
      }
    },
  },
})
