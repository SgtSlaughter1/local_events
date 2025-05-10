<template>
  <div class="container d-flex justify-content-center align-items-center min-vh-100">
    <div class="card shadow-lg" style="max-width: 420px; width: 100%;">
      <div class="card-header d-flex align-items-center justify-content-between bg-light border-bottom-0">
        <button class="btn btn-link p-0 me-2" @click="goBack" title="Back">
          <i class="bi bi-arrow-left"></i>
        </button>
        <span class="fw-semibold">Attendee Details</span>
        <button class="btn btn-link p-0 text-muted" @click="closeBooking" title="Close">
          <i class="bi bi-x-lg"></i>
        </button>
      </div>
      <div class="card-body pb-0">
        <div class="mb-3">
          <div class="d-flex justify-content-between align-items-center">
            <div class="fw-bold">{{ eventTitle }}</div>
            <div class="text-muted small">
              <i class="bi bi-calendar-event me-1"></i>{{ eventDate }}
            </div>
          </div>
        </div>
        <form @submit.prevent="continueToCheckout">
          <div class="mb-3">
            <BaseInput
              v-model="attendee.fullName"
              label="Full Name"
              placeholder="Enter Attendee's full name"
              :error="errors.fullName"
              required
            />
          </div>
          <div class="mb-3">
            <BaseInput
              v-model="attendee.email"
              label="E-mail"
              placeholder="Enter your email"
              :error="errors.email"
              required
            />
          </div>
          <div class="mb-3">
            <div class="input-group">
              <span class="input-group-text">+234</span>
              <input type="tel" class="form-control" v-model="attendee.phone" placeholder="Enter Attendee's Phone Number" required />
            </div>
            <div v-if="errors.phone" class="text-danger small mt-1">{{ errors.phone }}</div>
          </div>
          <div class="form-text mb-2">
            I accept the <a href="#" class="text-decoration-underline">Terms of Service</a> and have read the <a href="#" class="text-decoration-underline">Privacy Policy</a>
          </div>
          <div class="d-flex justify-content-between align-items-center border-top pt-3 mb-3">
            <span>Qty: <b>{{ quantity }}</b></span>
            <span>Total: <b class="text-success">{{ formatPrice(totalAmount) }}</b></span>
          </div>
          <BaseButton 
            type="submit"
            variant="primary"
            size="large"
            :full-width="true"
            class="mb-2"
          >
            Continue to Checkout <i class="bi bi-arrow-right ms-2"></i>
          </BaseButton>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import BaseButton from '@/components/Base/BaseButton.vue'
import BaseInput from '@/components/Base/BaseInput.vue'
import { formatPrice } from '@/utils/formatters'

const router = useRouter()
const route = useRoute()

// Dummy event data for now; replace with actual event data as needed
const eventTitle = 'Sound Of Christmas 2023'
const eventDate = 'Saturday, 2 December 2023'
const quantity = 1
const totalAmount = 200

const attendee = ref({
  fullName: '',
  email: '',
  phone: ''
})

const errors = ref({
  fullName: '',
  email: '',
  phone: ''
})

const validate = () => {
  let valid = true
  errors.value = { fullName: '', email: '', phone: '' }
  if (!attendee.value.fullName) {
    errors.value.fullName = 'Full name is required.'
    valid = false
  }
  if (!attendee.value.email) {
    errors.value.email = 'Email is required.'
    valid = false
  }
  if (!attendee.value.phone) {
    errors.value.phone = 'Phone is required.'
    valid = false
  }
  return valid
}

const continueToCheckout = () => {
  if (!validate()) return
  // Save attendee details and proceed
  // You can emit or use a store to pass data to the next step
  router.push({ name: 'summary', params: { id: route.params.id } })
}

const goBack = () => {
  router.back()
}
const closeBooking = () => {
  router.push({ name: 'events' })
}
</script> 