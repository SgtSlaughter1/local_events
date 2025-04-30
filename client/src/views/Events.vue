<template>
  <div class="events-page">
    <div class="hero">
      <div class="container">
        <div class="hero-content text-center">
          <h1 class="display-4 fw-bold mb-4">Discover Amazing Events</h1>
          <p class="lead subtitle">Find and join the most exciting events in your area</p>
          
          <!-- Search and Filter Section -->
          <div class="row justify-content-center mt-5">
            <div class="col-md-8">
              <div class="input-group">
                <input 
                  type="text" 
                  class="form-control form-control-lg"
                  placeholder="Search events..."
                  v-model="searchQuery"
                  @input="handleSearch"
                >
                <button class="btn btn-primary btn-lg" type="button">
                  <i class="fas fa-search"></i>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Events List Section -->
    <div class="events">
      <EventList />
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import EventList from '@/components/EventList.vue'

const searchQuery = ref('')

const handleSearch = () => {
  // TODO: Implement search functionality
  console.log('Searching for:', searchQuery.value)
}
</script>

<style scoped>
.events-page {
  min-height: 100vh;
  background-color: var(--light-color);
}

.hero {
  background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
  color: var(--white-color);
  padding: 5rem 0;
  margin-bottom: 2rem;
}

.hero h1 {
  color: var(--white-color);
  margin-bottom: 1.5rem;
}

.subtitle {
  color: var(--light-color);
  opacity: 0.9;
}

.form-control {
  border: none;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.form-control:focus {
  box-shadow: 0 0 0 0.25rem rgba(255, 224, 71, 0.25);
}

.events {
  padding: 2rem 0;
}
</style> 