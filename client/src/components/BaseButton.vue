<template>
  <button
    :type="type"
    :disabled="disabled"
    :class="[
      'base-button',
      variant,
      size,
      { 'is-loading': loading },
      { 'is-full-width': fullWidth }
    ]"
    @click="$emit('click', $event)"
  >
    <span v-if="loading" class="button-loader"></span>
    <slot v-else></slot>
  </button>
</template>

<script setup>
import { defineProps, defineEmits } from 'vue';

const props = defineProps({
  type: {
    type: String,
    default: 'button',
    validator: (value) => ['button', 'submit', 'reset'].includes(value)
  },
  variant: {
    type: String,
    default: 'primary',
    validator: (value) => ['primary', 'secondary', 'success', 'danger', 'warning', 'info'].includes(value)
  },
  size: {
    type: String,
    default: 'medium',
    validator: (value) => ['small', 'medium', 'large'].includes(value)
  },
  disabled: {
    type: Boolean,
    default: false
  },
  loading: {
    type: Boolean,
    default: false
  },
  fullWidth: {
    type: Boolean,
    default: false
  }
});

defineEmits(['click']);
</script>

<style scoped>
.base-button {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 0.5rem 1rem;
  border-radius: 0.375rem;
  font-weight: 500;
  transition: all 0.2s ease-in-out;
  cursor: pointer;
  border: none;
  outline: none;
}

/* Variants */
.primary {
  background-color: var(--accent-color);
  color: var(--primary-color);
}

.primary:hover:not(:disabled) {
  background-color: var(--primary-color);
  color: var(--white-color);
}

.secondary {
  background-color: var(--secondary-color);
  color: var(--white-color);
}

.secondary:hover:not(:disabled) {
  background-color: var(--primary-color);
  color: var(--white-color);
}

.success {
  background-color: #10b981;
  color: var(--white-color);
}

.success:hover:not(:disabled) {
  background-color: #059669;
}

.danger {
  background-color: #ef4444;
  color: var(--white-color);
}

.danger:hover:not(:disabled) {
  background-color: #dc2626;
}

.warning {
  background-color: #f59e0b;
  color: var(--white-color);
}

.warning:hover:not(:disabled) {
  background-color: #d97706;
}

.info {
  background-color: var(--primary-color);
  color: var(--white-color);
}

.info:hover:not(:disabled) {
  background-color: var(--secondary-color);
}

/* Sizes */
.small {
  padding: 0.25rem 0.5rem;
  font-size: 0.875rem;
}

.medium {
  padding: 0.5rem 1rem;
  font-size: 1rem;
}

.large {
  padding: 0.75rem 1.5rem;
  font-size: 1.125rem;
}

/* States */
.is-loading {
  opacity: 0.7;
  cursor: not-allowed;
}

:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.is-full-width {
  width: 100%;
}

/* Loading animation */
.button-loader {
  width: 1rem;
  height: 1rem;
  border: 2px solid currentColor;
  border-radius: 50%;
  border-right-color: transparent;
  animation: spin 0.75s linear infinite;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}
</style> 