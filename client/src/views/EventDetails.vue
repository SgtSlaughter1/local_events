<template>
  <div class="event-details-page">
    <div v-if="!event" class="text-center py-5">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
    </div>

    <template v-else>
      <!-- Back Navigation -->
      <div class="back-nav">
        <div class="container">
          <BaseButton
            variant="secondary"
            size="small"
            @click="$router.push('/events')"
          >
            <i class="fas fa-arrow-left"></i> Back to Events
          </BaseButton>
        </div>
      </div>

      <!-- Event Header Image -->
      <div class="event-header">
        <img :src="event.image_url || '/images/bg.jpg'" :alt="event.title" class="header-image" />
      </div>

      <!-- Event Content -->
      <div class="container">
        <div class="event-content">
          <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
              <h1 class="event-title">{{ event.title }}</h1>

              <!-- Host Information -->
              <div class="host-section" v-if="event.creator">
                <div class="host-info">
                  <img
                    :src="event.creator.avatar || '/images/sport.png'"
                    :alt="event.creator.name"
                    class="host-logo"
                  />
                  <div class="host-details">
                    <h6 class="host-name">{{ event.creator.name }}</h6>
                    <BaseButton
                      variant="secondary"
                      size="small"
                    >
                      Follow
                    </BaseButton>
                  </div>
                </div>
              </div>

              <!-- Category -->
              <div class="category-badge mb-4" v-if="event.category">
                <span class="badge bg-primary">{{ event.category.name }}</span>
              </div>

              <!-- Event Description -->
              <div class="description-section">
                <h5>Event Description</h5>
                <div class="description-content">{{ event.description }}</div>

                <!-- Capacity -->
                <div v-if="event.capacity" class="capacity-info mt-4">
                  <h6>Event Capacity</h6>
                  <p><i class="fas fa-users me-2"></i>{{ event.capacity }} attendees</p>
                </div>
              </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
              <div class="event-info-card">
                <!-- Date and Time -->
                <div class="info-section">
                  <h5>Date and Time</h5>
                  <div class="info-content">
                    <i class="far fa-calendar"></i>
                    <div>
                      <div class="date">{{ formatDate(event.start_date) }}</div>
                      <div class="time">
                        {{ formatTime(event.start_date) }} - {{ formatTime(event.end_date) }}
                      </div>
                      <div class="text-muted small">
                        Duration: {{ calculateDuration(event.start_date, event.end_date) }}
                      </div>
                      <a href="#" class="add-calendar-link">+ Add to Calendar</a>
                    </div>
                  </div>
                </div>

                <!-- Location -->
                <div class="info-section">
                  <h5>Location</h5>
                  <div class="info-content">
                    <i class="fas fa-map-marker-alt"></i>
                    <div>
                      <div class="venue">{{ event.location }}</div>
                      <div v-if="event.is_online" class="online-badge">
                        <i class="fas fa-video me-1"></i> Online Event
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Weather Forecast -->
                <div v-if="!event.is_online" class="info-section">
                  <h5>Weather Forecast</h5>
                  <div class="info-content">
                    <div v-if="loading" class="text-center py-3">
                      <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                      </div>
                    </div>
                    <div v-else-if="error" class="alert alert-warning">
                      {{ error }}
                    </div>
                    <div v-else-if="weather" class="weather-forecast">
                      <div class="d-flex align-items-center mb-2">
                        <i class="fas fa-temperature-high me-2"></i>
                        <span>High: {{ weather.daily?.temperature_2m_max?.[0] || 'N/A' }}°C</span>
                      </div>
                      <div class="d-flex align-items-center mb-2">
                        <i class="fas fa-temperature-low me-2"></i>
                        <span>Low: {{ weather.daily?.temperature_2m_min?.[0] || 'N/A' }}°C</span>
                      </div>
                      <div class="d-flex align-items-center">
                        <i class="fas fa-cloud-rain me-2"></i>
                        <span>Precipitation: {{ weather.daily?.precipitation_probability_max?.[0] || 'N/A' }}%</span>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Ticket Information -->
                <div class="info-section">
                  <h5>Ticket Information</h5>
                  <div class="ticket-type">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                      <span>Standard Ticket:</span>
                      <span class="ticket-price">{{ formatPrice(event.price) }}</span>
                    </div>
                  </div>
                  <BaseButton
                    variant="primary"
                    size="large"
                    class="w-100"
                    @click="handleBuyTickets"
                  >
                    Buy Tickets
                  </BaseButton>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </template>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useEventStore } from '../stores/event'
import useWeather from '../composables/useWeather'
import BaseButton from '../components/Base/BaseButton.vue'

const route = useRoute()
const router = useRouter()
const eventStore = useEventStore()
const event = ref(null)

const { weather, loading, error, fetchWeather } = useWeather(event)

onMounted(async () => {
  try {
    console.log('Route params:', route.params)
    const eventId = route.params.id
    const fetchedEvent = await eventStore.fetchEvent(eventId)
    // console.log('Fetched event:', fetchedEvent)
    event.value = fetchedEvent
    await fetchWeather()
  } catch (error) {
    console.error('Error fetching event:', error)
  }
})

const formatDate = (dateString) => {
  if (!dateString) return ''
  return new Date(dateString).toLocaleDateString('en-US', {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  })
}

const formatTime = (dateString) => {
  if (!dateString) return ''
  return new Date(dateString).toLocaleTimeString('en-US', {
    hour: 'numeric',
    minute: '2-digit',
    hour12: true,
  })
}

const calculateDuration = (start, end) => {
  if (!start || !end) return ''
  const startDate = new Date(start)
  const endDate = new Date(end)
  const diff = endDate - startDate
  const days = Math.floor(diff / (1000 * 60 * 60 * 24))
  const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60))

  if (days > 0) {
    return `${days} day${days > 1 ? 's' : ''}`
  }
  return `${hours} hour${hours > 1 ? 's' : ''}`
}

const formatPrice = (price) => {
  if (!price || price === '0.00') return 'Free'
  return new Intl.NumberFormat('en-IN', {
    style: 'currency',
    currency: 'NGN',
  }).format(price)
}

const handleBuyTickets = () => {
  router.push({
    name: 'ticket-booking',
    params: { id: event.value.id }
  })
}
</script>

<style scoped>
.weather-forecast {
  padding: 0.5rem;
  background-color: #f8f9fa;
  border-radius: 0.5rem;
}

.weather-forecast i {
  color: var(--primary-color);
  width: 1.5rem;
}

.spinner-border {
  width: 1.5rem;
  height: 1.5rem;
}
</style>
