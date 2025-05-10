<template>
    <div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="card shadow-lg" style="max-width: 420px; width: 100%;">
            <div class="card-header d-flex align-items-center justify-content-between bg-light border-bottom-0">
                <button class="btn btn-link p-0 me-2" @click="goBack" title="Back">
                    <i class="bi bi-arrow-left"></i>
                </button>
                <span class="fw-semibold">Order Summary</span>
                <button class="btn btn-link p-0 text-muted" @click="closeBooking" title="Close">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div class="card-body pb-0">
                <!-- Ticket Card -->
                <div class="ticket-summary-card card mb-4 border-0 bg-light">
                    <div class="card-body p-3 d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="fw-bold">Standard Ticket</div>
                            <div class="small text-muted">{{ attendeeName }}</div>
                            <div class="small text-muted">{{ attendeeEmail }}</div>
                        </div>
                        <div class="badge bg-primary fs-6">{{ formatPrice(ticketPrice) }}</div>
                    </div>
                </div>
                <!-- Summary Section -->
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Sub Total:</span>
                        <span>{{ formatPrice(subTotal) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Tax:</span>
                        <span>{{ formatPrice(tax) }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center border-top pt-3">
                        <span class="fw-bold">Order Total:</span>
                        <span class="fw-bold text-success fs-5">{{ formatPrice(orderTotal) }}</span>
                    </div>
                </div>
                <BaseButton type="button" variant="success" size="large" :full-width="true" class="mb-3"
                    @click="payNow">
                    <i class="bi bi-lock-fill me-2"></i> Pay Now
                </BaseButton>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import BaseButton from '@/components/Base/BaseButton.vue'
import { formatPrice } from '@/utils/formatters'

const router = useRouter()
const route = useRoute()

// Dummy data for now; replace with actual data as needed
const attendeeName = 'Andrea Gomes'
const attendeeEmail = 'andreagomes@example.com'
const ticketPrice = 200
const subTotal = 200
const tax = 11.8
const orderTotal = 211.8

const payNow = () => {
    // Implement payment logic or navigation
    router.push({ name: 'payment-success', params: { id: route.params.id } })
}
const goBack = () => {
    router.back()
}
const closeBooking = () => {
    router.push({ name: 'events' })
}
</script>