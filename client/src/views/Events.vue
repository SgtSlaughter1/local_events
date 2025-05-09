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
                <input
                  type="text"
                  class="form-control form-control-lg"
                  placeholder="Search events..."
                  v-model="searchQuery"
                  @input="handleSearch"
                />
                <input type="text" class="form-control form-control-lg" placeholder="Location" />
                <BaseButton
                  variant="primary"
                  size="large"
                  type="button"
                >
                  <i class="fas fa-search"></i>
                </BaseButton>
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
              <div class="results-count">{{ filteredEvents.length }} events found</div>
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
                <EventCard :event="event" @view-details="showEventDetails" />
              </div>
            </div>

            <!-- Pagination -->
            <div
              v-if="!selectedEventId && eventStore.pagination.last_page > 1"
              class="d-flex justify-content-center mt-4"
            >
              <nav aria-label="Event pagination">
                <ul class="pagination">
                  <li
                    class="page-item"
                    :class="{ disabled: eventStore.pagination.current_page === 1 }"
                  >
                    <BaseButton
                      variant="secondary"
                      size="small"
                      class="page-link"
                      :disabled="eventStore.pagination.current_page === 1"
                      @click="changePage(eventStore.pagination.current_page - 1)"
                    >
                      Previous
                    </BaseButton>
                  </li>
                  <li
                    v-for="page in eventStore.pagination.last_page"
                    :key="page"
                    class="page-item"
                    :class="{ active: page === eventStore.pagination.current_page }"
                  >
                    <BaseButton
                      variant="secondary"
                      size="small"
                      class="page-link"
                      :class="{ active: page === eventStore.pagination.current_page }"
                      @click="changePage(page)"
                    >
                      {{ page }}
                    </BaseButton>
                  </li>
                  <li
                    class="page-item"
                    :class="{
                      disabled:
                        eventStore.pagination.current_page === eventStore.pagination.last_page,
                    }"
                  >
                    <BaseButton
                      variant="secondary"
                      size="small"
                      class="page-link"
                      :disabled="eventStore.pagination.current_page === eventStore.pagination.last_page"
                      @click="changePage(eventStore.pagination.current_page + 1)"
                    >
                      Next
                    </BaseButton>
                  </li>
                </ul>
              </nav>
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
import EventCard from '../components/Events/EventCard.vue'
import EventFilterSidebar from '../components/Events/EventFilterSidebar.vue'
import BaseButton from '../components/Base/BaseButton.vue'

const eventStore = useEventStore()
const searchQuery = ref('')
const sortBy = ref('relevance')
const selectedEventId = ref(null)

// Add onMounted hook to fetch events
onMounted(async () => {
  try {
    await eventStore.fetchEvents(1)
  } catch (error) {
    console.error('Error fetching events:', error)
  }
})

const filteredEvents = computed(() => {
  let events = eventStore.getEvents

  // Apply search filter
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    events = events.filter(
      (event) =>
        event.title.toLowerCase().includes(query) ||
        event.description.toLowerCase().includes(query),
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
}

const showEventDetails = (eventId) => {
  selectedEventId.value = eventId
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

const closeEventDetails = () => {
  selectedEventId.value = null
}

const changePage = async (page) => {
  if (page < 1 || page > eventStore.pagination.last_page) return
  try {
    await eventStore.fetchEvents(page)
    window.scrollTo({ top: 0, behavior: 'smooth' })
  } catch (error) {
    console.error('Error changing page:', error)
  }
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

/* Pagination styles */
.pagination {
  display: flex;
  gap: 0.5rem;
  list-style: none;
  padding: 0;
  margin: 0;
}

.page-item {
  margin: 0;
}

.page-link {
  padding: 0.5rem 1rem;
  border-radius: 0.375rem;
  text-decoration: none;
}

.page-item.active .page-link {
  background-color: var(--accent-color);
  color: var(--primary-color);
  border: none;
}

.page-item.disabled .page-link {
  opacity: 0.5;
  cursor: not-allowed;
}
</style>
