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
        return response.data
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
        // Format the address data and ensure all required fields are present
        const formattedData = {
          title: eventData.title,
          description: eventData.description,
          start_date: eventData.start_date,
          end_date: eventData.end_date,
          street_address: eventData.street_address,
          city: eventData.city,
          country: eventData.country,
          capacity: eventData.capacity ? parseInt(eventData.capacity) : null,
          price: eventData.tickets?.[0]?.price ? parseFloat(eventData.tickets[0].price) : null,
          category_id: eventData.category, // The category value is already the ID from the select
          image: eventData.image,
          image_alt: eventData.image_alt,
          is_online: eventData.is_online || false,
          online_link: eventData.online_link || null
        }

        // Remove any undefined or null values
        Object.keys(formattedData).forEach(key => {
          if (formattedData[key] === undefined || formattedData[key] === null) {
            delete formattedData[key]
          }
        })

        // Ensure category_id is present
        if (!formattedData.category_id) {
          throw new Error('Category is required')
        }

        const response = await api.post('/api/events', formattedData)
        
        // Check if we have a valid event in the response
        if (!response.data || !response.data.event) {
          throw new Error('Invalid response structure from server')
        }

        const newEvent = response.data.event
        // Add the new event to the store
        this.events.push(newEvent)
        return newEvent
      } catch (error) {
        if (error.response?.data?.errors) {
          // Format validation errors into a readable message
          const errorMessages = Object.entries(error.response.data.errors)
            .map(([field, messages]) => `${field}: ${messages.join(', ')}`)
            .join('\n')
          this.error = `Validation failed:\n${errorMessages}`
        } else {
          this.error = error.response?.data?.message || error.message || 'Error creating event'
        }
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
        // Format the address data
        const formattedData = {
          ...eventData,
          location: eventData.street_address ? `${eventData.street_address}, ${eventData.city}, ${eventData.country}` : `${eventData.city}, ${eventData.country}`
        }

        const response = await api.put(`/api/events/${id}`, formattedData)
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
        return res.data.categories
      } catch (e) {
        return []
      }
    },

    async fetchMyEvents() {
      try {
        const res = await api.get('/api/my-events')
        return res.data.created_events || []
      } catch (e) {
        return []
      }
    },
  },
})
