<template>
  <div class="event-card">
    <div class="position-relative">
      <img 
        :src="event.image || '/default-event-image.jpg'" 
        :alt="event.title"
        class="card-img-top"
      >
      <div class="position-absolute top-0 end-0 m-2">
        <span 
          class="badge"
          :class="statusClass"
        >
          {{ event.status }}
        </span>
      </div>
    </div>
    
    <div class="card-body">
      <h3 class="card-title">{{ event.title }}</h3>
      <p class="card-text text-muted">{{ event.description }}</p>
      
      <div class="d-flex align-items-center mb-3">
        <i class="fas fa-calendar-alt text-primary me-2"></i>
        <span class="event-date">{{ formatDate(event.date) }}</span>
      </div>
      
      <div class="d-flex align-items-center mb-3">
        <i class="fas fa-map-marker-alt text-primary me-2"></i>
        <span class="event-location">{{ event.location }}</span>
      </div>

      <div class="d-flex align-items-center justify-content-between mt-4">
        <span class="text-primary fw-bold">
          {{ event.price ? formatPrice(event.price) : 'Free' }}
        </span>
        <button 
          @click="$emit('view-details', event.id)"
          class="btn btn-primary"
        >
          View Details
        </button>
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
  }
})

defineEmits(['view-details'])

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
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  height: 100%;
  background: var(--white-color);
}

.event-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
}

.card-img-top {
  height: 200px;
  object-fit: cover;
}

.card-title {
  color: var(--primary-color);
  font-weight: 600;
  margin-bottom: 1rem;
}

.card-text {
  color: var(--text-color);
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.event-date, .event-location {
  color: var(--text-color);
  font-size: 0.9rem;
}

.badge {
  padding: 0.5rem 1rem;
  font-weight: 500;
}
</style> 