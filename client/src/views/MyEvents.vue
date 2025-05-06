<template>
    <div class="my-events-page">
        <div class="page-header">
            <h2>My Events</h2>
            <BaseButton variant="primary" @click="$router.push('/dashboard/create-event')">
                <i class="fas fa-plus"></i> Create New Event
            </BaseButton>
        </div>
        <div v-if="loading" class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
        <div v-else>
            <div v-if="createdEvents.length === 0" class="empty-state">
                <i class="fas fa-calendar-times fa-3x mb-3"></i>
                <p class="lead">No events found.</p>
                <BaseButton variant="primary" @click="$router.push('/dashboard/create-event')">
                    Create Your First Event
                </BaseButton>
            </div>
            <div v-else class="event-list">
                <div v-for="event in createdEvents" :key="event.id" class="event-card-wrapper">
                    <EventCard :event="event" />
                    <div class="event-actions">
                        <BaseButton variant="primary" size="small" @click="editEvent(event.id)">
                            <i class="fas fa-edit"></i> Edit
                        </BaseButton>
                        <BaseButton variant="danger" size="small" @click="confirmDelete(event.id)">
                            <i class="fas fa-trash"></i> Delete
                        </BaseButton>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Event Modal -->
        <BaseModal
            v-model="showEditModal"
            title="Edit Event"
            :image="editEventData?.image_url"
        >
            <form v-if="editEventData" @submit.prevent="submitEdit">
                <BaseInput v-model="editEventData.title" label="Title" required />
                <BaseInput v-model="editEventData.description" label="Description" required />
                <BaseInput v-model="editEventData.start_date" label="Start Date & Time" type="datetime-local" required />
                <BaseInput v-model="editEventData.end_date" label="End Date & Time" type="datetime-local" required />
                <BaseInput v-model="editEventData.location" label="Location" required />
                <BaseInput v-model="editEventData.capacity" label="Capacity" type="number" min="1" />
                <BaseInput v-model="editEventData.price" label="Price" type="number" min="0" />
                <BaseSelect 
                    v-model="editEventData.category_id" 
                    label="Category" 
                    :options="categoryOptions" 
                    required 
                />
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" v-model="editEventData.is_online" id="editIsOnline">
                    <label class="form-check-label" for="editIsOnline">Online Event</label>
                </div>
                <BaseInput v-if="editEventData.is_online" v-model="editEventData.online_link" label="Online Event Link" />
                <div class="mt-3 d-flex justify-content-end">
                    <BaseButton type="submit" variant="primary" size="small">Save</BaseButton>
                </div>
            </form>
        </BaseModal>

        <!-- Delete Confirmation Modal -->
        <BaseModal
            v-model="showDeleteModal"
            title="Confirm Delete"
        >
            <div class="delete-confirmation">
                <div class="warning-icon">
                    <i class="fas fa-exclamation-triangle text-warning"></i>
                </div>
                <p>Are you sure you want to delete this event?</p>
                <p class="text-danger">This action cannot be undone.</p>
                <div class="mt-3 d-flex justify-content-end">
                    <BaseButton variant="danger" size="small" @click="deleteEvent(deleteEventId)">
                        Delete
                    </BaseButton>
                </div>
            </div>
        </BaseModal>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useToast } from 'vue-toastification'
import { useEventStore } from '@/stores/event'
import EventCard from '@/components/EventCard.vue'
import BaseButton from '@/components/Base/BaseButton.vue'
import BaseModal from '@/components/Base/BaseModal.vue'
import BaseInput from '@/components/Base/BaseInput.vue'
import BaseSelect from '@/components/Base/BaseSelect.vue'

const eventStore = useEventStore()
const createdEvents = ref([])
const loading = ref(true)
const router = useRouter()
const toast = useToast()

const showEditModal = ref(false)
const editEventData = ref(null)
const categories = ref([])
const showDeleteModal = ref(false)
const deleteEventId = ref(null)

const categoryOptions = computed(() => {
    return categories.value.map(category => ({
        value: category.id,
        label: category.name
    }))
})

onMounted(async () => {
    loading.value = true
    try {
        createdEvents.value = await eventStore.fetchMyEvents()
        categories.value = await eventStore.fetchCategories()
    } catch (error) {
        toast.error('Failed to load events or categories')
    } finally {
        loading.value = false
    }
})

const editEvent = (id) => {
    const event = createdEvents.value.find(e => e.id === id)
    if (event) {
        editEventData.value = JSON.parse(JSON.stringify(event))
        showEditModal.value = true
    }
}

const submitEdit = async () => {
    try {
        await eventStore.updateEvent(editEventData.value.id, editEventData.value)
        const idx = createdEvents.value.findIndex(e => e.id === editEventData.value.id)
        if (idx !== -1) {
            createdEvents.value[idx] = { ...editEventData.value }
        }
        toast.success('Event updated successfully!')
        showEditModal.value = false
    } catch (e) {
        toast.error('Failed to update event.')
    }
}

const confirmDelete = (id) => {
    deleteEventId.value = id
    showDeleteModal.value = true
}

const deleteEvent = async (id) => {
    try {
        await eventStore.deleteEvent(id)
        createdEvents.value = createdEvents.value.filter(event => event.id !== id)
        toast.success('Event deleted successfully!')
        showDeleteModal.value = false
    } catch (e) {
        toast.error('Failed to delete event.')
    }
}
</script>

<style scoped>
.my-events-page {
    padding: 1rem;
}

.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
}

.event-list {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: 2rem;
}

.event-card-wrapper {
    display: flex;
    flex-direction: column;
    height: 100%;
}

.event-actions {
    display: flex;
    gap: 1rem;
    margin-top: 0.5rem;
    justify-content: center;
}

.empty-state {
    text-align: center;
    padding: 3rem;
    background: #f8f9fa;
    border-radius: 8px;
    margin: 2rem 0;
}

.empty-state i {
    color: #6c757d;
    margin-bottom: 1rem;
}

.delete-confirmation {
    text-align: center;
    padding: 1rem;
}

.delete-confirmation p {
    margin-bottom: 1rem;
}

.warning-icon {
    font-size: 3rem;
    margin-bottom: 1rem;
    color: #ffc107;
}
</style>
