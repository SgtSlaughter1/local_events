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
            <div class="section registered-events">
                <div class="section-header">
                    <h3>My Registered Events</h3>
                    <router-link to="/my-events" class="view-all">View All</router-link>
                </div>
                <div class="table-responsive" v-if="registeredEvents.length">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Event</th>
                                <th>Date</th>
                                <th>Location</th>
                                <th>Tickets</th>
                                <th>Registration Status</th>
                                <th>Payment Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="event in registeredEvents" :key="event.id">
                                <td>
                                    <div class="event-cell">
                                        <img :src="event.image" :alt="event.title" class="event-thumbnail">
                                        <span>{{ event.title }}</span>
                                    </div>
                                </td>
                                <td>{{ formatDate(event.start_date) }}</td>
                                <td>{{ event.location }}</td>
                                <td>{{ event.number_of_tickets }}</td>
                                <td>
                                    <span :class="['status-badge', event.registration_status]">
                                        {{ event.registration_status }}
                                    </span>
                                </td>
                                <td>
                                    <span :class="['status-badge', event.payment_status]">
                                        {{ event.payment_status }}
                                    </span>
                                </td>
                                <td>
                                    <div class="table-actions">
                                        <router-link :to="`/events/${event.id}`" class="btn btn-sm btn-primary">
                                            View
                                        </router-link>
                                        <button 
                                            v-if="event.registration_status === 'pending' || event.registration_status === 'confirmed'"
                                            @click="openCancelModal(event)"
                                            class="btn btn-sm btn-danger"
                                        >
                                            Cancel
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div v-else class="no-events">
                    <p>You haven't registered for any events yet</p>
                    <router-link to="/events" class="btn btn-primary">Browse Events</router-link>
                </div>
            </div>

            <div class="section upcoming-events">
                <div class="section-header">
                    <h3>Upcoming Events</h3>
                    <router-link to="/events" class="view-all">View All</router-link>
                </div>
                <div class="events-grid" v-if="upcomingEvents.length">
                    <div v-for="event in upcomingEvents" :key="event.id" class="event-card">
                        <div class="event-image">
                            <img :src="event.image" :alt="event.title">
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
                <div class="table-responsive" v-if="tickets.length">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Event</th>
                                <th>Ticket Type</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="ticket in tickets" :key="ticket.id">
                                <td>
                                    <div class="event-cell">
                                        <img :src="ticket.event.image" :alt="ticket.event.title" class="event-thumbnail">
                                        <span>{{ ticket.event.title }}</span>
                                    </div>
                                </td>
                                <td>{{ ticket.ticket_type }}</td>
                                <td>{{ formatDate(ticket.event.start_date) }}</td>
                                <td>
                                    <span :class="['status-badge', ticket.status]">
                                        {{ ticket.status }}
                                    </span>
                                </td>
                                <td>
                                    <div class="table-actions">
                                        <button class="btn btn-sm btn-primary" @click="downloadTicket(ticket)">
                                            <i class="fas fa-download"></i> Download
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div v-else class="no-tickets">
                    <p>No tickets found</p>
                </div>
            </div>
        </div>

   
        <BaseModal
            v-model="showCancelModal"
            title="Cancel Registration"
            @close="cancelReason = ''"
        >
            <div class="cancel-modal-content">
                <p>Are you sure you want to cancel your registration for <strong>{{ selectedRegistration?.title }}</strong>?</p>
                <div class="form-group">
                    <label for="cancelReason">Reason for cancellation:</label>
                    <textarea
                        id="cancelReason"
                        v-model="cancelReason"
                        class="form-control"
                        rows="3"
                        placeholder="Please provide a reason for cancellation"
                        required
                    ></textarea>
                </div>
            </div>
            <template #footer>
                <button 
                    class="btn btn-secondary" 
                    @click="showCancelModal = false"
                    :disabled="isCancelling"
                >
                    Close
                </button>
                <button 
                    class="btn btn-danger" 
                    @click="handleCancelRegistration"
                    :disabled="isCancelling"
                >
                    {{ isCancelling ? 'Cancelling...' : 'Confirm Cancellation' }}
                </button>
            </template>
        </BaseModal>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useRegistrationStore } from '@/stores/registration'
import api from '@/services/api'
import { formatDate } from '@/utils/formatters'
import BaseModal from '@/components/Base/BaseModal.vue'

const authStore = useAuthStore()
const registrationStore = useRegistrationStore()
const user = ref(authStore.user)

const stats = ref({
    totalTickets: 0,
    upcomingEvents: 0,
    pastEvents: 0
})

const upcomingEvents = ref([])
const tickets = ref([])
const registeredEvents = ref([])

// Add new refs for modal
const showCancelModal = ref(false)
const selectedRegistration = ref(null)
const cancelReason = ref('')
const isCancelling = ref(false)

