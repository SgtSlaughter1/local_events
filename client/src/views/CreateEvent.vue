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
import { ref } from 'vue'
import BaseButton from '../components/Base/BaseButton.vue'
import EventDetails from '../components/create-event/EventDetails.vue'
import EventBanner from '../components/create-event/EventBanner.vue'
import EventTicketing from '../components/create-event/EventTicketing.vue'
import EventReview from '../components/create-event/EventReview.vue'

const formData = ref({})

const steps = [
    { key: 'edit', label: 'Edit', component: EventDetails },
    { key: 'banner', label: 'Banner', component: EventBanner },
    { key: 'ticketing', label: 'Ticketing', component: EventTicketing },
    { key: 'review', label: 'Review', component: EventReview },
]

const currentStep = ref(0)

const nextStep = () => {
    if (currentStep.value < steps.length - 1) currentStep.value++
}
const prevStep = () => {
    if (currentStep.value > 0) currentStep.value--
}
const submitEvent = () => {
    // TODO: Implement event submission logic
    alert('Event submitted!')
    console.log('Submitted data:', formData.value)
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