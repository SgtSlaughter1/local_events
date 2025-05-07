<template>
    <div class="ticket-booking">
        <div class="booking-header">
            <h2>Book Tickets for {{ event?.title }}</h2>
            <p class="text-muted">{{ formatDate(event?.start_date) }}</p>
        </div>

        <div class="booking-content">
            <!-- Ticket Selection -->
            <div class="ticket-selection">
                <h3>Select Tickets</h3>
                <div v-if="loading" class="loading-spinner">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
                <div v-else-if="error" class="alert alert-danger">
                    {{ error }}
                </div>
                <div v-else class="ticket-types">
                    <div class="ticket-type-card">
                        <div class="ticket-info">
                            <h4>Standard Ticket</h4>
                            <p class="price">{{ formatPrice(ticketPrice) }}</p>
                            <p class="description">Access to the event</p>
                        </div>
                        <div class="ticket-quantity">
                            <div class="quantity-controls">
                                <button 
                                    class="quantity-btn" 
                                    @click="decreaseQuantity"
                                    :disabled="selectedQuantity === 0"
                                >
                                    <i class="fas fa-minus"></i>
                                </button>
                                <span class="quantity">{{ selectedQuantity }}</span>
                                <button 
                                    class="quantity-btn" 
                                    @click="increaseQuantity"
                                    :disabled="selectedQuantity >= (event?.capacity || 100)"
                                >
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="order-summary">
                <h3>Order Summary</h3>
                <div class="summary-content">
                    <div v-if="selectedQuantity === 0" class="no-tickets">
                        <p>No tickets selected</p>
                    </div>
                    <template v-else>
                        <div class="ticket-summary">
                            <div class="summary-item">
                                <span>Standard Ticket x {{ selectedQuantity }}</span>
                                <span>{{ formatPrice(ticketPrice * selectedQuantity) }}</span>
                            </div>
                        </div>
                        <div class="summary-total">
                            <span>Total</span>
                            <span>{{ formatPrice(ticketPrice * selectedQuantity) }}</span>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- Navigation Buttons -->
        <div class="booking-actions">
            <BaseButton 
                variant="secondary" 
                @click="$router.push(`/events/${event?.id}`)"
            >
                Back to Event
            </BaseButton>
            <BaseButton 
                variant="primary" 
                :disabled="selectedQuantity === 0"
                @click="proceedToCheckout"
            >
                Proceed to Checkout
            </BaseButton>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useEventStore } from '@/stores/event'
import BaseButton from '@/components/Base/BaseButton.vue'
import { formatDate, formatPrice } from '@/utils/formatters'

const route = useRoute()
const router = useRouter()
const eventStore = useEventStore()
const loading = ref(true)
const error = ref(null)
const event = ref(null)
const selectedQuantity = ref(0)
const ticketPrice = ref(0)

// Load booking data from local storage if exists
onMounted(async () => {
    try {
        loading.value = true
        const eventId = route.params.id
        event.value = await eventStore.fetchEvent(eventId)
        
        // Load saved booking data
        const savedBooking = localStorage.getItem(`booking_${eventId}`)
        if (savedBooking) {
            const bookingData = JSON.parse(savedBooking)
            selectedQuantity.value = bookingData.tickets[0]?.quantity || 0
            ticketPrice.value = bookingData.eventPrice || event.value.price
        } else {
            // If no saved booking, initialize with event's standard ticket
            ticketPrice.value = event.value.price
            selectedQuantity.value = 0
        }
    } catch (err) {
        error.value = 'Failed to load ticket information'
        console.error('Error:', err)
    } finally {
        loading.value = false
    }
})

// Methods
const increaseQuantity = () => {
    if (selectedQuantity.value < (event.value?.capacity || 100)) {
        selectedQuantity.value++
        saveBookingData()
    }
}

const decreaseQuantity = () => {
    if (selectedQuantity.value > 0) {
        selectedQuantity.value--
        saveBookingData()
    }
}

const saveBookingData = () => {
    const bookingData = {
        eventId: event.value.id,
        eventTitle: event.value.title,
        eventDate: event.value.start_date,
        eventPrice: ticketPrice.value,
        tickets: [{
            id: 1,
            name: 'Standard Ticket',
            price: ticketPrice.value,
            quantity: selectedQuantity.value,
            available_quantity: event.value.capacity || 100
        }],
        totalAmount: ticketPrice.value * selectedQuantity.value,
        totalTickets: selectedQuantity.value,
        step: 'booking'
    }
    localStorage.setItem(`booking_${event.value.id}`, JSON.stringify(bookingData))
}

const proceedToCheckout = () => {
    if (selectedQuantity.value > 0) {
        router.push({
            name: 'checkout',
            params: { id: event.value.id }
        })
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