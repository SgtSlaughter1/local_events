<template>
  <div class="landing-page">
    <!-- Hero Section -->
    <section class="hero text-center py-5">
      <div class="container">
        <div class="row min-vh-75 align-items-center">
          <div class="col-lg-8 mx-auto">
            <h1 class="display-1 fw-bold mb-4">Welcome to Event Management</h1>
            <p class="lead fs-2 mb-5">Your one-stop platform for managing and attending events</p>
            <div class="cta-buttons">
              <BaseButton
                variant="primary"
                size="large"
                class="me-3"
                @click="navigateToEvents"
              >
                Browse Events
              </BaseButton>
              <BaseButton
                variant="secondary"
                size="large"
                @click="navigateToRegister"
              >
                Register Now
              </BaseButton>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Categories Section -->
    <section class="categories py-5">
      <div class="container">
        <h2 class="text-center mb-4">Explore Categories</h2>
        <div class="row g-4">
          <div v-if="loading.categories" class="col-12 text-center">
            <div class="spinner-border text-primary" role="status">
              <span class="visually-hidden">Loading...</span>
            </div>
          </div>
          <div v-else-if="categories.length === 0" class="col-12 text-center">
            <p class="text-muted">No categories found</p>
          </div>
          <div v-else v-for="category in categories" :key="category.id" class="col-md-4 col-lg-2">
            <div class="category-card text-center">
              <img
                :src="`/images/${category.image}`"
                :alt="category.name"
                class="img-fluid rounded-circle mb-3"
              />
              <h5>{{ category.name }}</h5>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Features Section -->
    <section class="features py-5 bg-light">
      <div class="container">
        <h2 class="text-center mb-4">Why Choose Us?</h2>
        <div class="row g-4">
          <div class="col-md-4">
            <div class="feature-card text-center p-4">
              <i class="fas fa-calendar-alt fa-3x mb-3 text-primary"></i>
              <h3>Easy Event Management</h3>
              <p>Create and manage your events with our intuitive platform</p>
            </div>
          </div>
          <div class="col-md-4">
            <div class="feature-card text-center p-4">
              <i class="fas fa-users fa-3x mb-3 text-primary"></i>
              <h3>Seamless Registration</h3>
              <p>Simple and quick registration process for attendees</p>
            </div>
          </div>
          <div class="col-md-4">
            <div class="feature-card text-center p-4">
              <i class="fas fa-chart-line fa-3x mb-3 text-primary"></i>
              <h3>Real-time Analytics</h3>
              <p>Track your event's performance with detailed analytics</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Events Section -->
    <section class="events py-5 bg-light">
      <div class="container">
        <!-- Upcoming Events -->
        <div class="mb-5">
          <h2 class="text-center mb-4">Upcoming Events</h2>
          <div class="row g-4">
            <div v-if="loading.upcoming" class="col-12 text-center">
              <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
              </div>
            </div>
            <div v-else-if="upcomingEvents.length === 0" class="col-12 text-center">
              <p class="text-muted">No upcoming events found</p>
            </div>
            <div v-else v-for="event in upcomingEvents" :key="event.id" class="col-md-6 col-lg-4">
              <EventCard :event="event" />
            </div>
          </div>
        </div>

        <!-- Online Events -->
        <div>
          <h2 class="text-center mb-4">Discover Best of Online Events</h2>
          <div class="row g-4">
            <div v-if="loading.online" class="col-12 text-center">
              <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
              </div>
            </div>
            <div v-else-if="onlineEvents.length === 0" class="col-12 text-center">
              <p class="text-muted">No online events found</p>
            </div>
            <div v-else v-for="event in onlineEvents" :key="event.id" class="col-md-6 col-lg-4">
              <EventCard :event="event" />
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA Section -->
    <section class="cta py-5 text-center">
      <div class="container">
        <h3 class="fw-bold mb-3">Events specially curated for you!</h3>
        <p class="lead mb-4">Event suggestions exclusively picked for your interests</p>
        <BaseButton
          variant="primary"
          size="large"
          @click="navigateToEvents"
        >
          Get Started
        </BaseButton>
      </div>
    </section>
  </div>
</template>

<script>
import BaseButton from '@/components/Base/BaseButton.vue'
import EventCard from '@/components/Events/EventCard.vue'
import { ref, onMounted } from 'vue'
import api from '@/services/api'

export default {
  name: 'LandingPage',
  components: {
    BaseButton,
    EventCard
  },
  setup() {
    const upcomingEvents = ref([])
    const onlineEvents = ref([])
    const categories = ref([])
    const loading = ref({
      upcoming: true,
      online: true,
      categories: true
    })

    const fetchEvents = async () => {
      try {
        const response = await api.get('/api/events')
        const { events } = response.data

        // Get first 3 events for each category
        const allEvents = events.data || []
        upcomingEvents.value = allEvents
          .filter(event => !event.is_online)
          .slice(0, 3)
        
        onlineEvents.value = allEvents
          .filter(event => event.is_online)
          .slice(0, 3)

        loading.value.upcoming = false
        loading.value.online = false
      } catch (error) {
        console.error('Error fetching events:', error)
        loading.value.upcoming = false
        loading.value.online = false
      }
    }

    const fetchCategories = async () => {
      try {
        const response = await api.get('/api/events')
        const { categories: categoriesData } = response.data
        categories.value = categoriesData
        loading.value.categories = false
      } catch (error) {
        console.error('Error fetching categories:', error)
        loading.value.categories = false
      }
    }

    onMounted(() => {
      fetchEvents()
      fetchCategories()
    })

    return {
      upcomingEvents,
      onlineEvents,
      categories,
      loading
    }
  },
  methods: {
    navigateToEvents() {
      this.$router.push('/events')
    },
    navigateToRegister() {
      this.$router.push('/register')
    },
    viewEvent(eventId) {
      this.$router.push(`/events/${eventId}`)
    },
    formatDate(date) {
      return new Date(date).toLocaleDateString('en-US', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric',
      })
    },
  },
}
</script>
