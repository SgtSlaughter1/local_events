<template>
  <div class="base-input">
    <label v-if="label" :for="id" class="input-label">{{ label }}</label>
    <input
      :id="id"
      :type="type"
      :placeholder="placeholder"
      :value="modelValue"
      :disabled="disabled"
      @input="$emit('update:modelValue', $event.target.value)"
      :class="['input-field', { error: error }]"
      v-bind="$attrs"
    />
    <div v-if="error" class="input-error">{{ error }}</div>
  </div>
</template>

<script setup>
const props = defineProps({
  modelValue: [String, Number],
  label: String,
  placeholder: String,
  type: {
    type: String,
    default: 'text',
  },
  id: String,
  error: String,
  disabled: Boolean,
})

defineEmits(['update:modelValue'])
</script>

<style scoped>
.base-input {
  display: flex;
  flex-direction: column;
  margin-bottom: 1.25rem;
}
.input-label {
  margin-bottom: 0.5rem;
  font-weight: 500;
  color: var(--text-color);
}
.input-field {
  padding: 0.75rem;
  border: 1px solid #e0e0e0;
  border-radius: 0.5rem;
  font-size: 1rem;
  transition: border-color 0.2s;
}
.input-field:focus {
  outline: none;
  border-color: var(--primary-color);
}
.input-field.error {
  border-color: #dc3545;
}
.input-error {
  color: #dc3545;
  font-size: 0.875rem;
  margin-top: 0.25rem;
}
</style> 