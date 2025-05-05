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
            <component :is="steps[currentStep].component" v-model="formData" />
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
    </div>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useEventStore } from '../stores/event'
import api from '../services/api'
import BaseButton from '../components/Base/BaseButton.vue'
import EventDetails from '../components/create-event/EventDetails.vue'
import EventBanner from '../components/create-event/EventBanner.vue'
import EventTicketing from '../components/create-event/EventTicketing.vue'
import EventReview from '../components/create-event/EventReview.vue'

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
        console.log('Fetched categories:', categories.value)
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
    location: '',
    capacity: '',
    is_online: false,
    online_link: '',

    // Banner
    bannerUrl: '',
    bannerAlt: '',

    // Ticketing
    eventType: 'ticketed',
    tickets: [{ name: '', price: '' }]
})

const stepKeys = [
    // Step 0: Details
    ['title', 'category', 'description', 'start_date', 'end_date', 'location', 'capacity', 'is_online', 'online_link'],
    // Step 1: Banner
    ['bannerUrl', 'bannerAlt'],
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
        // Transform the form data to match the backend API requirements
        const eventData = {
            title: formData.value.title,
            description: formData.value.description,
            category_id: formData.value.category,
            start_date: formData.value.start_date ? new Date(formData.value.start_date).toISOString() : null,
            end_date: formData.value.end_date ? new Date(formData.value.end_date).toISOString() : null,
            location: formData.value.location || null,
            capacity: formData.value.capacity ? parseInt(formData.value.capacity) : null,
            price: formData.value.eventType === 'ticketed' ? parseFloat(formData.value.tickets[0]?.price || 0) : 0,
            image: formData.value.bannerUrl || null,
            is_online: formData.value.is_online || false,
            online_link: formData.value.online_link || null
        }

        // Call the event store to create the event
        const createdEvent = await eventStore.createEvent(eventData)
        
        // Remove all step drafts after successful publish
        clearAllStepDrafts()
        
        // Show success message and redirect to the event details page
        alert('Event created successfully!')
        router.push(`/events/${createdEvent.id}`)
    } catch (error) {
        console.error('Error creating event:', error)
        if (error.response?.data?.errors) {
            const errorMessages = Object.values(error.response.data.errors).flat().join('\n')
            alert(`Validation errors:\n${errorMessages}`)
        } else {
            alert(error.message || 'Failed to create event. Please try again.')
        }
    }
}

const clearAllStepDrafts = () => {
    stepKeys.forEach((keys, idx) => {
        localStorage.removeItem(`eventDraft-step-${idx}`)
    })
}
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
</style>