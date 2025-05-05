<template>
    <form class="event-details-form" @submit.prevent>
        <BaseInput v-model="form.title" label="Event Title" placeholder="Enter the name of your event"
            :error="errors.title" required />
        <BaseSelect v-model="form.category" label="Event Category" placeholder="Please select one"
            :options="categoryOptions" :error="errors.category" required />
        <BaseTextarea v-model="form.description" label="Event Description"
            placeholder="Describe what's special about your event & other important details."
            :error="errors.description" required />
        <div class="date-time-section">
            <BaseInput v-model="form.start_date" label="Start Date & Time" type="datetime-local"
                :error="errors.start_date" required />
            <BaseInput v-model="form.end_date" label="End Date & Time" type="datetime-local" :error="errors.end_date"
                required />
        </div>
        <div class="location-section">
            <BaseInput v-model="form.location" label="Event Location" placeholder="Enter the event location"
                :error="errors.location" />
            <BaseInput v-model="form.capacity" label="Event Capacity" type="number" min="1"
                placeholder="Enter maximum number of attendees" :error="errors.capacity" />
        </div>
        <div class="online-section">
            <BaseCheckbox v-model="form.is_online" label="This is an online event" />
            <BaseInput v-if="form.is_online" v-model="form.online_link" label="Online Event Link"
                placeholder="Enter the online event link (e.g. Zoom, Google Meet)" :error="errors.online_link" />
        </div>
    </form>
</template>

<script setup>
import { reactive, watch, onMounted, ref } from 'vue'
import { useEventStore } from '../../stores/event'
import BaseInput from '../Base/BaseInput.vue'
import BaseSelect from '../Base/BaseSelect.vue'
import BaseTextarea from '../Base/BaseTextarea.vue'
import BaseCheckbox from '../Base/BaseCheckbox.vue'

const props = defineProps({
    modelValue: {
        type: Object,
        default: () => ({})
    }
})

const emit = defineEmits(['update:modelValue'])

const form = reactive({
    title: '',
    category: '',
    description: '',
    start_date: '',
    end_date: '',
    location: '',
    capacity: '',
    is_online: false,
    online_link: ''
})

const errors = reactive({
    title: '',
    category: '',
    description: '',
    start_date: '',
    end_date: '',
    location: '',
    capacity: '',
    online_link: ''
})

const eventStore = useEventStore()
const categoryOptions = ref([])

onMounted(async () => {
    Object.assign(form, props.modelValue)
    const categories = await eventStore.fetchCategories()
    if (Array.isArray(categories)) {
        categoryOptions.value = categories.map(cat => ({ value: cat.id, label: cat.name }))
    }
})

// Watch for changes in the form and emit updates
watch(form, (newVal) => {
    emit('update:modelValue', { ...newVal })
}, { deep: true })

// Watch for changes in modelValue and update form
watch(() => props.modelValue, (newVal) => {
    Object.assign(form, newVal)
}, { deep: true })
</script>

<style scoped>
.event-details-form {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.date-time-section,
.location-section,
.online-section {
    display: flex;
    gap: 1rem;
}

.date-time-section,
.location-section {
    flex-wrap: wrap;
}

.date-time-section>*,
.location-section>* {
    flex: 1;
    min-width: 200px;
}

.online-section {
    flex-direction: column;
}
</style>
