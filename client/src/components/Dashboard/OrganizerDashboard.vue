<template>
  <div class="organizer-dashboard">
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
              {{ event.location }}
            </p>
            <div class="event-stats">
              <span class="stat">
                <i class="fas fa-ticket-alt"></i>
                {{ event.bookings }} Bookings
              </span>
              <span class="stat">
                <i class="fas fa-users"></i>
                {{ event.attendees }} Attendees
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
import { ref, onMounted } from 'vue'
import axios from 'axios'

const stats = ref({
  totalEvents: 0,
  totalBookings: 0,
  totalAttendees: 0,
  totalRevenue: 0
})

const upcomingEvents = ref([])
const recentActivity = ref([])

onMounted(async () => {
  try {
    // Fetch dashboard data
    const response = await axios.get('/api/organizer/dashboard')
    stats.value = response.data.stats
    upcomingEvents.value = response.data.upcomingEvents
    recentActivity.value = response.data.recentActivity
  } catch (error) {
    console.error('Error loading dashboard data:', error)
  }
})

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric'
  })
}

const formatTime = (time) => {
  return new Date(time).toLocaleTimeString('en-US', {
    hour: 'numeric',
    minute: 'numeric',
    hour12: true
  })
}
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
