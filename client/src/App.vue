<template>
  <div class="app">
    <Navbar v-if="!isDashboardRoute" />
    <main class="main-content">
      <router-view />
    </main>
    <Footer v-if="!isDashboardRoute" />
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'
import Navbar from '@/components/Navbar.vue'
import Footer from '@/components/Footer.vue'

const route = useRoute()

const isDashboardRoute = computed(() => {
  return route.path.startsWith('/dashboard')
})
</script>

<style>
.app {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}

.main-content {
  flex: 1;
}
</style>
