<template>
    <div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="card shadow-lg" style="max-width: 420px; width: 100%;">
            <div class="card-header d-flex align-items-center justify-content-between bg-light border-bottom-0">
                <span class="fw-semibold">Select Tickets</span>
                <button class="btn btn-link p-0 text-muted" @click="closeBooking" title="Close">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div class="card-body pb-0">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <div class="fw-bold">Standard Ticket</div>
                        <div class="text-muted small">{{ availableTickets?.available_tickets }} tickets available</div>
                    </div>
                    <div class="d-flex align-items-center bg-light rounded-pill px-3 py-1">
                        <BaseButton @click="decreaseQuantity" :disabled="quantity <= 1" variant="secondary"
                            size="small">-</BaseButton>
                        <span class="mx-2 fw-semibold">{{ quantity }}</span>
                        <BaseButton @click="increaseQuantity"
                            :disabled="quantity >= availableTickets?.available_tickets" variant="secondary"
                            size="small">+</BaseButton>
                    </div>
                </div>
                <div class="d-flex justify-content-between align-items-center border-top pt-3 mb-3">
                    <span>Qty: <b>{{ quantity }}</b></span>
                    <span>Total: <b class="text-success">{{ formatPrice(totalAmount) }}</b></span>
                </div>
                <BaseButton @click="proceedToCheckout" :disabled="!canProceed" variant="primary" size="large"
                    :full-width="true" class="mb-2">
                    Proceed <i class="bi bi-arrow-right ms-2"></i>
                </BaseButton>
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

const route = useRoute()
const router = useRouter()
const registrationStore = useRegistrationStore()

const quantity = ref(1)
const loading = computed(() => registrationStore.isLoading)
const error = computed(() => registrationStore.getError)
const availableTickets = computed(() => registrationStore.getAvailableTickets)

const totalAmount = computed(() => {
    if (!availableTickets.value?.price) return 0
    return quantity.value * availableTickets.value.price
})

const canProceed = computed(() => {
    return quantity.value > 0 && availableTickets.value?.available_tickets > 0
})

onMounted(async () => {
    try {
        await registrationStore.fetchAvailableTickets(route.params.id)
    } catch (error) {
        console.error('Failed to fetch available tickets:', error)
    }
})

const increaseQuantity = () => {
    if (quantity.value < availableTickets.value?.available_tickets) {
        quantity.value++
    }
}

const decreaseQuantity = () => {
    if (quantity.value > 1) {
        quantity.value--
    }
}

const proceedToCheckout = async () => {
    try {
        const registrationData = {
            number_of_tickets: quantity.value,
            payment_method: 'card' // Default payment method
        }

        const response = await registrationStore.registerForEvent(route.params.id, registrationData)
        localStorage.setItem('currentRegistration', JSON.stringify(response.data))
        router.push({ name: 'attendee', params: { id: response.data.id } })
    } catch (error) {
        console.error('Failed to proceed to checkout:', error)
    }
}

const closeBooking = () => {
    router.push({ name: 'events' })
}
</script>

<style scoped>
.ticket-booking {
    max-width: 800px;
    margin: 2rem auto;
    padding: 2rem;
    background: white;
    border-radius: 1rem;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.ticket-booking h2 {
    color: #2c3e50;
    font-size: 1.8rem;
    margin-bottom: 1.5rem;
    font-weight: 600;
}

.ticket-booking-card {
    max-width: 400px;
    margin: 3rem auto;
    background: #fff;
    border-radius: 1rem;
    box-shadow: 0 2px 16px rgba(0, 0, 0, 0.07);
    padding: 0;
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

.card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: #f7f7f7;
    padding: 1.25rem 1.5rem 1rem 1.5rem;
    border-bottom: 1px solid #f0f0f0;
}

.card-title {
    font-size: 1.15rem;
    font-weight: 600;
    color: #222;
}

.close-btn {
    background: none;
    border: none;
    font-size: 1.5rem;
    color: #888;
    cursor: pointer;
    transition: color 0.2s;
}

.close-btn:hover {
    color: #222;
}

.ticket-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1.5rem 1.5rem 1rem 1.5rem;
    border-bottom: 1px solid #f0f0f0;
}

.ticket-info {
    display: flex;
    flex-direction: column;
}

.ticket-type {
    font-weight: 500;
    font-size: 1.1rem;
    margin-bottom: 0.25rem;
}

.ticket-price {
    color: #888;
    font-size: 1rem;
}

.quantity-selector {
    display: flex;
    align-items: center;
    background: #f4f4f4;
    border-radius: 999px;
    padding: 0.25rem 0.75rem;
    gap: 0.5rem;
}

.quantity {
    font-size: 1.1rem;
    font-weight: 500;
    min-width: 2rem;
    text-align: center;
}

.summary-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #f7f7f7;
    padding: 1rem 1.5rem;
    font-size: 1.05rem;
    border-top: 1px solid #f0f0f0;
    border-bottom: 1px solid #f0f0f0;
}

.total-price {
    color: #1db954;
    font-weight: 600;
}

.proceed-btn {
    margin: 1.5rem 1.5rem 1.25rem 1.5rem;
}

.arrow {
    margin-left: 0.5rem;
    font-size: 1.2em;
}

@media (max-width: 600px) {
    .ticket-booking-card {
        max-width: 98vw;
        margin: 1rem auto;
    }

    .card-header,
    .ticket-row,
    .summary-bar,
    .proceed-btn {
        padding-left: 1rem;
        padding-right: 1rem;
    }
}
</style>