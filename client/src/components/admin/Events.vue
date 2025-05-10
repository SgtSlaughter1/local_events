<template>
    <div class="events-container">
        <div class="dashboard-content" :class="{ 'loading': eventStore.isLoading }">
            <!-- Loading State -->
            <BaseLoading :show="eventStore.isLoading" message="Loading events..." />

            <!-- Header Section -->
            <div class="header mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h2 class="mb-1">Event Management</h2>
                        <p class="text-muted mb-0">Manage and monitor all events</p>
                    </div>
                    <div class="d-flex gap-2">
                        <div class="input-group">
                            <span class="input-group-text bg-white">
                                <i class="fas fa-search text-muted"></i>
                            </span>
                            <input type="text" class="form-control border-start-0" placeholder="Search events..."
                                v-model="searchQuery" @input="handleSearch">
                        </div>
                        <select class="form-select" v-model="categoryFilter" @change="handleFilter">
                            <option value="">All Categories</option>
                            <option v-for="category in categories" :key="category.id" :value="category.id">
                                {{ category.name }}
                            </option>
                        </select>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title mb-0">Total Events</h6>
                                        <h3 class="mb-0 mt-2">{{ eventStore.getPagination.total }}</h3>
                                    </div>
                                    <i class="fas fa-calendar-check fa-2x opacity-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title mb-0">Upcoming Events</h6>
                                        <h3 class="mb-0 mt-2">{{ upcomingEvents }}</h3>
                                    </div>
                                    <i class="fas fa-calendar-plus fa-2x opacity-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title mb-0">Total Tickets Sold</h6>
                                        <h3 class="mb-0 mt-2">{{ totalTicketsSold }}</h3>
                                    </div>
                                    <i class="fas fa-ticket-alt fa-2x opacity-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title mb-0">Total Revenue</h6>
                                        <h3 class="mb-0 mt-2">{{ formatPrice(totalRevenue) }}</h3>
                                    </div>
                                    <i class="fas fa-money-bill-wave fa-2x opacity-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Error State -->
            <div v-if="eventStore.getError" class="alert alert-danger" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                {{ eventStore.getError }}
            </div>

            <!-- Events Table -->
            <div v-else class="card">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Event</th>
                                <th>Category</th>
                                <th>Date</th>
                                <th>Location</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="event in eventStore.getEvents" :key="event.id">
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img :src="event.image_url || '/images/event-placeholder.jpg'"
                                            :alt="event.title" class="event-image me-3" />
                                        <div>
                                            <div class="fw-bold">{{ event.title }}</div>
                                            <div class="text-muted small">{{ event.description?.substring(0, 20) }}{{ event.description?.length > 20 ? '...' : '' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-primary">{{ event.category?.name }}</span>
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span>{{ formatDate(event.start_date) }}</span>
                                        <small class="text-muted">{{ formatTimeAgo(event.start_date) }}</small>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span>{{ event.city }}, {{ event.country }}</span>
                                        <small class="text-muted event-type">{{ event.is_online ? 'Online Event' : 'In-Person' }}</small>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge" :class="getEventStatusClass(event)">
                                        {{ getEventStatus(event) }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group justify-content-center">
                                        <BaseButton variant="primary" size="small" @click="openEventDetails(event)"
                                            title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </BaseButton>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="card-footer d-flex justify-content-between align-items-center">
                    <div class="text-muted">
                        Showing {{ paginationInfo.from }} to {{ paginationInfo.to }} of {{ paginationInfo.total }}
                        entries
                    </div>
                    <nav>
                        <ul class="pagination mb-0">
                            <li class="page-item" :class="{ disabled: currentPage === 1 }">
                                <a class="page-link" href="#" @click.prevent="changePage(currentPage - 1)">
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            </li>
                            <li v-for="page in totalPages" :key="page" class="page-item"
                                :class="{ active: currentPage === page }">
                                <a class="page-link" href="#" @click.prevent="changePage(page)">{{ page }}</a>
                            </li>
                            <li class="page-item" :class="{ disabled: currentPage === totalPages }">
                                <a class="page-link" href="#" @click.prevent="changePage(currentPage + 1)">
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>

        <!-- Event Details Modal -->
        <BaseModal v-model="showEventDetails" title="Event Details" size="modal-lg">
            <div v-if="selectedEvent" class="row">
                <div class="col-md-6">
                    <h6 class="mb-3">Event Information</h6>
                    <table class="table table-sm">
                        <tr>
                            <th>Title:</th>
                            <td>{{ selectedEvent.title }}</td>
                        </tr>
                        <tr>
                            <th>Description:</th>
                            <td>{{ selectedEvent.description }}</td>
                        </tr>
                        <tr>
                            <th>Category:</th>
                            <td>{{ selectedEvent.category?.name }}</td>
                        </tr>
                        <tr>
                            <th>Date:</th>
                            <td>{{ formatDate(selectedEvent.start_date) }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h6 class="mb-3">Location Information</h6>
                    <table class="table table-sm">
                        <tr>
                            <th>Type:</th>
                            <td>{{ selectedEvent.is_online ? 'Online Event' : 'In-Person' }}</td>
                        </tr>
                        <tr>
                            <th>Location:</th>
                            <td>{{ selectedEvent.city }}, {{ selectedEvent.country }}</td>
                        </tr>
                        <tr>
                            <th>Status:</th>
                            <td>
                                <span class="badge" :class="getEventStatusClass(selectedEvent)">
                                    {{ getEventStatus(selectedEvent) }}
                                </span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </BaseModal>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useEventStore } from '@/stores/event'
import BaseModal from '@/components/Base/BaseModal.vue'
import BaseButton from '@/components/Base/BaseButton.vue'
import BaseLoading from '@/components/Base/BaseLoading.vue'
import { formatDate, formatTimeAgo, formatPrice } from '@/utils/formatters'
import { useToast } from 'vue-toastification'

const eventStore = useEventStore()
const toast = useToast()
const searchQuery = ref('')
const categoryFilter = ref('')
const selectedEvent = ref(null)
const showEventDetails = ref(false)
const categories = ref([])
const searchTimeout = ref(null)

// Computed properties
const currentPage = computed(() => eventStore.getPagination?.current_page || 1)
const totalPages = computed(() => {
    const total = eventStore.getPagination?.total || 0
    const perPage = eventStore.getPagination?.per_page || 10
    return Math.ceil(total / perPage)
})
const paginationInfo = computed(() => {
    const current_page = eventStore.getPagination?.current_page || 1
    const total = eventStore.getPagination?.total || 0
    const per_page = eventStore.getPagination?.per_page || 10
    const from = total === 0 ? 0 : (current_page - 1) * per_page + 1
    const to = Math.min(current_page * per_page, total)
    return { from, to, total }
})

// Event statistics
const upcomingEvents = computed(() => {
    if (!eventStore.getEvents) return 0
    const now = new Date()
    return eventStore.getEvents.filter(event => {
        const eventDate = new Date(event.start_date)
        return eventDate > now
    }).length
})

const totalTicketsSold = computed(() => {
    if (!eventStore.getEvents) return 0
    return eventStore.getEvents.reduce((total, event) => total + (event.tickets_sold || 0), 0)
})

const totalRevenue = computed(() => {
    if (!eventStore.getEvents) return 0
    return eventStore.getEvents.reduce((total, event) => total + (event.revenue || 0), 0)
})

// Methods
const changePage = async (page) => {
    if (page < 1 || page > totalPages.value) return
    await fetchEvents(page)
}

const fetchEvents = async (page = 1) => {
    try {
        await eventStore.fetchEvents(page)
    } catch (error) {
        console.error('Error fetching events:', error)
        toast.error('Failed to fetch events')
    }
}

const handleSearch = () => {
    if (searchTimeout.value) clearTimeout(searchTimeout.value)
    searchTimeout.value = setTimeout(() => {
        fetchEvents(1)
    }, 300)
}

const handleFilter = () => {
    fetchEvents(1)
}

const getEventStatus = (event) => {
    const now = new Date()
    const eventDate = new Date(event.start_date)

    if (eventDate < now) return 'Past'
    if (eventDate > now) return 'Upcoming'
    return 'Ongoing'
}

const getEventStatusClass = (event) => {
    const status = getEventStatus(event)
    const classes = {
        'Past': 'bg-secondary',
        'Upcoming': 'bg-success',
        'Ongoing': 'bg-primary'
    }
    return classes[status] || 'bg-secondary'
}

const openEventDetails = (event) => {
    selectedEvent.value = event
    showEventDetails.value = true
}

// Initialize
onMounted(async () => {
    try {
        // Fetch categories
        categories.value = await eventStore.fetchCategories()
        // Fetch initial events
        await fetchEvents()
    } catch (error) {
        console.error('Error initializing:', error)
    }
})
</script>

<style scoped>
.events-container {
    padding: 0;
    height: 100%;
}

.dashboard-content {
    position: relative;
    min-height: 400px;
    padding: 1.5rem;
}

.dashboard-content.loading {
    pointer-events: none;
}

.event-image {
    width: 60px;
    height: 60px;
    border-radius: 8px;
    object-fit: cover;
}

.table {
    width: 100%;
    table-layout: fixed;
}

.table th {
    font-weight: 600;
    background-color: #f8f9fa;
    border-bottom: 2px solid #dee2e6;
}

.table th:nth-child(1) { width: 30%; }  /* Event column */
.table th:nth-child(2) { width: 15%; }  /* Category column */
.table th:nth-child(3) { width: 15%; }  /* Date column */
.table th:nth-child(4) { width: 20%; }  /* Location column */
.table th:nth-child(5) { width: 10%; }  /* Status column */
.table th:nth-child(6) { width: 10%; }  /* Actions column */

.badge {
    padding: 0.5em 0.75em;
    font-weight: 500;
}

.btn-group {
    display: flex;
    gap: 0.25rem;
    justify-content: center;
    width: 100%;
}

.page-link {
    color: #0d6efd;
    cursor: pointer;
    padding: 0.5rem 0.75rem;
}

.page-item.active .page-link {
    background-color: #0d6efd;
    border-color: #0d6efd;
}

.modal-body .table th {
    width: 120px;
    background: none;
    border-bottom: 1px solid #dee2e6;
}

.card {
    border: none;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    margin-bottom: 1.5rem;
}

.input-group-text {
    border-right: none;
}

.form-control:focus {
    box-shadow: none;
    border-color: #dee2e6;
}

.table> :not(caption)>*>* {
    padding: 1rem;
}

.fa-2x {
    font-size: 1.5em;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .dashboard-content {
        padding: 1rem;
    }
    
    .card {
        margin-bottom: 1rem;
    }
}

.table td {
    vertical-align: middle;
    word-wrap: break-word;
    max-width: 0;
}

.event-type {
    white-space: normal;
    word-break: break-word;
    line-height: 1.2;
}
</style>