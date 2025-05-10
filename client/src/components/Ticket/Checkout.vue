<template>
    <div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="card shadow-lg" style="max-width: 420px; width: 100%;">
            <div class="card-header d-flex align-items-center justify-content-between bg-light border-bottom-0">
                <button class="btn btn-link p-0 me-2" @click="goBack" title="Back">
                    <i class="bi bi-arrow-left"></i>
                </button>
                <span class="fw-semibold">Payment Details</span>
                <button class="btn btn-link p-0 text-muted" @click="closeBooking" title="Close">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div class="card-body pb-0">
                <form @submit.prevent="processPayment">
                    <div class="mb-3">
                        <BaseInput v-model="paymentDetails.cardNumber" label="Card Number"
                            placeholder="4242 4242 4242 4242" :error="errors.cardNumber" required />
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <BaseInput v-model="paymentDetails.expiryDate" label="Expiry Date" placeholder="MM/YY"
                                :error="errors.expiryDate" required />
                        </div>
                        <div class="col">
                            <BaseInput v-model="paymentDetails.cvc" label="CVC" placeholder="123" :error="errors.cvc"
                                required />
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Event</span>
                            <span>{{ registration?.event?.title }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Number of Tickets</span>
                            <span>{{ registration?.number_of_tickets }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Price per Ticket</span>
                            <span>{{ formatPrice(registration?.event?.price) }}</span>
                        </div>
                        <div class="d-flex justify-content-between border-top pt-2 mt-2">
                            <span class="fw-bold">Total</span>
                            <span class="fw-bold text-success">{{ formatPrice(registration?.payment_amount) }}</span>
                        </div>
                    </div>
                    <BaseButton type="submit" :disabled="processing" variant="primary" size="large" :full-width="true"
                        :loading="processing" class="mb-2">
                        {{ processing ? 'Processing...' : 'Pay Now' }}
                    </BaseButton>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useRegistrationStore } from '@/stores/registration'
import { formatPrice } from '@/utils/formatters'
import BaseButton from '@/components/Base/BaseButton.vue'
import BaseInput from '@/components/Base/BaseInput.vue'

const route = useRoute()
const router = useRouter()
const registrationStore = useRegistrationStore()

const paymentDetails = ref({
    cardNumber: '',
    expiryDate: '',
    cvc: ''
})

const errors = ref({
    cardNumber: '',
    expiryDate: '',
    cvc: ''
})

const processing = ref(false)
const loading = computed(() => registrationStore.isLoading)
const error = computed(() => registrationStore.getError)
const registration = computed(() => registrationStore.getCurrentRegistration)

onMounted(async () => {
    try {
        // Load registration data from localStorage
        const savedRegistration = localStorage.getItem('currentRegistration')
        if (!savedRegistration) {
            router.push({ name: 'events' })
            return
        }
        // Fetch registration details
        await registrationStore.fetchRegistrationDetails(route.params.id)
    } catch (error) {
        console.error('Failed to load registration details:', error)
    }
})

const processPayment = async () => {
    try {
        processing.value = true
        const paymentData = {
            payment_method: 'card',
            payment_details: {
                card_number: paymentDetails.value.cardNumber,
                expiry_date: paymentDetails.value.expiryDate,
                cvc: paymentDetails.value.cvc
            }
        }
        await registrationStore.processPayment(route.params.id, paymentData)
        localStorage.removeItem('currentRegistration')
        router.push({ name: 'payment-success', params: { id: route.params.id } })
    } catch (error) {
        console.error('Payment processing failed:', error)
    } finally {
        processing.value = false
    }
}
const goBack = () => {
    router.back()
}
const closeBooking = () => {
    router.push({ name: 'events' })
}
</script>

<style scoped>
.checkout {
    max-width: 1000px;
    margin: 2rem auto;
    padding: 2rem;
    background: white;
    border-radius: 1rem;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.checkout h2 {
    color: #2c3e50;
    font-size: 1.8rem;
    margin-bottom: 1.5rem;
    font-weight: 600;
}

.checkout-content {
    display: grid;
    grid-template-columns: 1fr 400px;
    gap: 2rem;
}

.payment-form {
    background: #f8fafc;
    padding: 1.5rem;
    border-radius: 0.75rem;
}

.payment-form h3 {
    color: #1e293b;
    font-size: 1.4rem;
    margin-bottom: 1rem;
    font-weight: 500;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    color: #475569;
    font-weight: 500;
}

.form-group input {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #e2e8f0;
    border-radius: 0.5rem;
    font-size: 1rem;
    transition: all 0.2s ease;
}

.form-group input:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

.order-summary {
    background: #f1f5f9;
    padding: 1.5rem;
    border-radius: 0.75rem;
    height: fit-content;
}

.order-summary h3 {
    color: #1e293b;
    font-size: 1.4rem;
    margin-bottom: 1rem;
    font-weight: 500;
}

.event-details {
    margin-bottom: 1.5rem;
    padding-bottom: 1.5rem;
    border-bottom: 2px solid #e2e8f0;
}

.event-details h4 {
    color: #1e293b;
    font-size: 1.2rem;
    margin-bottom: 0.5rem;
    font-weight: 500;
}

.event-details p {
    color: #64748b;
    margin: 0.25rem 0;
}

.summary-item {
    display: flex;
    justify-content: space-between;
    margin-bottom: 0.75rem;
    color: #475569;
}

.summary-item.total {
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 2px solid #e2e8f0;
    font-weight: 600;
    color: #1e293b;
    font-size: 1.1rem;
}

.pay-button {
    width: 100%;
    padding: 1rem;
    background: #3b82f6;
    color: white;
    border: none;
    border-radius: 0.5rem;
    font-size: 1.1rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
    margin-top: 1.5rem;
}

.pay-button:hover {
    background: #2563eb;
    transform: translateY(-1px);
}

.pay-button:disabled {
    background: #94a3b8;
    cursor: not-allowed;
    transform: none;
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

@media (max-width: 768px) {
    .checkout {
        margin: 1rem;
        padding: 1rem;
    }

    .checkout-content {
        grid-template-columns: 1fr;
    }

    .checkout h2 {
        font-size: 1.5rem;
    }

    .payment-form h3,
    .order-summary h3 {
        font-size: 1.2rem;
    }

    .form-row {
        grid-template-columns: 1fr;
        gap: 0;
    }
}
</style>