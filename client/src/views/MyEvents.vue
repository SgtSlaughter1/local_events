<template>
    <div class="my-events-page">
        <h2>My Events</h2>
        <div v-if="loading" class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
        <div v-else>
            <div v-if="createdEvents.length === 0">No events found.</div>
            <div class="event-list">
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
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useToast } from 'vue-toastification'
import { useEventStore } from '@/stores/event'
import EventCard from '@/components/EventCard.vue'
import BaseButton from '@/components/Base/BaseButton.vue'

const eventStore = useEventStore()
const createdEvents = ref([])
const loading = ref(true)
const router = useRouter()
const toast = useToast()

onMounted(async () => {
    loading.value = true
    createdEvents.value = await eventStore.fetchMyEvents()
    loading.value = false
})

const editEvent = (id) => {
    router.push(`/events/${id}/edit`)
}

const confirmDelete = (id) => {
    toast(
        {
            component: {
                template: `
          <div>
            <p>Are you sure you want to delete this event?</p>
            <div style="display: flex; gap: 1rem; margin-top: 1rem;">
              <button class='btn btn-danger btn-sm' @click="$emit('confirm')">Delete</button>
              <button class='btn btn-secondary btn-sm' @click="$emit('close')">Cancel</button>
            </div>
          </div>
        `,
                emits: ['confirm', 'close'],
            },
            listeners: {
                confirm: async () => {
                    await deleteEvent(id)
                    toast.clear()
                },
                close: () => toast.clear(),
            },
        },
        {
            timeout: false,
            closeOnClick: false,
            hideProgressBar: true,
            position: 'top-center',
        }
    )
}

const deleteEvent = async (id) => {
    try {
        await eventStore.deleteEvent(id)
        createdEvents.value = createdEvents.value.filter(event => event.id !== id)
        toast.success('Event deleted successfully!')
    } catch (e) {
        toast.error('Failed to delete event.')
    }
}
</script>

<style scoped>
.my-events-page {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem 1rem;
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
</style>
