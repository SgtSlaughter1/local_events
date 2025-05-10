<template>
    <div v-if="show" class="loading-overlay" :class="{ 'full-page': fullPage }">
        <div class="loading-spinner">
            <i class="fas fa-spinner fa-spin"></i>
            <span>{{ message }}</span>
        </div>
    </div>
</template>

<script setup>
defineProps({
    show: {
        type: Boolean,
        default: true
    },
    message: {
        type: String,
        default: 'Loading...'
    },
    fullPage: {
        type: Boolean,
        default: false
    }
})
</script>

<style scoped>
.loading-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: white;
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 100;
}

.loading-overlay.full-page {
    position: fixed;
    z-index: 1000;
}

.loading-spinner {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1rem;
    padding: 2rem;
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.loading-spinner i {
    font-size: 2rem;
    color: var(--primary-color);
}

.loading-spinner span {
    color: var(--text-color);
    font-weight: 500;
}
</style>