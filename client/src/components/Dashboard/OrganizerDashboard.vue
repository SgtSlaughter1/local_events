<template>
  <div class="organizer-dashboard">
    <!-- Loading State -->
    <BaseLoading 
      :show="isLoading" 
      message="Loading dashboard data..."
    />

    <!-- Quick Stats -->
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-icon">
          <i class="fas fa-calendar-check"></i>
        </div>
        <div class="stat-content">
          <h3 class="stat-value">{{ stats.totalEvents }}</h3>
          <p class="stat-label">Total Events</p>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon">
          <i class="fas fa-ticket-alt"></i>
        </div>
        <div class="stat-content">
          <h3 class="stat-value">{{ stats.totalBookings }}</h3>
          <p class="stat-label">Total Bookings</p>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon">
          <i class="fas fa-users"></i>
        </div>
        <div class="stat-content">
          <h3 class="stat-value">{{ stats.totalAttendees }}</h3>
          <p class="stat-label">Total Attendees</p>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon">
          <i class="fas fa-dollar-sign"></i>
        </div>
        <div class="stat-content">
          <h3 class="stat-value">${{ stats.totalRevenue }}</h3>
          <p class="stat-label">Total Revenue</p>
        </div>
      </div>
    </div>

    <!-- Quick Actions -->
    <div class="quick-actions">
      <h2 class="section-title">Quick Actions</h2>
      <div class="actions-grid">
        <router-link to="/dashboard/create-event" class="action-card">
          <i class="fas fa-plus-circle"></i>
          <span>Create New Event</span>
        </router-link>
        <router-link to="/dashboard/my-events" class="action-card">
          <i class="fas fa-calendar-alt"></i>
          <span>View My Events</span>
        </router-link>
        <router-link to="/dashboard/bookings" class="action-card">
          <i class="fas fa-ticket-alt"></i>
          <span>Manage Bookings</span>
        </router-link>
        <router-link to="/dashboard/analytics" class="action-card">
          <i class="fas fa-chart-bar"></i>
          <span>View Analytics</span>
        </router-link>
      </div>
    </div>

    <!-- Upcoming Events -->
    <div class="upcoming-events">
      <div class="section-header">
        <h2 class="section-title">Upcoming Events</h2>
        <router-link to="/dashboard/my-events" class="view-all">View All</router-link>
      </div>
      <div class="events-grid">
        <div v-for="event in upcomingEvents" :key="event.id" class="event-card">
          <div class="event-image">
            <img :src="event.image" :alt="event.title">
            <span class="event-date">{{ formatDate(event.date) }}</span>
          </div>
          <div class="event-content">
            <h3 class="event-title">{{ event.title }}</h3>
            <p class="event-location">
              <i class="fas fa-map-marker-alt"></i>
              <template v-if="event.is_online">
                <i class="fas fa-video me-1"></i> Online Event
              </template>
              <template v-else>
                {{ event.city }}, {{ event.country }}
              </template>
            </p>
            <div class="event-stats">
              <span class="stat">
                <i class="fas fa-ticket-alt"></i>
                {{ event.bookings_count || 0 }} Bookings
              </span>
              <span class="stat">
                <i class="fas fa-users"></i>
                {{ event.attendees_count || 0 }} Attendees
              </span>
              <span class="stat" v-if="event.price">
                <i class="fas fa-dollar-sign"></i>
                {{ formatPrice(event.price) }}
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Recent Activity -->
    <div class="recent-activity">
      <div class="section-header">
        <h2 class="section-title">Recent Activity</h2>
        <router-link to="/dashboard/activity" class="view-all">View All</router-link>
      </div>
      <div class="activity-list">
        <div v-for="activity in recentActivity" :key="activity.id" class="activity-item">
          <div class="activity-icon" :class="activity.type">
            <i :class="activity.icon"></i>
          </div>
          <div class="activity-content">
            <p class="activity-text">{{ activity.text }}</p>
            <span class="activity-time">{{ formatTime(activity.time) }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onBeforeMount } from 'vue'
import { useEventStore } from '@/stores/event'
import { useRegistrationStore } from '@/stores/registration'
import api from '@/services/api'
import { formatDate, formatTime, formatPrice } from '@/utils/formatters'
import BaseLoading from '@/components/Base/BaseLoading.vue'

const eventStore = useEventStore()
const registrationStore = useRegistrationStore()
const isLoading = ref(true)

const stats = ref({
  totalEvents: 0,
  totalBookings: 0,
  totalAttendees: 0,
  totalRevenue: 0
})

const upcomingEvents = ref([])
const recentActivity = ref([])

