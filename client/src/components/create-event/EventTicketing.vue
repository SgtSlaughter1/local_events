<template>
    <div class="event-ticketing-form">
        <div class="section">
            <h3 class="section-title">What type of event are you running?</h3>
            <div class="event-type-toggle">
                <div class="event-type-card" :class="{ active: form.eventType === 'ticketed' }"
                    @click="form.eventType = 'ticketed'">
                    <div class="icon"><img src="/images/ion_ticket.svg" alt="Ticketed Event"></div>
                    <div class="type-title">Ticketed Event</div>
                    <div class="type-desc">My event requires tickets for entry</div>
                </div>
                <div class="event-type-card" :class="{ active: form.eventType === 'free' }"
                    @click="form.eventType = 'free'">
                    <div class="icon"><img src="/images/free.svg" alt="Free Event"></div>
                    <div class="type-title">Free Event</div>
                    <div class="type-desc">I'm running a free event</div>
                </div>
            </div>
        </div>

        <div class="section">
            <h3 class="section-title">What tickets are you selling?</h3>
            <div v-for="(ticket, idx) in form.tickets" :key="idx" class="ticket-row">
                <BaseInput v-model="ticket.name" label="Ticket Name" placeholder="Ticket Name e.g. General Admission"
                    class="ticket-input" />
                <BaseInput v-model="ticket.price" label="Ticket Price" placeholder="₦ 0.00" type="number" min="0"
                    class="ticket-input" />
                <button v-if="form.tickets.length > 1" type="button" class="remove-ticket" @click="removeTicket(idx)">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
            <button type="button" class="add-ticket-btn" @click="addTicket">
                <i class="fas fa-plus"></i>
            </button>
        </div>
    </div>
</template>

<script setup>
import { reactive, watch } from 'vue'
import BaseInput from '../Base/BaseInput.vue'

const props = defineProps({
    modelValue: Object,
})
const emit = defineEmits(['update:modelValue'])

const form = reactive({
    eventType: props.modelValue?.eventType || 'ticketed',
    tickets: props.modelValue?.tickets?.length
        ? props.modelValue.tickets.map(t => ({ ...t }))
        : [{ name: '', price: '' }],
})

function addTicket() {
    form.tickets.push({ name: '', price: '' })
}
function removeTicket(idx) {
    form.tickets.splice(idx, 1)
}

watch(form, (val) => {
    emit('update:modelValue', { ...val, tickets: val.tickets.map(t => ({ ...t })) })
}, { deep: true })
</script>

<style scoped>
.event-ticketing-form {
    display: flex;
    flex-direction: column;
    gap: 2.5rem;
}

.section {
    margin-bottom: 1.5rem;
}

.section-title {
    font-size: 1.2rem;
    font-weight: 600;
    margin-bottom: 1rem;
}

.event-type-toggle {
    display: flex;
    gap: 2rem;
}

.event-type-card {
    flex: 1;
    border: 2px solid #e0e0e0;
    border-radius: 1rem;
    padding: 2rem 1.5rem;
    text-align: center;
    cursor: pointer;
    background: var(--white-color);
    transition: border-color 0.2s, box-shadow 0.2s;
}

.event-type-card.active {
    border-color: var(--primary-color);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
}

.event-type-card .icon {
    font-size: 2.2rem;
    margin-bottom: 0.5rem;
    color: var(--primary-color);
}

.type-title {
    font-weight: 600;
    font-size: 1.1rem;
    margin-bottom: 0.25rem;
}

.type-desc {
    color: #888;
    font-size: 0.95rem;
}

.ticket-row {
    display: flex;
    align-items: flex-end;
    gap: 1rem;
    margin-bottom: 1rem;
}

.ticket-input {
    flex: 1;
}

.add-ticket-btn {
    background: none;
    border: 2px dashed var(--primary-color);
    color: var(--primary-color);
    border-radius: 0.5rem;
    padding: 0.5rem 1.2rem;
    font-size: 1.1rem;
    cursor: pointer;
    margin-top: 0.5rem;
    transition: background 0.2s;
}

.add-ticket-btn:hover {
    background: var(--primary-color);
    color: var(--white-color);
}

.remove-ticket {
    background: none;
    border: none;
    color: #dc3545;
    font-size: 1.2rem;
    cursor: pointer;
    margin-bottom: 0.5rem;
}
</style>
