<template>
  <div class="base-textarea">
    <label v-if="label" :for="id" class="textarea-label">{{ label }}</label>
    <textarea
      :id="id"
      :placeholder="placeholder"
      :value="modelValue"
      :disabled="disabled"
      @input="$emit('update:modelValue', $event.target.value)"
      :class="['textarea-field', { error: error }]"
      v-bind="$attrs"
    ></textarea>
    <div v-if="error" class="textarea-error">{{ error }}</div>
  </div>
</template>

<script setup>
const props = defineProps({
  modelValue: String,
  label: String,
  placeholder: String,
  id: String,
  error: String,
  disabled: Boolean,
})

defineEmits(['update:modelValue'])
</script>

<style scoped>
.base-textarea {
  display: flex;
  flex-direction: column;
  margin-bottom: 1.25rem;
}
.textarea-label {
  margin-bottom: 0.5rem;
  font-weight: 500;
  color: var(--text-color);
}
.textarea-field {
  padding: 0.75rem;
  border: 1px solid #e0e0e0;
  border-radius: 0.5rem;
  font-size: 1rem;
  min-height: 120px;
  resize: vertical;
  transition: border-color 0.2s;
  background: var(--white-color);
}
.textarea-field:focus {
  outline: none;
  border-color: var(--primary-color);
}
.textarea-field.error {
  border-color: #dc3545;
}
.textarea-error {
  color: #dc3545;
  font-size: 0.875rem;
  margin-top: 0.25rem;
}
</style> 