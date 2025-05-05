<template>
  <div class="base-select">
    <label v-if="label" :for="id" class="select-label">{{ label }}</label>
    <select
      :id="id"
      :value="modelValue"
      :disabled="disabled"
      @change="$emit('update:modelValue', $event.target.value)"
      :class="['select-field', { error: error }]"
      v-bind="$attrs"
    >
      <option v-if="placeholder" value="" disabled selected hidden>{{ placeholder }}</option>
      <option v-for="option in options" :key="option.value || option" :value="option.value || option">
        {{ option.label || option }}
      </option>
    </select>
    <div v-if="error" class="select-error">{{ error }}</div>
  </div>
</template>

<script setup>
const props = defineProps({
  modelValue: [String, Number],
  label: String,
  placeholder: String,
  options: {
    type: Array,
    required: true,
  },
  id: String,
  error: String,
  disabled: Boolean,
})

defineEmits(['update:modelValue'])
</script>

<style scoped>
.base-select {
  display: flex;
  flex-direction: column;
  margin-bottom: 1.25rem;
}
.select-label {
  margin-bottom: 0.5rem;
  font-weight: 500;
  color: var(--text-color);
}
.select-field {
  padding: 0.75rem;
  border: 1px solid #e0e0e0;
  border-radius: 0.5rem;
  font-size: 1rem;
  transition: border-color 0.2s;
  background: var(--white-color);
}
.select-field:focus {
  outline: none;
  border-color: var(--primary-color);
}
.select-field.error {
  border-color: #dc3545;
}
.select-error {
  color: #dc3545;
  font-size: 0.875rem;
  margin-top: 0.25rem;
}
</style> 