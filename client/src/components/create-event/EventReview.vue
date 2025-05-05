<template>
    <div class="event-review-card">
        <!-- Banner -->
        <div class="banner-section">
            <img :src="data.bannerUrl || defaultBanner" alt="Event Banner" class="banner-img" />
        </div>
        <div class="content-section">
            <!-- Title -->
            <h1 class="event-title">{{ data.title || 'Event Title' }}</h1>
            <div class="info-row">
                <!-- Date & Time -->
                <div class="date-time">
                    <i class="fas fa-calendar-alt"></i>
                    <span>{{ formatDate(data.start_date) }} - {{ formatDate(data.end_date) }}</span>
                </div>
                <!-- Ticket Info -->
                <div class="ticket-info">
                    <i class="fas fa-ticket-alt"></i>
                    <span>
                        {{ data.eventType === 'ticketed' ? 'Ticketed' : 'Free' }}:
                        {{ data.tickets && data.tickets[0] ? `₦${data.tickets[0].price}` : 'Free' }}
                    </span>
                </div>
            </div>
            <!-- Location -->
            <div class="location-section">
                <div class="location-label">
                    <i class="fas fa-map-marker-alt"></i>
                    <span>{{ data.location || 'Address' }}</span>
                </div>
                <div class="map-placeholder">
                    <img src="/images/map.png" alt="Map Placeholder" />
                </div>
            </div>
            <!-- Host -->
            <div class="host-section">
                <img v-if="user && user.avatar" :src="user.avatar" class="host-avatar" />
                <div v-else class="host-avatar"></div>
                <div class="host-info">
                    <div class="host-name">{{ user?.name || 'Host Name' }}</div>
                    <button class="contact-btn">Contact</button>
                    <button class="follow-btn">Follow</button>
                </div>
            </div>
            <!-- Description -->
            <div class="description-section">
                <h2>Event Description</h2>
                <p>{{ data.description || 'Event description goes here.' }}</p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
    modelValue: Object,
    user: Object
})

const data = computed(() => props.modelValue || {})

const defaultBanner = '/default-banner.png'

const formatDate = (date) => {
    if (!date) return 'Not specified'
    return new Date(date).toLocaleDateString('en-US', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    })
}

const categoryOptions = [
    { value: 'music', label: 'Music' },
    { value: 'sports', label: 'Sports' },
    { value: 'conference', label: 'Conference' },
    { value: 'workshop', label: 'Workshop' },
    { value: 'meetup', label: 'Meetup' },
    { value: 'other', label: 'Other' },
]

const categoryLabel = computed(() => {
    const found = categoryOptions.find(opt => opt.value === data.value.category)
    return found ? found.label : data.value.category || '-'
})
</script>

<style scoped>
.event-review-card {
    max-width: 700px;
    margin: 2rem auto;
    border: 1px solid #d9d9d9;
    border-radius: 16px;
    background: #fff;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
    overflow: hidden;
    display: flex;
    flex-direction: column;
}

.banner-section {
    width: 100%;
    height: 220px;
    background: #f3f3f3;
    display: flex;
    align-items: center;
    justify-content: center;
}

.banner-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.content-section {
    padding: 2rem 2rem 1rem 2rem;
}

.event-title {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 1.5rem;
}

.info-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 1.5rem;
    gap: 2rem;
}

.date-time,
.ticket-info {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 1.1rem;
    color: #333;
}

.location-section {
    margin-bottom: 1.5rem;
}

.location-label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 500;
    margin-bottom: 0.5rem;
}

.map-placeholder {
    width: 100%;
    height: 120px;
    background: #eaeaea;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}

.map-placeholder img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.host-section {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.host-avatar {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: #d9d9d9;
}

.host-info {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.host-name {
    font-weight: 600;
    font-size: 1.1rem;
}

.description-section {
    margin-bottom: 2rem;
}

.description-section h2 {
    font-size: 1.2rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
}
</style>
