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
                      <div v-else class="mt-3">
                        <MapView 
                          v-if="mapData"
                          :center="mapData.center"
                          :zoom="13"
                          :markers="[mapData.marker]"
                        />
                        <div v-else class="alert alert-warning">
                          Unable to display map for this location
                        </div>
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
                  <template v-if="auth.hasRole('attendee')">
                    <div v-if="registrationStatus" class="registration-status mb-3">
                      <div :class="['status-badge', registrationStatus]">
                        {{ registrationStatus.charAt(0).toUpperCase() + registrationStatus.slice(1) }}
                      </div>
                      <p class="text-muted small mt-2">
                        You have already registered for this event
                      </p>
                    </div>
                  <BaseButton
                      v-else
                    variant="primary"
                    size="large"
                    class="w-100"
                    @click="handleBuyTickets"
                  >
                    Buy Tickets
                  </BaseButton>
                  </template>
                  <div v-else class="login-prompt">
                    <p class="text-muted">Please log in to purchase tickets</p>
                    <BaseButton
                      variant="secondary"
                      size="large"
                      class="w-100"
                      @click="$router.push('/login')"
                    >
                      Log In
                    </BaseButton>
                  </div>
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
import { ref, onMounted, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useEventStore } from '../stores/event'
import useWeather from '../composables/useWeather'
import BaseButton from '../components/Base/BaseButton.vue'
import { useAuthStore } from '../stores/auth'
import { useRegistrationStore } from '../stores/registration'
import { formatDate, formatTime, formatPrice, calculateDuration } from '@/utils/formatters'
import MapView from '../components/MapView.vue'

const route = useRoute()
const router = useRouter()
const eventStore = useEventStore()
const auth = useAuthStore()
const registrationStore = useRegistrationStore()
const event = ref(null)
const registrationStatus = ref(null)
const coordinates = ref(null)

const { weather, loading, error, fetchWeather } = useWeather(event)

// Add a computed property for map data
const mapData = computed(() => {
  console.log('Computing map data with coordinates:', coordinates.value);
  if (!coordinates.value) {
    console.log('No coordinates available');
    return null;
  }
  // Convert Proxy array to plain array
  const [lat, lng] = coordinates.value;
  const center = [Number(lat), Number(lng)];
  console.log('Computed map center:', center);
  return {
    center,
    marker: {
      position: center,
      popup: event.value?.location || ''
    }
  };
});

const geocodeLocation = async (address) => {
  try {    
    // Check if we have cached coordinates for this address
    const cachedCoords = localStorage.getItem(`coords_${address}`);
    if (cachedCoords) {
      const parsedCoords = JSON.parse(cachedCoords);
      coordinates.value = [Number(parsedCoords[0]), Number(parsedCoords[1])];
      return;
    }

    const response = await fetch(
      `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(address)}`,
      {
        headers: {
          'User-Agent': 'EventManagementApp/1.0'
        }
      }
    );
    const data = await response.json();
    console.log('Nominatim response:', data);
    
    if (data && data.length > 0) {
      const lat = Number(data[0].lat);
      const lng = Number(data[0].lon);
      coordinates.value = [lat, lng];
      // Cache the coordinates for 24 hours
      localStorage.setItem(`coords_${address}`, JSON.stringify([lat, lng]));
    } else {
      coordinates.value = null;
    }
  } catch (error) {
    console.error('Error geocoding location:', error);
    coordinates.value = null;
  }
};

onMounted(async () => {
  try {
    const eventId = route.params.id
    const fetchedEvent = await eventStore.fetchEvent(eventId)
    event.value = fetchedEvent

    if (!event.value.is_online) {
      await geocodeLocation(event.value.location)
    }
    
    await fetchWeather()

    // Check if user is registered for this event
    if (auth.hasRole('attendee')) {
      await registrationStore.fetchUserRegistrations()
      const userRegistration = registrationStore.getUserRegistrations.find(
        reg => reg.event_id === parseInt(eventId)
      )
      registrationStatus.value = userRegistration ? userRegistration.status : null
    }
  } catch (error) {
    console.error('Error fetching event:', error)
  }
})

const handleBuyTickets = () => {
  // Save initial booking data
  const bookingData = {
    eventId: event.value.id,
    eventTitle: event.value.title,
    eventDate: event.value.start_date,
    eventPrice: event.value.price,
    tickets: [{
      id: 1,
      name: 'Standard Ticket',
      price: event.value.price,
      quantity: 0,
      available_quantity: event.value.capacity || 100
    }],
    totalAmount: 0,
    totalTickets: 0,
    step: 'booking'
  }
  
  // Save to localStorage
  localStorage.setItem(`booking_${event.value.id}`, JSON.stringify(bookingData))
  
  // Navigate to booking page
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

.registration-status {
  text-align: center;
  padding: 1rem;
  background-color: #f8f9fa;
  border-radius: 0.5rem;
}

.status-badge {
  display: inline-block;
  padding: 0.25rem 0.75rem;
  border-radius: 1rem;
  font-size: 0.875rem;
  font-weight: 500;
  text-transform: capitalize;
}

.status-badge.pending {
  background-color: #fef3c7;
  color: #92400e;
}

.status-badge.confirmed {
  background-color: #dcfce7;
  color: #166534;
}

.status-badge.cancelled {
  background-color: #fee2e2;
  color: #991b1b;
}

.login-prompt {
  text-align: center;
  padding: 1rem;
}

.login-prompt p {
  margin-bottom: 1rem;
}
</style>
