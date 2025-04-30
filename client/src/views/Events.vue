<template>
    <div class="events-page">
        <div class="hero" :class="{ 'hero-minimized': selectedEventId }">
            <div class="container">
                <div class="hero-content text-center">
                    <h1 class="display-4 fw-bold mb-4">Explore a world of events. Find what excites you!</h1>

                    <!-- Search and Location Section -->
                    <div class="row justify-content-center mt-5">
                        <div class="col-md-8">
                            <div class="input-group">
                                <input type="text" class="form-control form-control-lg" placeholder="Search events..."
                                    v-model="searchQuery" @input="handleSearch">
                                <input type="text" class="form-control form-control-lg" placeholder="Location">
                                <button class="btn btn-primary btn-lg" type="button">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Events Content Section -->
        <div class="container py-5">
            <div class="row">
                <!-- Filters Sidebar -->
                <div class="col-md-3" v-if="!selectedEventId">
                    <EventFilterSidebar />
                </div>

                <!-- Events Grid or Detailed View -->
                <div :class="selectedEventId ? 'col-12' : 'col-md-9'">
                    <div v-if="!selectedEventId">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div class="results-count">
                                {{ filteredEvents.length }} events found
                            </div>
                            <div class="sort-by">
                                <select class="form-select" v-model="sortBy">
                                    <option value="relevance">Relevance</option>
                                    <option value="date">Date</option>
                                    <option value="price">Price</option>
                                </select>
                            </div>
                        </div>

                        <div v-if="isLoading" class="text-center py-5">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>

                        <div v-else-if="error" class="alert alert-danger" role="alert">
                            {{ error }}
                        </div>

                        <div v-else-if="filteredEvents.length === 0" class="text-center py-5">
                            <h3>No events found</h3>
                            <p class="text-muted">Try adjusting your filters or search criteria</p>
                        </div>

                        <div v-else class="row g-4">
                            <div v-for="event in filteredEvents" :key="event.id" class="col-md-6 col-lg-4">
                                <EventCard 
                                    :event="event"
                                    @view-details="showEventDetails"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- Detailed Event View -->
                    <div v-else>
                        <EventCard 
                            v-if="selectedEvent"
                            :event="selectedEvent"
                            :is-detailed="true"
                            @close-details="closeEventDetails"
                            @register="handleEventRegistration"
                        />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useEventStore } from '../stores/event'
import EventCard from '../components/EventCard.vue'
import EventFilterSidebar from '../components/EventFilterSidebar.vue'

const eventStore = useEventStore()
const searchQuery = ref('')
const sortBy = ref('relevance')
const selectedEventId = ref(null)

// Add onMounted hook to fetch events
onMounted(async () => {
    try {
        await eventStore.fetchEvents()
        console.log('Fetched events:', eventStore.getEvents)
    } catch (error) {
        console.error('Error fetching events:', error)
    }
})

const filteredEvents = computed(() => {
    let events = eventStore.getEvents
    
    // Apply search filter
    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase()
        events = events.filter(event => 
            event.title.toLowerCase().includes(query) ||
            event.description.toLowerCase().includes(query)
        )
    }

    // Apply sorting
    return [...events].sort((a, b) => {
        switch (sortBy.value) {
            case 'date':
                return new Date(a.date) - new Date(b.date)
            case 'price':
                return a.price - b.price
            default:
                return 0
        }
    })
})

const selectedEvent = computed(() => {
    if (!selectedEventId.value) return null
    return eventStore.getEventById(selectedEventId.value)
})

const isLoading = computed(() => eventStore.isLoading)
const error = computed(() => eventStore.getError)

const handleSearch = () => {
    selectedEventId.value = null
    console.log('Searching for:', searchQuery.value)
}

const showEventDetails = (eventId) => {
    selectedEventId.value = eventId
    window.scrollTo({ top: 0, behavior: 'smooth' })
}

const closeEventDetails = () => {
    selectedEventId.value = null
}

const handleEventRegistration = (eventId) => {
    console.log('Register for event:', eventId)
    // TODO: Implement registration logic
}
</script>

<style scoped>
.events-page {
    min-height: 100vh;
    background-color: var(--light-color);
}

.hero {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
    color: var(--white-color);
    padding: 5rem 0;
    transition: padding 0.3s ease;
}

.hero-minimized {
    padding: 2rem 0;
}

.form-control {
    border: none;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.form-control:focus {
    box-shadow: 0 0 0 0.25rem rgba(255, 224, 71, 0.25);
}

.sort-by {
    width: 150px;
}

.results-count {
    color: var(--gray-600);
    font-weight: 500;
}
</style>