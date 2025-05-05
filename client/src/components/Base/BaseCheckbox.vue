<template>
  <div class="base-checkbox">
    <label class="checkbox-label">
      <input
        type="checkbox"
        :checked="modelValue"
        @change="$emit('update:modelValue', $event.target.checked)"
        class="checkbox-input"
      />
      <span class="checkbox-custom"></span>
      <span class="checkbox-text">{{ label }}</span>
    </label>
    <div v-if="error" class="error-message">{{ error }}</div>
  </div>
</template>

<script setup>
defineProps({
  modelValue: {
    type: Boolean,
    default: false
  },
  label: {
    type: String,
    required: true
  },
  error: {
    type: String,
    default: ''
  }
})

defineEmits(['update:modelValue'])
</script>

<style scoped>
.base-checkbox {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}

.checkbox-label {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  cursor: pointer;
  user-select: none;
}

.checkbox-input {
  position: absolute;
  opacity: 0;
  cursor: pointer;
  height: 0;
  width: 0;
}

.checkbox-custom {
  position: relative;
  height: 20px;
  width: 20px;
  background-color: #fff;
  border: 2px solid var(--primary-color);
  border-radius: 4px;
  transition: all 0.2s;
}

.checkbox-input:checked ~ .checkbox-custom {
  background-color: var(--primary-color);
}

.checkbox-custom:after {
  content: "";
  position: absolute;
  display: none;
  left: 6px;
  top: 2px;
  width: 5px;
  height: 10px;
  border: solid white;
  border-width: 0 2px 2px 0;
  transform: rotate(45deg);
}

.checkbox-input:checked ~ .checkbox-custom:after {
  display: block;
}

.checkbox-text {
  font-size: 1rem;
  color: var(--text-color);
}

.error-message {
  color: #dc3545;
  font-size: 0.875rem;
  margin-top: 0.25rem;
}
</style> 