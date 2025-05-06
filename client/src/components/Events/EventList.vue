<template>
  <div class="container py-5">
    <!-- Loading State -->
    <div
      v-if="eventStore.isLoading"
      class="d-flex justify-content-center align-items-center"
      style="min-height: 200px"
    >
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
    </div>

    <!-- Error State -->
    <div v-else-if="eventStore.getError" class="alert alert-danger text-center" role="alert">
      {{ eventStore.getError }}
    </div>

    <!-- Empty State -->
    <div v-else-if="!events?.length" class="text-center text-muted p-4">
      <i class="fas fa-calendar-times fa-3x mb-3"></i>
      <p class="lead">No events found.</p>
    </div>

    <!-- Events Grid -->
    <div v-else class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
      <div class="col" v-for="event in events" :key="event.id">
        <EventCard :event="event" @view-details="navigateToEvent" />
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useEventStore } from '@/stores/event'
import EventCard from './EventCard.vue'

const router = useRouter()
const eventStore = useEventStore()

// Computed property for events
const events = computed(() => eventStore.getEvents)

const navigateToEvent = (eventId) => {
  router.push({ name: 'event-details', params: { id: eventId } })
}

onMounted(async () => {
  try {
    await eventStore.fetchEvents()
  } catch (error) {
    console.error('Failed to fetch events:', error)
  }
})
</script>

<style scoped>
.spinner-border {
  width: 3rem;
  height: 3rem;
  color: var(--primary-color);
}
</style>
