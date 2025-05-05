<template>
    <div class="modal fade" :class="{ show: modelValue }" tabindex="-1" role="dialog"
        :style="modelValue ? 'display: block;' : 'display: none;'" @click.self="close" aria-modal="true" aria-hidden="!modelValue">
        <div class="modal-dialog" :class="sizeClass" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ title }}</h5>
                    <button type="button" class="btn-close" @click="close" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <img v-if="image" :src="image" alt="Modal Image" class="modal-img mb-3" />
                    <slot />
                </div>
                <div class="modal-footer">
                    <slot name="footer">
                        <button type="button" class="btn btn-secondary" @click="close">Close</button>
                    </slot>
                </div>
            </div>
        </div>
        <div v-if="modelValue" class="modal-backdrop fade show"></div>
    </div>
</template>

<script setup>
import { computed, watch } from 'vue'

const props = defineProps({
    title: {
        type: String,
        default: ''
    },
    modelValue: {
        type: Boolean,
        default: false
    },
    size: {
        type: String,
        default: '' // '', 'modal-lg', 'modal-sm', etc.
    },
    image: {
        type: String,
        default: ''
    }
})
const emit = defineEmits(['update:modelValue', 'close'])

const sizeClass = computed(() => props.size ? props.size : '')

function close() {
    emit('update:modelValue', false)
    emit('close')
}

// Hide modal on ESC key
watch(() => props.modelValue, (val) => {
    if (val) {
        document.body.classList.add('modal-open')
    } else {
        document.body.classList.remove('modal-open')
    }
})
</script>

<style scoped>
.modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    transition: opacity 0.15s linear;
    background: rgba(0, 0, 0, 0.3);
    z-index: 1050;
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal[style*="display: none"] {
    pointer-events: none;
}

.modal-dialog {
    position: relative;
    width: auto;
    max-width: 500px;
    margin: 1.75rem auto;
    pointer-events: auto;
    z-index: 1051;
    transform: translateY(0);
}

.modal-content {
    position: relative;
    display: flex;
    flex-direction: column;
    width: 100%;
    background-color: #fff;
    background-clip: padding-box;
    border: 1px solid rgba(0, 0, 0, 0.2);
    border-radius: 0.3rem;
    outline: 0;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

.modal-backdrop {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 1040;
}

.modal-img {
    display: block;
    max-width: 100%;
    height: auto;
    margin-left: auto;
    margin-right: auto;
    border-radius: 0.5rem;
}
</style>