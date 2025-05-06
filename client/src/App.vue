<template>
  <div class="app">
    <Navbar v-if="!isDashboardRoute" />
    <main class="page-container">
      <router-view />
    </main>
    <Footer v-if="!isDashboardRoute" />
  </div>
</template>

<script>
import Navbar from './components/Navbar.vue'
import Footer from './components/Footer.vue'
import { computed } from 'vue'
import { useRoute } from 'vue-router'

export default {
  name: 'App',
  components: {
    Navbar,
    Footer,
  },
  setup() {
    const route = useRoute()
    const isDashboardRoute = computed(() => route.name === 'dashboard')

    return {
      isDashboardRoute
    }
  }
}
</script>

<style>
@import './assets/main.css';
</style>
