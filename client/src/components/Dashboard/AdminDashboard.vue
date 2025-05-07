<template>
  <div class="admin-dashboard">
    <!-- Loading State -->
    <BaseLoading 
      :show="loading" 
      message="Loading dashboard data..."
    />

    <!-- Welcome Section -->
    <div class="welcome-section">
      <h1>Welcome, {{ auth.user?.name }}</h1>
      <p class="text-muted">Here's what's happening in your event management system</p>
    </div>

    <!-- Statistics Cards -->
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-icon">
          <i class="fas fa-calendar-check"></i>
        </div>
        <div class="stat-content">
          <h3>{{ stats.totalEvents || 0 }}</h3>
          <p>Total Events</p>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon">
          <i class="fas fa-users"></i>
        </div>
        <div class="stat-content">
          <h3>{{ stats.totalUsers || 0 }}</h3>
          <p>Total Users</p>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon">
          <i class="fas fa-ticket-alt"></i>
        </div>
        <div class="stat-content">
          <h3>{{ stats.totalTickets || 0 }}</h3>
          <p>Total Tickets</p>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon">
          <i class="fas fa-money-bill-wave"></i>
        </div>
        <div class="stat-content">
          <h3>{{ formatPrice(stats.totalRevenue || 0) }}</h3>
          <p>Total Revenue</p>
        </div>
      </div>
    </div>

    <!-- Main Content Grid -->
    <div class="dashboard-grid">
      <!-- Recent Events -->
      <div class="dashboard-card">
        <div class="card-header">
          <h3>Recent Events</h3>
          <BaseButton variant="secondary" size="small" @click="viewAllEvents">
            View All
          </BaseButton>
        </div>
        <div class="card-content">
          <div v-if="loading" class="loading-spinner">
            <div class="spinner-border text-primary" role="status">
              <span class="visually-hidden">Loading...</span>
            </div>
          </div>
          <div v-else-if="recentEvents.length === 0" class="no-data">
            No events found
          </div>
          <div v-else class="event-list">
            <div v-for="event in recentEvents" :key="event.id" class="event-item">
              <img :src="event.image_url" :alt="event.title" class="event-image" />
              <div class="event-details">
                <h4>{{ event.title }}</h4>
                <p class="text-muted">{{ formatDate(event.start_date) }}</p>
                <div class="event-stats">
                  <span><i class="fas fa-users"></i> {{ event.attendees_count }}</span>
                  <span><i class="fas fa-ticket-alt"></i> {{ event.tickets_sold }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- User Management -->
      <div class="dashboard-card">
        <div class="card-header">
          <h3>User Management</h3>
          <BaseButton variant="secondary" size="small" @click="viewAllUsers">
            View All
          </BaseButton>
        </div>
        <div class="card-content">
          <div v-if="loading" class="loading-spinner">
            <div class="spinner-border text-primary" role="status">
              <span class="visually-hidden">Loading...</span>
            </div>
          </div>
          <div v-else-if="recentUsers.length === 0" class="no-data">
            No users found
          </div>
          <div v-else class="user-list">
            <div v-for="user in recentUsers" :key="user.id" class="user-item">
              <img :src="user.avatar" :alt="user.name" class="user-avatar" />
              <div class="user-details">
                <h4>{{ user.name }}</h4>
                <p class="text-muted">{{ user.email }}</p>
                <span class="user-role">{{ user.role }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Recent Activities -->
      <div class="dashboard-card">
        <div class="card-header">
          <h3>Recent Activities</h3>
        </div>
        <div class="card-content">
          <div v-if="loading" class="loading-spinner">
            <div class="spinner-border text-primary" role="status">
              <span class="visually-hidden">Loading...</span>
            </div>
          </div>
          <div v-else-if="activities.length === 0" class="no-data">
            No activities found
          </div>
          <div v-else class="activity-list">
            <div v-for="activity in activities" :key="activity.id" class="activity-item">
              <div class="activity-icon" :class="activity.type">
                <i :class="getActivityIcon(activity.type)"></i>
              </div>
              <div class="activity-details">
                <p>{{ activity.description }}</p>
                <span class="activity-time">{{ formatTimeAgo(activity.created_at) }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="dashboard-card">
        <div class="card-header">
          <h3>Quick Actions</h3>
        </div>
        <div class="card-content">
          <div class="quick-actions">
            <BaseButton variant="primary" @click="createNewEvent">
              <i class="fas fa-plus"></i> Create Event
            </BaseButton>
            <BaseButton variant="secondary" @click="manageUsers">
              <i class="fas fa-users-cog"></i> Manage Users
            </BaseButton>
            <BaseButton variant="secondary" @click="viewReports">
              <i class="fas fa-chart-bar"></i> View Reports
            </BaseButton>
            <BaseButton variant="secondary" @click="manageSettings">
              <i class="fas fa-cog"></i> System Settings
            </BaseButton>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import BaseButton from '@/components/Base/BaseButton.vue'
import BaseLoading from '@/components/Base/BaseLoading.vue'
import { formatDate, formatTimeAgo, formatPrice } from '@/utils/formatters'

const router = useRouter()
const auth = useAuthStore()
const loading = ref(true)

// Data
const stats = ref({
  totalEvents: 0,
  totalUsers: 0,
  totalTickets: 0,
  totalRevenue: 0
})
const recentEvents = ref([])
const recentUsers = ref([])
const activities = ref([])

// Fetch dashboard data
const fetchDashboardData = async () => {
  try {
    loading.value = true
    // TODO: Implement API calls to fetch dashboard data
    // For now, using mock data
    stats.value = {
      totalEvents: 25,
      totalUsers: 150,
      totalTickets: 500,
      totalRevenue: 1500000
    }
    
    recentEvents.value = [
      {
        id: 1,
        title: 'Tech Conference 2024',
        image_url: '/images/event1.jpg',
        start_date: '2024-04-15',
        attendees_count: 150,
        tickets_sold: 200
      },
    ]
    
    recentUsers.value = [
      {
        id: 1,
        name: 'John Doe',
        email: 'john@example.com',
        avatar: '/images/avatar.svg',
        role: 'Organizer'
      },
      {
        id: 2,
        name: 'Jane Smith',
        email: 'jane@example.com',
        avatar: '/images/avatar.svg',
        role: 'Attendee'
      },
    ]
    
    activities.value = [
      {
        id: 1,
        type: 'event',
        description: 'New event "Tech Conference 2024" created',
        created_at: '2024-03-20T10:00:00'
      },
      {
        id: 2,
        type: 'user',
        description: 'New user "Jane Smith" registered',
        created_at: '2024-03-19T15:30:00'
      },
      {
        id: 3,
        type: 'ticket',
        description: '50 tickets sold for "Tech Conference 2024"',
        created_at: '2024-03-18T09:15:00'
      }
    ]
  } catch (error) {
    console.error('Error fetching dashboard data:', error)
  } finally {
    loading.value = false
  }
}

// Navigation methods
const viewAllEvents = () => router.push('/dashboard/events')
const viewAllUsers = () => router.push('/dashboard/users')
const createNewEvent = () => router.push('/dashboard/create-event')
const manageUsers = () => router.push('/dashboard/users')
const viewReports = () => router.push('/dashboard/reports')
const manageSettings = () => router.push('/dashboard/settings')

// Helper methods
const getActivityIcon = (type) => {
  const icons = {
    event: 'fas fa-calendar',
    user: 'fas fa-user',
    ticket: 'fas fa-ticket-alt',
    payment: 'fas fa-money-bill'
  }
  return icons[type] || 'fas fa-info-circle'
}

onMounted(() => {
  fetchDashboardData()
})
</script>

<style scoped>
.admin-dashboard {
  padding: 1rem;
}

.welcome-section {
  margin-bottom: 2rem;
}

.welcome-section h1 {
  font-size: 1.8rem;
  margin-bottom: 0.5rem;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
  gap: 1rem;
  margin-bottom: 2rem;
}

.stat-card {
  background: var(--card-bg);
  border-radius: 0.5rem;
  padding: 1.5rem;
  display: flex;
  align-items: center;
  gap: 1rem;
  box-shadow: var(--card-shadow);
}

.stat-icon {
  width: 48px;
  height: 48px;
  border-radius: 50%;
  background: var(--primary-color);
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 1.5rem;
}

.stat-content h3 {
  font-size: 1.5rem;
  margin: 0;
}

.stat-content p {
  margin: 0;
  color: var(--text-muted);
}

.dashboard-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
  gap: 1.5rem;
}

.dashboard-card {
  background: var(--card-bg);
  border-radius: 0.5rem;
  box-shadow: var(--card-shadow);
}

.card-header {
  padding: 1rem;
  border-bottom: 1px solid var(--border-color);
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.card-header h3 {
  margin: 0;
  font-size: 1.2rem;
}

.card-content {
  padding: 1rem;
}

.loading-spinner {
  display: none;
}

.no-data {
  text-align: center;
  padding: 2rem;
  color: var(--text-muted);
}

/* Event List Styles */
.event-list {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.event-item {
  display: flex;
  gap: 1rem;
  padding: 0.5rem;
  border-radius: 0.5rem;
  background: var(--bg-color);
}

.event-image {
  width: 80px;
  height: 80px;
  border-radius: 0.5rem;
  object-fit: cover;
}

.event-details h4 {
  margin: 0 0 0.5rem 0;
  font-size: 1rem;
}

.event-stats {
  display: flex;
  gap: 1rem;
  font-size: 0.875rem;
  color: var(--text-muted);
}

/* User List Styles */
.user-list {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.user-item {
  display: flex;
  gap: 1rem;
  padding: 0.5rem;
  border-radius: 0.5rem;
  background: var(--bg-color);
}

.user-avatar {
  width: 48px;
  height: 48px;
  border-radius: 50%;
  object-fit: cover;
}

.user-details h4 {
  margin: 0 0 0.25rem 0;
  font-size: 1rem;
}

.user-role {
  font-size: 0.875rem;
  color: var(--primary-color);
  background: var(--primary-light);
  padding: 0.25rem 0.5rem;
  border-radius: 1rem;
}

/* Activity List Styles */
.activity-list {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.activity-item {
  display: flex;
  gap: 1rem;
  padding: 0.5rem;
  border-radius: 0.5rem;
  background: var(--bg-color);
}

.activity-icon {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
}

.activity-icon.event { background: var(--primary-color); }
.activity-icon.user { background: var(--success-color); }
.activity-icon.ticket { background: var(--warning-color); }
.activity-icon.payment { background: var(--info-color); }

.activity-details p {
  margin: 0 0 0.25rem 0;
  font-size: 0.875rem;
}

.activity-time {
  font-size: 0.75rem;
  color: var(--text-muted);
}

/* Quick Actions Styles */
.quick-actions {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1rem;
}

.quick-actions .base-button {
  width: 100%;
  justify-content: center;
  gap: 0.5rem;
}

@media (max-width: 768px) {
  .dashboard-grid {
    grid-template-columns: 1fr;
  }
  
  .stats-grid {
    grid-template-columns: repeat(2, 1fr);
  }
  
  .quick-actions {
    grid-template-columns: 1fr;
  }
}
</style>
