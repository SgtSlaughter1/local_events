<template>
  <div class="max-w-2xl mx-auto p-6">
    <div v-if="loading" class="flex justify-center items-center min-h-[400px]">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary"></div>
    </div>

    <div v-else-if="error" class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
      <p class="text-red-600">{{ error }}</p>
    </div>

    <div v-else class="text-center">
      <div class="mb-8">
        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
          <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
          </svg>
        </div>
        <h2 class="text-2xl font-semibold mb-2">Payment Successful!</h2>
        <p class="text-gray-600">Thank you for your purchase.</p>
      </div>

      <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
        <h3 class="text-lg font-medium mb-4">Order Details</h3>
        <div class="space-y-3">
          <div class="flex justify-between text-gray-600">
            <span>Event</span>
            <span>{{ registration?.event?.title }}</span>
          </div>
          <div class="flex justify-between text-gray-600">
            <span>Number of Tickets</span>
            <span>{{ registration?.number_of_tickets }}</span>
          </div>
          <div class="flex justify-between text-gray-600">
            <span>Total Amount</span>
            <span>{{ formatPrice(registration?.payment_amount) }}</span>
          </div>
          <div class="flex justify-between text-gray-600">
            <span>Order ID</span>
            <span>{{ registration?.id }}</span>
          </div>
        </div>
      </div>

      <div class="space-x-4">
        <button 
          @click="viewTickets"
          class="bg-primary text-white px-6 py-2 rounded-lg font-medium hover:bg-primary-dark"
        >
          View My Tickets
        </button>
        <button 
          @click="goToEvents"
          class="bg-gray-100 text-gray-700 px-6 py-2 rounded-lg font-medium hover:bg-gray-200"
        >
          Browse More Events
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useRegistrationStore } from '@/stores/registration'
import { formatPrice } from '@/utils/formatters'

const route = useRoute()
const router = useRouter()
const registrationStore = useRegistrationStore()

const loading = computed(() => registrationStore.isLoading)
const error = computed(() => registrationStore.getError)
const registration = computed(() => registrationStore.getCurrentRegistration)

onMounted(async () => {
  try {
    await registrationStore.fetchRegistrationDetails(route.params.id)
  } catch (error) {
    console.error('Failed to load registration details:', error)
  }
})

const viewTickets = () => {
  router.push({ name: 'my-tickets' })
}

const goToEvents = () => {
  router.push({ name: 'events' })
}
</script>

<style scoped>
.payment-success {
    max-width: 600px;
    margin: 4rem auto;
    padding: 2rem;
    text-align: center;
}

.success-icon {
    width: 80px;
    height: 80px;
    background: #48bb78;
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.5rem;
    margin: 0 auto 2rem;
}

h2 {
    color: #2d3748;
    margin-bottom: 1rem;
}

p {
    color: #718096;
    margin-bottom: 2rem;
}

.ticket-details {
    background: #f7fafc;
    padding: 1.5rem;
    border-radius: 0.5rem;
    margin-bottom: 2rem;
}

.ticket-details h3 {
    margin-bottom: 1rem;
    color: #2d3748;
}

.ticket-item {
    display: flex;
    justify-content: space-between;
    margin-bottom: 0.5rem;
    color: #4a5568;
}

.total {
    display: flex;
    justify-content: space-between;
    font-weight: bold;
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid #e2e8f0;
}

.actions {
    display: flex;
    gap: 1rem;
    justify-content: center;
}

.download-btn,
.home-btn {
    padding: 0.75rem 1.5rem;
    border-radius: 0.25rem;
    font-weight: bold;
    cursor: pointer;
    transition: background-color 0.2s;
}

.download-btn {
    background: #4299e1;
    color: white;
    border: none;
}

.download-btn:hover {
    background: #3182ce;
}

.home-btn {
    display: inline-block;
    padding: 0.75rem 1.5rem;
    border-radius: 0.25rem;
    font-weight: bold;
    cursor: pointer;
    transition: background-color 0.2s;
    background: white;
    color: #4a5568;
    border: 1px solid #e2e8f0;
    text-decoration: none;
}

.home-btn:hover {
    background: #f7fafc;
}
</style>