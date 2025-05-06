<template>
    <div class="payment-success">
        <div class="success-icon">✓</div>
        <h2>Payment Successful!</h2>
        <p>Thank you for your purchase. Your tickets have been booked successfully.</p>

        <div class="ticket-details">
            <h3>Ticket Details</h3>
            <div v-for="ticket in tickets" :key="ticket.id" class="ticket-item">
                <span>{{ ticket.name }} x {{ ticket.quantity }}</span>
                <span>₦{{ (ticket.price * ticket.quantity).toFixed(2) }}</span>
            </div>
            <div class="total">
                <span>Total Amount:</span>
                <span>₦{{ totalAmount.toFixed(2) }}</span>
            </div>
        </div>

        <div class="actions">
            <button @click="downloadTickets" class="download-btn">
                Download Tickets
            </button>
            <router-link :to="{ name: 'home' }" class="home-btn">
                Return to Home
            </router-link>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'

const route = useRoute()

// Get tickets from route state
const tickets = computed(() => route.state?.tickets || [])

const totalAmount = computed(() => {
    return tickets.value.reduce((total, ticket) => {
        return total + (ticket.price * ticket.quantity)
    }, 0)
})

const downloadTickets = () => {
    // Here you would typically:
    // 1. Generate PDF tickets
    // 2. Allow user to download them
    console.log('Downloading tickets...')
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