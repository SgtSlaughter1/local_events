<template>
    <div class="max-w-4xl mx-auto p-6">
        <div v-if="loading" class="flex justify-center items-center min-h-[400px]">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary"></div>
        </div>

        <div v-else-if="error" class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
            <p class="text-red-600">{{ error }}</p>
        </div>

        <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Ticket Selection -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-2xl font-semibold mb-6">Select Tickets</h2>
                
                <div class="space-y-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="font-medium">Standard Ticket</h3>
                            <p class="text-gray-600 text-sm">{{ availableTickets?.available_tickets }} tickets available</p>
                        </div>
                        <div class="text-xl font-semibold">{{ formatPrice(availableTickets?.price) }}</div>
                    </div>

                    <div class="flex items-center space-x-4">
                        <button 
                            @click="decreaseQuantity" 
                            :disabled="quantity <= 1"
                            class="w-10 h-10 rounded-full border border-gray-300 flex items-center justify-center hover:bg-gray-50 disabled:opacity-50"
                        >
                            -
                        </button>
                        <span class="text-lg font-medium">{{ quantity }}</span>
                        <button 
                            @click="increaseQuantity" 
                            :disabled="quantity >= availableTickets?.available_tickets"
                            class="w-10 h-10 rounded-full border border-gray-300 flex items-center justify-center hover:bg-gray-50 disabled:opacity-50"
                        >
                            +
                        </button>
                    </div>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-2xl font-semibold mb-6">Order Summary</h2>
                
                <div class="space-y-4">
                    <div class="flex justify-between text-gray-600">
                        <span>Number of Tickets</span>
                        <span>{{ quantity }}</span>
                    </div>
                    <div class="flex justify-between text-gray-600">
                        <span>Price per Ticket</span>
                        <span>{{ formatPrice(availableTickets?.price) }}</span>
                    </div>
                    <div class="border-t pt-4">
                        <div class="flex justify-between font-semibold text-lg">
                            <span>Total</span>
                            <span>{{ formatPrice(totalAmount) }}</span>
                        </div>
                    </div>
                </div>

                <button 
                    @click="proceedToCheckout"
                    :disabled="!canProceed"
                    class="w-full mt-6 bg-primary text-white py-3 rounded-lg font-medium hover:bg-primary-dark disabled:opacity-50"
                >
                    Proceed to Checkout
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useRegistrationStore } from '@/stores/registration'
import { formatPrice } from '@/utils/formatters'

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
        
        // Save registration data to localStorage for checkout
        localStorage.setItem('currentRegistration', JSON.stringify(response.data))
        
        // Navigate to checkout
        router.push({
            name: 'checkout',
            params: { id: response.data.id }
        })
    } catch (error) {
        console.error('Failed to proceed to checkout:', error)
    }
}
</script>

<style scoped>
.ticket-booking {
    max-width: 800px;
    margin: 0 auto;
    padding: 2rem;
}

.booking-header {
    text-align: center;
    margin-bottom: 2rem;
}

.booking-header h2 {
    margin-bottom: 0.5rem;
}

.booking-content {
    display: grid;
    gap: 2rem;
}

.ticket-selection {
    background: var(--card-bg);
    border-radius: 0.5rem;
    padding: 1.5rem;
    box-shadow: var(--card-shadow);
}

.ticket-types {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.ticket-type-card {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem;
    background: var(--bg-color);
    border-radius: 0.5rem;
}

.ticket-info h4 {
    margin: 0 0 0.5rem 0;
}

.ticket-info .price {
    font-size: 1.25rem;
    font-weight: bold;
    color: var(--primary-color);
    margin: 0.5rem 0;
}

.ticket-info .description {
    color: var(--text-muted);
    margin: 0;
}

.quantity-controls {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.quantity-btn {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    border: 1px solid var(--border-color);
    background: var(--card-bg);
    color: var(--text-color);
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s;
}

.quantity-btn:hover:not(:disabled) {
    background: var(--primary-color);
    color: white;
    border-color: var(--primary-color);
}

.quantity-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.quantity {
    font-size: 1.1rem;
    font-weight: bold;
    min-width: 2rem;
    text-align: center;
}

.order-summary {
    background: var(--card-bg);
    border-radius: 0.5rem;
    padding: 1.5rem;
    box-shadow: var(--card-shadow);
}

.summary-content {
    margin-top: 1rem;
}

.no-tickets {
    text-align: center;
    color: var(--text-muted);
    padding: 1rem;
}

.ticket-summary {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.summary-item {
    display: flex;
    justify-content: space-between;
    padding: 0.5rem 0;
    border-bottom: 1px solid var(--border-color);
}

.summary-total {
    display: flex;
    justify-content: space-between;
    padding: 1rem 0 0;
    font-weight: bold;
    font-size: 1.1rem;
}

.booking-actions {
    display: flex;
    justify-content: space-between;
    margin-top: 2rem;
    gap: 1rem;
}

.loading-spinner {
    display: flex;
    justify-content: center;
    padding: 2rem;
}

@media (max-width: 768px) {
    .ticket-booking {
        padding: 1rem;
    }

    .ticket-type-card {
        flex-direction: column;
        gap: 1rem;
    text-align: center;
    }

    .booking-actions {
        flex-direction: column;
}

    .booking-actions .base-button {
        width: 100%;
    }
}
</style>