<template>
    <div :class="['event-card', { 'event-details-view': isDetailed }]">
        <!-- Preview Mode -->
        <div v-if="!isDetailed">
            <div class="position-relative">
                <img :src="event.image_url || '/default-event-image.jpg'" :alt="event.title" class="card-img-top">
                <div class="position-absolute top-0 end-0 m-2">
                    <span class="badge" :class="statusClass">{{ event.status }}</span>
                </div>
            </div>

            <div class="card-body">
                <h3 class="card-title">{{ event.title }}</h3>
                <p class="card-text text-muted text-truncate">{{ event.description }}</p>

                <div class="d-flex align-items-center mb-3">
                    <i class="fas fa-calendar-alt text-primary me-2"></i>
                    <span class="event-date">{{ formatDate(event.start_date) }}</span>
                </div>

                <div class="d-flex align-items-center mb-3">
                    <i class="fas fa-map-marker-alt text-primary me-2"></i>
                    <span class="event-location">{{ event.location }}</span>
                </div>

                <div class="d-flex align-items-center justify-content-between mt-4">
                    <span class="text-primary fw-bold">
                        {{ event.price ? formatPrice(event.price) : 'Free' }}
                    </span>
                    <router-link :to="'/events/' + event.id" class="btn btn-primary">
                        View Details
                    </router-link>
                </div>
            </div>
        </div>

        <!-- Detailed View -->
        <div v-else class="event-details">
            <!-- Back Button -->
            <div class="back-button">
                <button @click="$emit('close-details')" class="btn btn-link">
                    <i class="fas fa-arrow-left"></i> Back to Events
                </button>
            </div>

            <!-- Header Image -->
            <div class="event-header">
                <img :src="event.image_url || '/default-event-image.jpg'" :alt="event.title" class="header-image">
            </div>

            <!-- Event Content -->
            <div class="event-content container py-4">
                <div class="row">
                    <!-- Main Content -->
                    <div class="col-lg-8">
                        <h1 class="event-title mb-4">{{ event.title }}</h1>
                        
                        <!-- Host Information -->
                        <div class="host-info mb-4" v-if="event.organizer">
                            <h5 class="section-title">Hosted by</h5>
                            <div class="d-flex align-items-center">
                                <div class="host-avatar">
                                    <img :src="event.organizer.avatar || '/default-avatar.jpg'" :alt="event.organizer.name">
                                </div>
                                <div class="ms-3">
                                    <h6 class="mb-0">{{ event.organizer.name }}</h6>
                                    <button class="btn btn-link p-0" v-if="event.organizer.contact">Contact</button>
                                </div>
                            </div>
                        </div>

                        <!-- Event Description -->
                        <div class="event-description mb-4">
                            <h5 class="section-title">Event Description</h5>
                            <div class="description-content">{{ event.description }}</div>
                        </div>
                    </div>

                    <!-- Sidebar Information -->
                    <div class="col-lg-4">
                        <div class="event-info-card">
                            <!-- Date and Time -->
                            <div class="info-section">
                                <h5 class="section-title">Date and Time</h5>
                                <div class="d-flex mb-2">
                                    <i class="fas fa-calendar-alt text-primary me-2 mt-1"></i>
                                    <div>
                                        <div>{{ formatDate(event.start_date) }}</div>
                                        <div class="text-muted">{{ formatTime(event.start_time) }} - {{ formatTime(event.end_time) }}</div>
                                        <a href="#" class="add-calendar-link">+ Add to Calendar</a>
                                    </div>
                                </div>
                            </div>

                            <!-- Location -->
                            <div class="info-section">
                                <h5 class="section-title">Location</h5>
                                <div class="d-flex">
                                    <i class="fas fa-map-marker-alt text-primary me-2 mt-1"></i>
                                    <div>
                                        <div>{{ event.venue }}</div>
                                        <div class="text-muted">{{ event.address }}</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Ticket Information -->
                            <div class="info-section">
                                <h5 class="section-title">Ticket Information</h5>
                                <div class="ticket-price mb-2">
                                    <span class="price-label">Standard Ticket:</span>
                                    <span class="price-amount">{{ event.price ? formatPrice(event.price) : 'Free' }}</span>
                                </div>
                                <button @click="$emit('register', event.id)" class="btn btn-primary w-100">
                                    Buy Tickets
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
    event: {
        type: Object,
        required: true
    },
    isDetailed: {
        type: Boolean,
        default: false
    }
})

defineEmits(['view-details', 'close-details', 'register'])

const statusClass = computed(() => {
    const classes = {
        'upcoming': 'bg-success text-white',
        'ongoing': 'bg-info text-white',
        'completed': 'bg-secondary text-white',
        'cancelled': 'bg-danger text-white'
    }
    return classes[props.event.status?.toLowerCase()] || classes['upcoming']
})

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-US', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    })
}

const formatTime = (time) => {
    return new Date(`2000-01-01T${time}`).toLocaleTimeString('en-US', {
        hour: 'numeric',
        minute: 'numeric'
    })
}

const formatPrice = (price) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD'
    }).format(price)
}
</script>

<style scoped>
.event-card {
    border: none;
    border-radius: 1rem;
    overflow: hidden;
    background: var(--white-color);
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

/* Preview Card Styles */
.card-img-top {
    height: 200px;
    object-fit: cover;
}

.card-title {
    color: var(--primary-color);
    font-weight: 600;
    margin-bottom: 1rem;
}

/* Detailed View Styles */
.event-details-view {
    box-shadow: none;
    max-width: 1200px;
    margin: 0 auto;
}

.back-button {
    padding: 1rem;
    background: white;
}

.event-header {
    position: relative;
    height: 400px;
    overflow: hidden;
}

.header-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.event-content {
    background: white;
    border-radius: 1rem 1rem 0 0;
    margin-top: -3rem;
    position: relative;
}

.event-title {
    font-size: 2.5rem;
    font-weight: 700;
    color: var(--primary-color);
}

.section-title {
    font-size: 1.1rem;
    font-weight: 600;
    margin-bottom: 1rem;
    color: #333;
}

.host-info {
    background: #f8f9fa;
    padding: 1rem;
    border-radius: 0.5rem;
}

.host-avatar img {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    object-fit: cover;
}

.event-info-card {
    background: #f8f9fa;
    border-radius: 0.5rem;
    padding: 1.5rem;
}

.info-section {
    padding-bottom: 1.5rem;
    margin-bottom: 1.5rem;
    border-bottom: 1px solid #dee2e6;
}

.info-section:last-child {
    padding-bottom: 0;
    margin-bottom: 0;
    border-bottom: none;
}

.add-calendar-link {
    color: var(--primary-color);
    text-decoration: none;
    font-size: 0.9rem;
    display: inline-block;
    margin-top: 0.5rem;
}

.ticket-price {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.5rem 0;
}

.price-label {
    color: #666;
}

.price-amount {
    font-weight: 600;
    color: var(--primary-color);
}

.description-content {
    white-space: pre-line;
    line-height: 1.6;
    color: #4a4a4a;
}
</style>