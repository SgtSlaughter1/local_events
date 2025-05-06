<template>
    <div class="attendee-dashboard">
        <div class="dashboard-header">
            <h2>My Dashboard</h2>
            <p class="welcome-message">Welcome back, {{ user?.name }}</p>
        </div>

        <div class="dashboard-stats">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-ticket-alt"></i>
                </div>
                <div class="stat-info">
                    <h3>My Tickets</h3>
                    <p class="stat-value">{{ stats.totalTickets }}</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="stat-info">
                    <h3>Upcoming Events</h3>
                    <p class="stat-value">{{ stats.upcomingEvents }}</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-history"></i>
                </div>
                <div class="stat-info">
                    <h3>Past Events</h3>
                    <p class="stat-value">{{ stats.pastEvents }}</p>
                </div>
            </div>
        </div>

        <div class="dashboard-sections">
            <div class="section upcoming-events">
                <div class="section-header">
                    <h3>Upcoming Events</h3>
                    <router-link to="/events" class="view-all">View All</router-link>
                </div>
                <div class="events-grid" v-if="upcomingEvents.length">
                    <div v-for="event in upcomingEvents" :key="event.id" class="event-card">
                        <div class="event-image">
                            <img :src="event.banner_url" :alt="event.title">
                        </div>
                        <div class="event-info">
                            <h4>{{ event.title }}</h4>
                            <p class="event-date">
                                <i class="fas fa-calendar"></i>
                                {{ formatDate(event.start_date) }}
                            </p>
                            <p class="event-location">
                                <i class="fas fa-map-marker-alt"></i>
                                {{ event.location }}
                            </p>
                            <div class="event-actions">
                                <router-link :to="`/events/${event.id}`" class="btn btn-primary">
                                    View Details
                                </router-link>
                            </div>
                        </div>
                    </div>
                </div>
                <div v-else class="no-events">
                    <p>No upcoming events found</p>
                </div>
            </div>

            <div class="section my-tickets">
                <div class="section-header">
                    <h3>My Tickets</h3>
                    <router-link to="/tickets" class="view-all">View All</router-link>
                </div>
                <div class="tickets-list" v-if="tickets.length">
                    <div v-for="ticket in tickets" :key="ticket.id" class="ticket-card">
                        <div class="ticket-info">
                            <h4>{{ ticket.event.title }}</h4>
                            <p class="ticket-type">{{ ticket.ticket_type }}</p>
                            <p class="ticket-date">
                                <i class="fas fa-calendar"></i>
                                {{ formatDate(ticket.event.start_date) }}
                            </p>
                        </div>
                        <div class="ticket-actions">
                            <button class="btn btn-outline-primary" @click="downloadTicket(ticket)">
                                <i class="fas fa-download"></i> Download
                            </button>
                        </div>
                    </div>
                </div>
                <div v-else class="no-tickets">
                    <p>No tickets found</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import api from '@/services/api'

const authStore = useAuthStore()
const user = ref(authStore.user)

const stats = ref({
    totalTickets: 0,
    upcomingEvents: 0,
    pastEvents: 0
})

const upcomingEvents = ref([])
const tickets = ref([])

async function fetchDashboardData() {
    try {
        const response = await api.get('/api/attendee/dashboard')
        const { stats: dashboardStats, upcomingEvents: events, tickets: userTickets } = response.data

        stats.value = dashboardStats
        upcomingEvents.value = events
        tickets.value = userTickets
    } catch (error) {
        console.error('Error fetching dashboard data:', error)
    }
}

function formatDate(date) {
    return new Date(date).toLocaleDateString('en-US', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    })
}

async function downloadTicket(ticket) {
    try {
        const response = await api.get(`/api/attendee/tickets/${ticket.id}/download`, {
            responseType: 'blob'
        })
        
        const url = window.URL.createObjectURL(new Blob([response.data]))
        const link = document.createElement('a')
        link.href = url
        link.setAttribute('download', `ticket-${ticket.id}.pdf`)
        document.body.appendChild(link)
        link.click()
        link.remove()
    } catch (error) {
        console.error('Error downloading ticket:', error)
    }
}

onMounted(() => {
    fetchDashboardData()
})
</script>

<style scoped>
.attendee-dashboard {
    padding: 2rem;
}

.dashboard-header {
    margin-bottom: 2rem;
}

.welcome-message {
    color: #666;
    margin-top: 0.5rem;
}

.dashboard-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: white;
    border-radius: 1rem;
    padding: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.stat-icon {
    width: 48px;
    height: 48px;
    background: var(--primary-color);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.5rem;
}

.stat-info h3 {
    font-size: 1rem;
    color: #666;
    margin: 0;
}

.stat-value {
    font-size: 1.5rem;
    font-weight: 600;
    margin: 0.25rem 0 0;
}

.dashboard-sections {
    display: grid;
    gap: 2rem;
}

.section {
    background: white;
    border-radius: 1rem;
    padding: 1.5rem;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
}

.section-header h3 {
    margin: 0;
}

.view-all {
    color: var(--primary-color);
    text-decoration: none;
}

.events-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 1.5rem;
}

.event-card {
    background: white;
    border-radius: 0.75rem;
    overflow: hidden;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.event-image {
    height: 160px;
    overflow: hidden;
}

.event-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.event-info {
    padding: 1rem;
}

.event-info h4 {
    margin: 0 0 0.5rem;
    font-size: 1.1rem;
}

.event-date, .event-location {
    color: #666;
    font-size: 0.9rem;
    margin: 0.25rem 0;
}

.event-actions {
    margin-top: 1rem;
}

.tickets-list {
    display: grid;
    gap: 1rem;
}

.ticket-card {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 0.75rem;
}

.ticket-info h4 {
    margin: 0 0 0.5rem;
}

.ticket-type {
    color: var(--primary-color);
    font-weight: 500;
    margin: 0.25rem 0;
}

.ticket-date {
    color: #666;
    font-size: 0.9rem;
    margin: 0;
}

.no-events, .no-tickets {
    text-align: center;
    padding: 2rem;
    color: #666;
}

.btn {
    padding: 0.5rem 1rem;
    border-radius: 0.5rem;
    border: none;
    cursor: pointer;
    font-size: 0.9rem;
    transition: all 0.2s;
}

.btn-primary {
    background: var(--primary-color);
    color: white;
}

.btn-outline-primary {
    background: none;
    border: 1px solid var(--primary-color);
    color: var(--primary-color);
}

.btn:hover {
    opacity: 0.9;
}
</style> 