async function fetchDashboardData() {
    try {
        // Fetch registered events first
        await registrationStore.fetchUserRegistrations()
        
        // Calculate stats from registrations
        const upcomingRegistrations = registrationStore.getUpcomingRegistrations
        const pastRegistrations = registrationStore.getPastRegistrations
        
        stats.value = {
            totalTickets: registrationStore.getUserRegistrations.reduce((sum, reg) => sum + reg.number_of_tickets, 0),
            upcomingEvents: upcomingRegistrations.length,
            pastEvents: pastRegistrations.length
        }

        // Set registered events with proper status
        registeredEvents.value = registrationStore.getUserRegistrations
            .filter(registration => registration.status !== 'cancelled')
            .map(registration => ({
                ...registration.event,
                registration_status: registration.status,
                payment_status: registration.payment_status,
                number_of_tickets: registration.number_of_tickets,
                registration_id: registration.id
            }))

        // Fetch upcoming events (not registered)
        const response = await api.get('/api/events/upcoming')
        upcomingEvents.value = response.data.data

        // Fetch tickets
        const ticketsResponse = await api.get('/api/user/tickets')
        tickets.value = ticketsResponse.data.data
    } catch (error) {
        console.error('Error fetching dashboard data:', error)
    }
}

async function downloadTicket(ticket) {
    try {
        const response = await api.get(`/api/tickets/${ticket.id}/download`, {
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

async function openCancelModal(registration) {
    selectedRegistration.value = registration
    showCancelModal.value = true
}

async function handleCancelRegistration() {
    if (!cancelReason.value.trim()) {
        alert('Please provide a reason for cancellation')
        return
    }

    isCancelling.value = true
    try {
        await registrationStore.cancelRegistration(selectedRegistration.value.registration_id, cancelReason.value)
        // Update the registration status in the list
        const index = registeredEvents.value.findIndex(
            event => event.registration_id === selectedRegistration.value.registration_id
        )
        if (index !== -1) {
            registeredEvents.value[index].registration_status = 'pending'
        }
        showCancelModal.value = false
        cancelReason.value = ''
    } catch (error) {
        console.error('Error cancelling registration:', error)
    } finally {
        isCancelling.value = false
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
    display: flex;
    gap: 0.5rem;
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

.event-status {
    display: flex;
    gap: 0.5rem;
    margin: 0.5rem 0;
}

.status-badge {
    padding: 0.25rem 0.5rem;
    border-radius: 0.25rem;
    font-size: 0.8rem;
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

.status-badge.paid {
    background-color: #dcfce7;
    color: #166534;
}

.status-badge.refunded {
    background-color: #f3f4f6;
    color: #374151;
}

.ticket-count {
    color: #666;
    font-size: 0.9rem;
    margin: 0.5rem 0;
}

.ticket-count i {
    margin-right: 0.5rem;
    color: var(--primary-color);
}

.btn-danger {
    background-color: #dc2626;
    color: white;
    margin-left: 0.5rem;
}

.btn-danger:hover {
    background-color: #b91c1c;
}

.no-events {
    text-align: center;
    padding: 2rem;
    color: #666;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1rem;
}

.no-events .btn {
    margin-top: 1rem;
}

.table-responsive {
    overflow-x: auto;
    margin: 0 -1.5rem;
    padding: 0 1.5rem;
}

.data-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 1rem;
    background: white;
    border-radius: 0.5rem;
    overflow: hidden;
}

.data-table th,
.data-table td {
    padding: 1rem;
    text-align: left;
    border-bottom: 1px solid #e5e7eb;
}

.data-table th {
    background-color: #f9fafb;
    font-weight: 600;
    color: #374151;
}

.data-table tbody tr:hover {
    background-color: #f9fafb;
}

.event-cell {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.event-thumbnail {
    width: 40px;
    height: 40px;
    border-radius: 0.375rem;
    object-fit: cover;
}

.table-actions {
    display: flex;
    gap: 0.5rem;
}

.btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}

.status-badge {
    padding: 0.25rem 0.5rem;
    border-radius: 0.25rem;
    font-size: 0.75rem;
    font-weight: 500;
    text-transform: capitalize;
    display: inline-block;
}

.no-events,
.no-tickets {
    text-align: center;
    padding: 2rem;
    color: #666;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1rem;
}

.no-events .btn,
.no-tickets .btn {
    margin-top: 1rem;
}

.cancel-modal-content {
    padding: 1rem 0;
}

.form-group {
    margin-top: 1rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 500;
}

.form-control {
    width: 100%;
    padding: 0.5rem;
    border: 1px solid #d1d5db;
    border-radius: 0.375rem;
    font-size: 0.875rem;
}

.form-control:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 2px rgba(var(--primary-color-rgb), 0.1);
}

.btn:disabled {
    opacity: 0.7;
    cursor: not-allowed;
}
</style> 