<template>
  <form class="event-details-form" @submit.prevent>
    <BaseInput
      v-model="form.title"
      label="Event Title"
      placeholder="Enter the name of your event"
      :error="errors.title"
      required
    />
    <BaseSelect
      v-model="form.category"
      label="Event Category"
      placeholder="Please select one"
      :options="categoryOptions"
      :error="errors.category"
      required
    />
    <BaseTextarea
      v-model="form.description"
      label="Event Description"
      placeholder="Describe what's special about your event & other important details."
      :error="errors.description"
      required
    />
  </form>
</template>

<script setup>
import { reactive, watch, toRefs } from 'vue'
import BaseInput from '../Base/BaseInput.vue'
import BaseSelect from '../Base/BaseSelect.vue'
import BaseTextarea from '../Base/BaseTextarea.vue'

const props = defineProps({
  modelValue: Object,
})
const emit = defineEmits(['update:modelValue'])

const form = reactive({
  title: props.modelValue?.title || '',
  category: props.modelValue?.category || '',
  description: props.modelValue?.description || '',
})

const errors = reactive({
  title: '',
  category: '',
  description: '',
})

const categoryOptions = [
  { value: 'music', label: 'Music' },
  { value: 'sports', label: 'Sports' },
  { value: 'conference', label: 'Conference' },
  { value: 'workshop', label: 'Workshop' },
  { value: 'meetup', label: 'Meetup' },
  { value: 'other', label: 'Other' },
]

watch(form, (val) => {
  emit('update:modelValue', { ...val })
}, { deep: true })
</script>

<style scoped>
.event-details-form {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}
</style>
