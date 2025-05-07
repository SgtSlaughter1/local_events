<template>
    <div class="create-event-page">
        <!-- Stepper -->
        <div class="stepper-container">
            <div class="stepper">
                <div v-for="(step, idx) in steps" :key="step.key" class="step">
                    <div class="step-circle" :class="{ active: currentStep === idx }">
                        <span>{{ idx + 1 }}</span>
                    </div>
                    <div class="step-label">{{ step.label }}</div>
                    <div v-if="idx < steps.length - 1" class="step-line"></div>
                </div>
            </div>
        </div>

        <h2 class="page-title">Create a New Event</h2>

        <!-- Step Content -->
        <div class="step-content">
            <component :is="steps[currentStep].component" v-model="formData" :errors="errors" />
        </div>

        <!-- Navigation Buttons -->
        <div class="step-actions">
            <BaseButton v-if="currentStep > 0" variant="secondary" size="large" @click="prevStep">
                Back
            </BaseButton>
            <BaseButton v-if="currentStep < steps.length - 1" variant="primary" size="large" class="ms-2"
                @click="nextStep">
                Next
            </BaseButton>
            <BaseButton v-else variant="primary" size="large" class="ms-2" @click="submitEvent">
                Submit
            </BaseButton>
        </div>

        <!-- Success Modal -->
        <BaseModal
            v-model="showSuccessModal"
            title="Success!"
            size="modal-sm"
        >
            <div class="text-center">
                <i class="fas fa-check-circle text-success fa-3x mb-3"></i>
                <p class="mb-0">Event created successfully!</p>
                <p class="text-muted small">Redirecting to event page...</p>
            </div>
        </BaseModal>
    </div>
</template>

<script setup>
import { ref, watch, onMounted, onBeforeUnmount } from 'vue'
import { useRouter } from 'vue-router'
import { useEventStore } from '../stores/event'
import api from '../services/api'
import BaseButton from '../components/Base/BaseButton.vue'
import EventDetails from '../components/create-event/EventDetails.vue'
import EventBanner from '../components/create-event/EventBanner.vue'
import EventTicketing from '../components/create-event/EventTicketing.vue'
import EventReview from '../components/create-event/EventReview.vue'
import BaseModal from '@/components/Base/BaseModal.vue'

const router = useRouter()
const eventStore = useEventStore()

const user = ref({ name: '' })
onMounted(() => {
    // Fetch user from localStorage
    const userStr = localStorage.getItem('user')
    if (userStr) {
        try {
            user.value = JSON.parse(userStr)
        } catch (e) {
            user.value = { name: '' }
        }
    }
})

const categories = ref([])
onMounted(async () => {
    try {
        const res = await api.get('/api/categories')
        categories.value = res.data.categories
    } catch (e) {
        categories.value = []
    }
})

const formData = ref({
    // Event Details
    title: '',
    category: '',
    description: '',
    start_date: '',
    end_date: '',
    street_address: '',
    city: '',
    country: '',
    capacity: '',
    is_online: false,
    online_link: '',

    // Image
    image: '',
    image_alt: '',

    // Ticketing
    eventType: 'ticketed',
    tickets: [{ name: '', price: '' }]
})

const stepKeys = [
    // Step 0: Details
    ['title', 'category', 'description', 'start_date', 'end_date', 'street_address', 'city', 'country', 'capacity', 'is_online', 'online_link'],
    // Step 1: Image
    ['image', 'image_alt'],
    // Step 2: Ticketing
    ['eventType', 'tickets'],
    // Step 3: Review (no new fields, but keep for completeness)
    []
]

const saveStepDraft = (step) => {
    const stepData = {}
    stepKeys[step].forEach(key => {
        stepData[key] = formData.value[key]
    })
    localStorage.setItem(`eventDraft-step-${step}`, JSON.stringify(stepData))
}

const mergeAllStepDrafts = () => {
    stepKeys.forEach((keys, idx) => {
        const saved = localStorage.getItem(`eventDraft-step-${idx}`)
        if (saved) {
            Object.assign(formData.value, JSON.parse(saved))
        }
    })
}

onMounted(() => {
    mergeAllStepDrafts()
})

const steps = [
    { key: 'details', label: 'Details', component: EventDetails },
    { key: 'banner', label: 'Banner', component: EventBanner },
    { key: 'ticketing', label: 'Ticketing', component: EventTicketing },
    { key: 'review', label: 'Review', component: EventReview },
]

const currentStep = ref(0)
const totalSteps = steps.length
const errors = ref({})
const showSuccessModal = ref(false)
let redirectTimer = null

const nextStep = () => {
    // Validate current step before proceeding
    if (currentStep.value === 0) {
        // Validate Event Details
        if (!formData.value.title || !formData.value.category || !formData.value.description || 
            !formData.value.start_date || !formData.value.end_date) {
            alert('Please fill in all required fields')
            return
        }
    }
    if (currentStep.value < steps.length - 1) {
        saveStepDraft(currentStep.value)
        // If moving to review step, merge all drafts
        if (currentStep.value + 1 === steps.length - 1) {
            mergeAllStepDrafts()
        }
        currentStep.value++
    }
}

const prevStep = () => {
    if (currentStep.value > 0) {
        saveStepDraft(currentStep.value)
        currentStep.value--
    }
}

const submitEvent = async () => {
    try {
        const event = await eventStore.createEvent(formData.value)
        showSuccessModal.value = true
        
        // Set timer to redirect after 10 seconds
        redirectTimer = setTimeout(() => {
            router.push(`/events/${event.id}`)
        }, 10000)
    } catch (error) {
        errors.value = error.response?.data?.errors || {}
    }
}

// Clean up timer when component is unmounted
onBeforeUnmount(() => {
    if (redirectTimer) {
        clearTimeout(redirectTimer)
    }
})
</script>

<style scoped>
.create-event-page {
    max-width: 800px;
    margin: 0 auto;
    padding: 2rem 1rem 3rem 1rem;
}

.page-title {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 2rem;
    margin-top: 1.5rem;
}

.stepper-container {
    margin-bottom: 2.5rem;
    margin-top: 1rem;
}

.stepper {
    display: flex;
    align-items: center;
    justify-content: flex-start;
    gap: 0.5rem;
}

.step {
    display: flex;
    align-items: center;
    position: relative;
}

.step-circle {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: var(--white-color);
    border: 2px solid var(--primary-color);
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 1.1rem;
    color: var(--primary-color);
    transition: background 0.2s, color 0.2s;
}

.step-circle.active {
    background: var(--primary-color);
    color: var(--white-color);
}

.step-label {
    margin-left: 0.5rem;
    margin-right: 0.5rem;
    font-size: 1rem;
    font-weight: 500;
    color: var(--text-color);
    min-width: 70px;
    text-align: center;
}

.step-line {
    width: 60px;
    height: 2px;
    background: #e0e0e0;
    margin: 0 0.5rem;
}

.step-content {
    min-height: 300px;
    background: var(--white-color);
    border-radius: 1rem;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
    padding: 2rem 1.5rem;
    margin-bottom: 2rem;
}

.step-actions {
    display: flex;
    justify-content: flex-end;
    gap: 1rem;
}

.progress {
    height: 25px;
    border-radius: 12px;
    background-color: #e9ecef;
}

.progress-bar {
    background-color: #4CAF50;
    transition: width 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 500;
}
</style>