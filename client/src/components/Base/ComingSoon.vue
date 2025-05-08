<template>
  <div class="coming-soon">
    <div class="coming-soon-container">
      <i :class="[icon, 'fa-4x', 'mb-4', 'text-primary']"></i>
      <h1>{{ title }}</h1>
      <p class="lead">{{ message }}</p>
      <p>{{ description }}</p>
    </div>
  </div>
</template>

<script setup>
defineProps({
  title: {
    type: String,
    required: true
  },
  message: {
    type: String,
    default: 'This feature is coming soon!'
  },
  description: {
    type: String,
    default: 'We are working hard to bring you this feature. Stay tuned for updates!'
  },
  icon: {
    type: String,
    default: 'fas fa-rocket'
  }
})
</script>

<style scoped>
.coming-soon {
  padding: 2rem;
  min-height: calc(100vh - 60px);
  display: flex;
  align-items: center;
  justify-content: center;
}

.coming-soon-container {
  text-align: center;
  padding: 3rem;
  background: var(--white-color);
  border-radius: 1rem;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  max-width: 600px;
  width: 100%;
}

.coming-soon-container i {
  color: var(--accent-color);
  margin-bottom: 1.5rem;
}

.coming-soon-container h1 {
  color: var(--primary-color);
  margin-bottom: 1rem;
  font-weight: 600;
}

.coming-soon-container .lead {
  color: var(--text-color);
  font-size: 1.25rem;
  margin-bottom: 1rem;
}

.coming-soon-container p {
  color: var(--text-color);
  opacity: 0.8;
}
</style> 