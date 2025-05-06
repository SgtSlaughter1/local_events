<template>
    <div class="ticket-booking">
        <div class="ticket-booking__header">
            <h2>Book Tickets for {{ event?.title }}</h2>
        </div>

        <div class="ticket-booking__content">
            <!-- Ticket Selection -->
            <div class="ticket-types">
                <div v-for="ticket in tickets" :key="ticket.id" class="ticket-type">
                    <div class="ticket-type__info">
                        <h3>{{ ticket.name }}</h3>
                        <p class="price">₦{{ ticket.price.toFixed(2) }}</p>
                        <p class="description">{{ ticket.description }}</p>
                    </div>
                    <div class="ticket-type__quantity">
                        <button @click="decreaseQuantity(ticket)" :disabled="ticket.quantity <= 0" class="quantity-btn">
                            -
                        </button>
                        <span class="quantity">{{ ticket.quantity }}</span>
                        <button @click="increaseQuantity(ticket)" :disabled="ticket.quantity >= ticket.available"
                            class="quantity-btn">
                            +
                        </button>
                    </div>
                </div>
            </div>

            <!-- Booking Summary -->
            <div class="booking-summary">
                <h3>Booking Summary</h3>
                <div class="summary-items">
                    <div v-for="ticket in selectedTickets" :key="ticket.id" class="summary-item">
                        <span>{{ ticket.name }} x {{ ticket.quantity }}</span>
                        <span>₦{{ (ticket.price * ticket.quantity).toFixed(2) }}</span>
                    </div>
                </div>
                <div class="total">
                    <span>Total:</span>
                    <span>₦{{ totalAmount.toFixed(2) }}</span>
                </div>
                <router-link 
                    :to="{
                        name: 'checkout',
                        params: { eventId: event?.id },
                        state: { tickets: selectedTickets }
                    }"
                    :class="{ 'checkout-btn': true, 'disabled': !hasSelectedTickets }"
                    :disabled="!hasSelectedTickets"
                >
                    Proceed to Checkout
                </router-link>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useEventStore } from '../stores/event'

const route = useRoute()
const router = useRouter()
const eventStore = useEventStore()
const event = ref(null)

onMounted(async () => {
    try {
        const eventId = route.params.id
        event.value = await eventStore.fetchEvent(eventId)
    } catch (error) {
        console.error('Error fetching event:', error)
    }
})

// Use event's price for tickets
const tickets = ref([
    {
        id: 1,
        name: 'General Admission',
        price: 0,
        description: 'Standard entry to the event',
        quantity: 0,
        available: 100
    }
])

// Update ticket price when event is loaded
watch(() => event.value, (newEvent) => {
    if (newEvent) {
        tickets.value[0].price = parseFloat(newEvent.price) || 0
        tickets.value[0].available = parseInt(newEvent.capacity) || 100
    }
}, { immediate: true })

const increaseQuantity = (ticket) => {
    if (ticket.quantity < ticket.available) {
        ticket.quantity++
    }
}

const decreaseQuantity = (ticket) => {
    if (ticket.quantity > 0) {
        ticket.quantity--
    }
}

const selectedTickets = computed(() => {
    return tickets.value.filter(ticket => ticket.quantity > 0)
})

const totalAmount = computed(() => {
    return selectedTickets.value.reduce((total, ticket) => {
        return total + (ticket.price * ticket.quantity)
    }, 0)
})

const hasSelectedTickets = computed(() => {
    return selectedTickets.value.length > 0
})
</script>

<style scoped>
.ticket-booking {
    max-width: 800px;
    margin: 0 auto;
    padding: 2rem;
}

.ticket-booking__header {
    margin-bottom: 2rem;
    text-align: center;
}

.ticket-booking__content {
    display: grid;
    grid-template-columns: 1fr 300px;
    gap: 2rem;
}

.ticket-type {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem;
    border: 1px solid #e2e8f0;
    border-radius: 0.5rem;
    margin-bottom: 1rem;
}

.ticket-type__info {
    flex: 1;
}

.ticket-type__info h3 {
    margin: 0 0 0.5rem 0;
    color: #2d3748;
}

.price {
    font-size: 1.25rem;
    font-weight: bold;
    color: #4a5568;
    margin: 0.5rem 0;
}

.description {
    color: #718096;
    margin: 0;
}

.ticket-type__quantity {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.quantity-btn {
    width: 2rem;
    height: 2rem;
    border: 1px solid #e2e8f0;
    border-radius: 0.25rem;
    background: white;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
}

.quantity-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.quantity {
    font-weight: bold;
    min-width: 2rem;
    text-align: center;
}

.booking-summary {
    background: #f7fafc;
    padding: 1.5rem;
    border-radius: 0.5rem;
    position: sticky;
    top: 2rem;
}

.summary-items {
    margin: 1rem 0;
}

.summary-item {
    display: flex;
    justify-content: space-between;
    margin-bottom: 0.5rem;
}

.total {
    display: flex;
    justify-content: space-between;
    font-weight: bold;
    font-size: 1.25rem;
    margin: 1rem 0;
    padding-top: 1rem;
    border-top: 1px solid #e2e8f0;
}

.checkout-btn {
    display: block;
    width: 100%;
    padding: 0.75rem;
    background: #4299e1;
    color: white;
    border: none;
    border-radius: 0.25rem;
    font-weight: bold;
    cursor: pointer;
    transition: background-color 0.2s;
    text-align: center;
    text-decoration: none;
}

.checkout-btn:hover {
    background: #3182ce;
}

.checkout-btn.disabled {
    background: #cbd5e0;
    cursor: not-allowed;
    pointer-events: none;
}
</style>