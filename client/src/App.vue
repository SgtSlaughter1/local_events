<template>
  <div class="app">
    <Navbar />
    <main class="page-container">
      <router-view />
    </main>
    <Footer />
  </div>
</template>

<script>
import Navbar from './components/Navbar.vue'
import Footer from './components/Footer.vue'

export default {
  name: 'App',
  components: {
    Navbar,
    Footer,
  },
}
</script>

<style>
@import './assets/main.css';
</style>
