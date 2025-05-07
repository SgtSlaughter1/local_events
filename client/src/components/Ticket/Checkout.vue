<template>
    <div class="max-w-4xl mx-auto p-6">
        <div v-if="loading" class="flex justify-center items-center min-h-[400px]">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary"></div>
        </div>

        <div v-else-if="error" class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
            <p class="text-red-600">{{ error }}</p>
        </div>

        <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Payment Form -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-2xl font-semibold mb-6">Payment Details</h2>
                
                <form @submit.prevent="processPayment" class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Card Number</label>
                        <input 
                            type="text" 
                            v-model="paymentDetails.cardNumber"
                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                            placeholder="4242 4242 4242 4242"
                            required
                        />
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Expiry Date</label>
                            <input 
                                type="text" 
                                v-model="paymentDetails.expiryDate"
                                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                                placeholder="MM/YY"
                                required
                            />
                </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">CVC</label>
                            <input 
                                type="text" 
                                v-model="paymentDetails.cvc"
                                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                                placeholder="123"
                                required
                            />
                </div>
            </div>

                    <button 
                        type="submit"
                        :disabled="processing"
                        class="w-full bg-primary text-white py-3 rounded-lg font-medium hover:bg-primary-dark disabled:opacity-50"
                    >
                        {{ processing ? 'Processing...' : 'Pay Now' }}
                    </button>
                </form>
                </div>

            <!-- Order Summary -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-2xl font-semibold mb-6">Order Summary</h2>
                
                <div class="space-y-4">
                    <div class="flex justify-between text-gray-600">
                        <span>Event</span>
                        <span>{{ registration?.event?.title }}</span>
                </div>
                    <div class="flex justify-between text-gray-600">
                        <span>Number of Tickets</span>
                        <span>{{ registration?.number_of_tickets }}</span>
                </div>
                    <div class="flex justify-between text-gray-600">
                        <span>Price per Ticket</span>
                        <span>{{ formatPrice(registration?.event?.price) }}</span>
                    </div>
                    <div class="border-t pt-4">
                        <div class="flex justify-between font-semibold text-lg">
                            <span>Total</span>
                            <span>{{ formatPrice(registration?.payment_amount) }}</span>
                        </div>
                    </div>
                </div>
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

const paymentDetails = ref({
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
        
        // Clear registration data from localStorage
        localStorage.removeItem('currentRegistration')

        // Navigate to success page
        router.push({
            name: 'payment-success',
            params: { id: route.params.id }
        })
    } catch (error) {
        console.error('Payment processing failed:', error)
    } finally {
        processing.value = false
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