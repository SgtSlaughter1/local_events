<template>
    <div class="checkout">
        <div class="checkout__header">
            <h2>Checkout</h2>
        </div>

        <div v-if="!tickets.length" class="checkout__empty">
            <p>No tickets selected. Please go back to select tickets.</p>
            <router-link :to="{ name: 'ticket-booking', params: { id: route.params.eventId } }" class="back-btn">
                Go Back
            </router-link>
        </div>

        <div v-else class="checkout__content">
            <!-- Order Summary -->
            <div class="order-summary">
                <h3>Order Summary</h3>
                <div class="summary-items">
                    <div v-for="ticket in tickets" :key="ticket.id" class="summary-item">
                        <span>{{ ticket.name }} x {{ ticket.quantity }}</span>
                        <span>₦{{ (ticket.price * ticket.quantity).toFixed(2) }}</span>
                    </div>
                </div>
                <div class="total">
                    <span>Total Amount:</span>
                    <span>₦{{ totalAmount.toFixed(2) }}</span>
                </div>
            </div>

            <!-- Payment Form -->
            <form @submit.prevent="processPayment" class="payment-form">
                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input type="text" id="name" v-model="paymentDetails.name" required
                        placeholder="Enter your full name">
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" v-model="paymentDetails.email" required
                        placeholder="Enter your email">
                </div>

                <div class="form-group">
                    <label for="card">Card Number</label>
                    <input type="text" id="card" v-model="paymentDetails.cardNumber" required
                        placeholder="1234 5678 9012 3456" maxlength="19">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="expiry">Expiry Date</label>
                        <input type="text" id="expiry" v-model="paymentDetails.expiry" required placeholder="MM/YY"
                            maxlength="5">
                    </div>

                    <div class="form-group">
                        <label for="cvv">CVV</label>
                        <input type="text" id="cvv" v-model="paymentDetails.cvv" required placeholder="123"
                            maxlength="3">
                    </div>
                </div>

                <button type="submit" class="pay-btn" :disabled="isProcessing">
                    {{ isProcessing ? 'Processing...' : `Pay ₦${totalAmount.toFixed(2)}` }}
                </button>
            </form>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRouter, useRoute } from 'vue-router'

const router = useRouter()
const route = useRoute()

// Get tickets from route state
const tickets = computed(() => route.state?.tickets || [])

const totalAmount = computed(() => {
    return tickets.value.reduce((total, ticket) => {
        return total + (ticket.price * ticket.quantity)
    }, 0)
})

const paymentDetails = ref({
    name: '',
    email: '',
    cardNumber: '',
    expiry: '',
    cvv: ''
})

const isProcessing = ref(false)

const processPayment = async () => {
    try {
        isProcessing.value = true

        // Here you would typically:
        // 1. Validate the payment details
        // 2. Call your payment processing API
        // 3. Create the order in your backend
        // 4. Send confirmation emails

        // Mock API call
        await new Promise(resolve => setTimeout(resolve, 2000))

        // Navigate to success page with ticket data
        router.push({
            name: 'payment-success',
            params: { eventId: route.params.eventId },
            state: { tickets: tickets.value }
        })
    } catch (error) {
        console.error('Payment processing failed:', error)
        alert('Payment processing failed. Please try again.')
    } finally {
        isProcessing.value = false
    }
}
</script>

<style scoped>
.checkout {
    max-width: 800px;
    margin: 0 auto;
    padding: 2rem;
}

.checkout__header {
    margin-bottom: 2rem;
    text-align: center;
}

.checkout__content {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
}

.order-summary {
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

.payment-form {
    background: white;
    padding: 1.5rem;
    border-radius: 0.5rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

label {
    display: block;
    margin-bottom: 0.5rem;
    color: #4a5568;
    font-weight: 500;
}

input {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #e2e8f0;
    border-radius: 0.25rem;
    font-size: 1rem;
}

input:focus {
    outline: none;
    border-color: #4299e1;
    box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.1);
}

.pay-btn {
    width: 100%;
    padding: 0.75rem;
    background: #4299e1;
    color: white;
    border: none;
    border-radius: 0.25rem;
    font-weight: bold;
    cursor: pointer;
    transition: background-color 0.2s;
}

.pay-btn:hover {
    background: #3182ce;
}

.pay-btn:disabled {
    background: #cbd5e0;
    cursor: not-allowed;
}

.checkout__empty {
    text-align: center;
    padding: 2rem;
    background: #f7fafc;
    border-radius: 0.5rem;
    margin: 2rem 0;
}

.back-btn {
    display: inline-block;
    margin-top: 1rem;
    padding: 0.75rem 1.5rem;
    background: #4299e1;
    color: white;
    border: none;
    border-radius: 0.25rem;
    font-weight: bold;
    cursor: pointer;
    transition: background-color 0.2s;
    text-decoration: none;
}

.back-btn:hover {
    background: #3182ce;
}
</style>