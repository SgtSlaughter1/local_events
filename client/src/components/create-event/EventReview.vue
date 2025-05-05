<template>
  <div class="event-review">
    <h3 class="review-title">Review Your Event Details</h3>
    <div class="review-section">
      <h4>Event Details</h4>
      <div class="review-row"><span class="review-label">Title:</span> <span>{{ data.title }}</span></div>
      <div class="review-row"><span class="review-label">Category:</span> <span>{{ categoryLabel }}</span></div>
      <div class="review-row"><span class="review-label">Description:</span> <span>{{ data.description }}</span></div>
    </div>
    <div class="review-section" v-if="data.bannerUrl">
      <h4>Banner</h4>
      <img :src="data.bannerUrl" :alt="data.bannerAlt || 'Event Banner'" class="review-banner" />
      <div v-if="data.bannerAlt" class="review-row"><span class="review-label">Alt Text:</span> <span>{{ data.bannerAlt }}</span></div>
    </div>
    <div class="review-section">
      <h4>Event Type</h4>
      <div class="review-row">
        <span>{{ data.eventType === 'ticketed' ? 'Ticketed Event' : 'Free Event' }}</span>
      </div>
    </div>
    <div class="review-section">
      <h4>Tickets</h4>
      <div v-if="data.tickets && data.tickets.length">
        <div v-for="(ticket, idx) in data.tickets" :key="idx" class="review-row">
          <span class="review-label">{{ ticket.name || 'Ticket' }}:</span>
          <span>{{ ticket.price ? `₦ ${Number(ticket.price).toLocaleString()}` : 'Free' }}</span>
        </div>
      </div>
      <div v-else class="review-row">No tickets added.</div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  modelValue: Object,
})

const data = computed(() => props.modelValue || {})

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
.event-review {
  display: flex;
  flex-direction: column;
  gap: 2rem;
}
.review-title {
  font-size: 1.3rem;
  font-weight: 700;
  margin-bottom: 1.5rem;
}
.review-section {
  background: #fafbfc;
  border-radius: 0.75rem;
  padding: 1.25rem 1rem;
  margin-bottom: 1rem;
}
.review-section h4 {
  font-size: 1.1rem;
  font-weight: 600;
  margin-bottom: 0.75rem;
}
.review-row {
  display: flex;
  gap: 0.5rem;
  margin-bottom: 0.5rem;
  font-size: 1rem;
}
.review-label {
  font-weight: 500;
  color: var(--primary-color);
  min-width: 110px;
}
.review-banner {
  max-width: 100%;
  border-radius: 0.5rem;
  margin-bottom: 0.5rem;
  box-shadow: 0 2px 8px rgba(0,0,0,0.06);
}
</style>
