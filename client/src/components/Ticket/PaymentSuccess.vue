<template>
  <div class="container d-flex justify-content-center align-items-center min-vh-100">
    <div class="card shadow-lg" style="max-width: 420px; width: 100%;">
      <div class="card-header d-flex align-items-center justify-content-between bg-light border-bottom-0">
        <span class="fw-semibold">Payment Success</span>
        <button class="btn btn-link p-0 text-muted" @click="closeBooking" title="Close">
          <i class="bi bi-x-lg"></i>
        </button>
      </div>
      <div class="card-body text-center">
        <div class="mb-4">
          <div class="mx-auto mb-3" style="width: 64px; height: 64px;">
            <div class="bg-success bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width: 64px; height: 64px;">
              <i class="bi bi-check2-circle text-success fs-1"></i>
            </div>
          </div>
          <h2 class="fw-bold mb-2">Payment Successful!</h2>
          <p class="text-muted">Thank you for your purchase.</p>
        </div>
        <div class="bg-light rounded p-3 mb-4 text-start">
          <div class="fw-bold">Order Details</div>
          <div class="small text-muted">Event: {{ registration?.event?.title }}</div>
          <div class="small text-muted">Tickets: {{ registration?.number_of_tickets }}</div>
          <div class="small text-muted">Total: {{ formatPrice(registration?.payment_amount) }}</div>
          <div class="small text-muted">Order ID: {{ registration?.id }}</div>
        </div>
        <div class="d-flex gap-2 justify-content-center">
          <BaseButton 
            @click="viewTickets"
            variant="primary"
            size="large"
            :full-width="false"
          >
            View My Tickets
          </BaseButton>
          <BaseButton 
            @click="goToEvents"
            variant="secondary"
            size="large"
            :full-width="false"
          >
            Browse More Events
          </BaseButton>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useRegistrationStore } from '@/stores/registration'
import { formatPrice } from '@/utils/formatters'
import BaseButton from '@/components/Base/BaseButton.vue'

const route = useRoute()
const router = useRouter()
const registrationStore = useRegistrationStore()

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

const closeBooking = () => {
  router.push({ name: 'events' })
}
</script>

<style scoped>
.payment-success {
  max-width: 800px;
  margin: 2rem auto;
  padding: 2rem;
  background: white;
  border-radius: 1rem;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  text-align: center;
}

.success-icon {
  width: 80px;
  height: 80px;
  margin: 0 auto 1.5rem;
  background: #dcfce7;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #16a34a;
  font-size: 2.5rem;
}

.payment-success h2 {
  color: #2c3e50;
  font-size: 1.8rem;
  margin-bottom: 1rem;
  font-weight: 600;
}

.payment-success p {
  color: #64748b;
  font-size: 1.1rem;
  margin-bottom: 2rem;
}

.order-details {
  background: #f8fafc;
  padding: 1.5rem;
  border-radius: 0.75rem;
  margin: 2rem 0;
  text-align: left;
}

.order-details h3 {
  color: #1e293b;
  font-size: 1.4rem;
  margin-bottom: 1rem;
  font-weight: 500;
}

.detail-item {
  display: flex;
  justify-content: space-between;
  margin-bottom: 0.75rem;
  color: #475569;
}

.detail-item.total {
  margin-top: 1rem;
  padding-top: 1rem;
  border-top: 2px solid #e2e8f0;
  font-weight: 600;
  color: #1e293b;
  font-size: 1.1rem;
}

.action-buttons {
  display: flex;
  gap: 1rem;
  justify-content: center;
  margin-top: 2rem;
}

.view-tickets-btn,
.browse-events-btn {
  padding: 0.75rem 1.5rem;
  border-radius: 0.5rem;
  font-size: 1rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
  text-decoration: none;
}

.view-tickets-btn {
  background: #3b82f6;
  color: white;
  border: none;
}

.view-tickets-btn:hover {
  background: #2563eb;
  transform: translateY(-1px);
}

.browse-events-btn {
  background: white;
  color: #3b82f6;
  border: 2px solid #3b82f6;
}

.browse-events-btn:hover {
  background: #f8fafc;
  transform: translateY(-1px);
}

.error-message {
  background: #fee2e2;
  color: #dc2626;
  padding: 1rem;
  border-radius: 0.5rem;
  margin-bottom: 1rem;
  font-weight: 500;
}

.loading-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(255, 255, 255, 0.9);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.loading-spinner {
  width: 50px;
  height: 50px;
  border: 4px solid #e2e8f0;
  border-top-color: #3b82f6;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

@media (max-width: 640px) {
  .payment-success {
    margin: 1rem;
    padding: 1rem;
  }

  .payment-success h2 {
    font-size: 1.5rem;
  }

  .order-details h3 {
    font-size: 1.2rem;
  }

  .action-buttons {
    flex-direction: column;
  }

  .view-tickets-btn,
  .browse-events-btn {
    width: 100%;
  }
}
</style>