const fetchDashboardData = async () => {
  try {
    isLoading.value = true
    // Fetch user's events
    const eventsResponse = await eventStore.fetchMyEvents()
    const events = eventsResponse || []
    
    // Calculate stats
    stats.value = {
      totalEvents: events.length,
      totalBookings: events.reduce((sum, event) => sum + (event.bookings_count || 0), 0),
      totalAttendees: events.reduce((sum, event) => sum + (event.attendees_count || 0), 0),
      totalRevenue: events.reduce((sum, event) => sum + (event.revenue || 0), 0)
    }

    // Get upcoming events (events with start_date in the future)
    const now = new Date()
    upcomingEvents.value = events
      .filter(event => new Date(event.start_date) > now)
      .sort((a, b) => new Date(a.start_date) - new Date(b.start_date))
      .slice(0, 3) // Show only 3 upcoming events

    // Fetch recent registrations for activity feed
    const registrationsResponse = await registrationStore.fetchUserRegistrations()
    const registrations = registrationsResponse?.data || []

    // Format recent activity
    recentActivity.value = registrations
      .sort((a, b) => new Date(b.created_at) - new Date(a.created_at))
      .slice(0, 5) // Show only 5 recent activities
      .map(registration => ({
        id: registration.id,
        type: 'booking',
        icon: 'fas fa-ticket-alt',
        text: `New booking for ${registration.event?.title || 'Event'}`,
        time: registration.created_at
      }))

  } catch (error) {
    console.error('Error loading dashboard data:', error)
  } finally {
    isLoading.value = false
  }
}

// Use onBeforeMount as it's the earliest available hook in Vue 3 Composition API
onBeforeMount(() => {
  fetchDashboardData()
})
</script>

<style scoped>
.organizer-dashboard {
  padding: 1rem;
}

/* Stats Grid */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.stat-card {
  background: var(--white-color);
  border-radius: 8px;
  padding: 1.5rem;
  display: flex;
  align-items: center;
  gap: 1rem;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.stat-icon {
  width: 48px;
  height: 48px;
  border-radius: 12px;
  background: var(--primary-color);
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.5rem;
}

.stat-content {
  flex: 1;
}

.stat-value {
  font-size: 1.5rem;
  font-weight: 600;
  margin: 0;
  color: var(--text-color);
}

.stat-label {
  font-size: 0.875rem;
  color: #6c757d;
  margin: 0;
}

/* Quick Actions */
.section-title {
  font-size: 1.25rem;
  font-weight: 600;
  margin-bottom: 1rem;
  color: var(--text-color);
}

.actions-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 1rem;
  margin-bottom: 2rem;
}

.action-card {
  background: var(--white-color);
  border-radius: 8px;
  padding: 1.5rem;
  text-align: center;
  text-decoration: none;
  color: var(--text-color);
  transition: transform 0.2s, box-shadow 0.2s;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.action-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.action-card i {
  font-size: 2rem;
  color: var(--primary-color);
  margin-bottom: 0.5rem;
}

.action-card span {
  display: block;
  font-weight: 500;
}

/* Upcoming Events */
.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
}

.view-all {
  color: var(--primary-color);
  text-decoration: none;
  font-weight: 500;
}

.events-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.event-card {
  background: var(--white-color);
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.event-image {
  position: relative;
  height: 160px;
}

.event-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.event-date {
  position: absolute;
  bottom: 1rem;
  left: 1rem;
  background: rgba(0, 0, 0, 0.7);
  color: white;
  padding: 0.25rem 0.75rem;
  border-radius: 4px;
  font-size: 0.875rem;
}

.event-content {
  padding: 1rem;
}

.event-title {
  font-size: 1.125rem;
  font-weight: 600;
  margin: 0 0 0.5rem;
  color: var(--text-color);
}

.event-location {
  font-size: 0.875rem;
  color: #6c757d;
  margin: 0 0 0.5rem;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.event-stats {
  display: flex;
  gap: 1rem;
  font-size: 0.875rem;
  color: #6c757d;
}

.event-stats .stat {
  display: flex;
  align-items: center;
  gap: 0.25rem;
}

/* Recent Activity */
.activity-list {
  background: var(--white-color);
  border-radius: 8px;
  padding: 1rem;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.activity-item {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1rem;
  border-bottom: 1px solid #e9ecef;
}

.activity-item:last-child {
  border-bottom: none;
}

.activity-icon {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.25rem;
}

.activity-icon.booking {
  background-color: #e3f2fd;
  color: #1976d2;
}

.activity-icon.event {
  background-color: #e8f5e9;
  color: #2e7d32;
}

.activity-icon.revenue {
  background-color: #fff3e0;
  color: #f57c00;
}

.activity-content {
  flex: 1;
}

.activity-text {
  margin: 0;
  color: var(--text-color);
}

.activity-time {
  font-size: 0.875rem;
  color: #6c757d;
}

/* Responsive Design */
@media (max-width: 1200px) {
  .stats-grid {
    grid-template-columns: repeat(2, 1fr);
  }

  .actions-grid {
    grid-template-columns: repeat(2, 1fr);
  }

  .events-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 768px) {

  .stats-grid,
  .actions-grid,
  .events-grid {
    grid-template-columns: 1fr;
  }
}
</style>
