<template>
  <div class="filters-section">
    <h3 class="filters-title">Filters</h3>

    <!-- Price Filter -->
    <div class="filter-group">
      <h4 class="filter-heading">Price</h4>
      <div class="filter-options">
        <label class="filter-option">
          <input type="checkbox" v-model="filters.price" value="free" />
          <span>Free</span>
        </label>
        <label class="filter-option">
          <input type="checkbox" v-model="filters.price" value="paid" />
          <span>Paid</span>
        </label>
      </div>
    </div>

    <!-- Date Filter -->
    <div class="filter-group">
      <h4 class="filter-heading">Date</h4>
      <div class="filter-options">
        <label class="filter-option">
          <input type="checkbox" v-model="filters.date" value="today" />
          <span>Today</span>
        </label>
        <label class="filter-option">
          <input type="checkbox" v-model="filters.date" value="tomorrow" />
          <span>Tomorrow</span>
        </label>
        <label class="filter-option">
          <input type="checkbox" v-model="filters.date" value="this_week" />
          <span>This Week</span>
        </label>
        <label class="filter-option">
          <input type="checkbox" v-model="filters.date" value="this_weekend" />
          <span>This Weekend</span>
        </label>
        <label class="filter-option">
          <input type="checkbox" v-model="filters.date" value="pick_date" />
          <span>Pick a Date</span>
        </label>
        <input
          v-if="filters.date.includes('pick_date')"
          type="date"
          v-model="filters.customDate"
          class="custom-date-input"
        />
      </div>
    </div>

    <!-- Category Filter -->
    <div class="filter-group">
      <h4 class="filter-heading">Category</h4>
      <div class="filter-options">
        <label v-for="category in categories" :key="category.id" class="filter-option">
          <input type="checkbox" :value="category.id" v-model="filters.categories" />
          <span>{{ category.name }}</span>
        </label>
      </div>
    </div>

    <!-- Format Filter -->
    <div class="filter-group">
      <h4 class="filter-heading">Format</h4>
      <div class="filter-options">
        <label class="filter-option">
          <input type="checkbox" v-model="filters.format" value="community" />
          <span>Community Engagement</span>
        </label>
        <label class="filter-option">
          <input type="checkbox" v-model="filters.format" value="literary" />
          <span>Literary & Books</span>
        </label>
        <label class="filter-option">
          <input type="checkbox" v-model="filters.format" value="conference" />
          <span>Conference</span>
        </label>
        <label class="filter-option">
          <input type="checkbox" v-model="filters.format" value="attraction" />
          <span>Attraction Events</span>
        </label>
        <label class="filter-option">
          <input type="checkbox" v-model="filters.format" value="festival" />
          <span>Festival & Fairs</span>
        </label>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'
import { useEventStore } from '../stores/event'

const eventStore = useEventStore()
const categories = ref([])

const filters = ref({
  price: [],
  date: [],
  customDate: '',
  categories: [],
  format: [],
})

// Watch for changes in the store's categories
watch(
  () => eventStore.categories,
  (newCategories) => {
    categories.value = newCategories
  },
  { immediate: true },
)

// Watch for filter changes
watch(
  filters,
  (newFilters) => {
    eventStore.setFilters(newFilters)
  },
  { deep: true },
)
</script>

<style scoped>
.filters-section {
  width: 280px;
  background: white;
  padding: 1.5rem;
  position: sticky;
  top: 4rem;
  z-index: 100;
}

.filters-title {
  font-size: 1.1rem;
  font-weight: 600;
  margin-bottom: 1.5rem;
}

.filter-group {
  margin-bottom: 1.5rem;
  border-bottom: 1px solid #eee;
  padding-bottom: 1rem;
}

.filter-group:last-child {
  border-bottom: none;
}

.filter-heading {
  font-weight: 500;
  margin-bottom: 0.75rem;
  color: #333;
}

.filter-options {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.filter-option {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  cursor: pointer;
  font-size: 0.9rem;
  color: #666;
}

.filter-option input[type='checkbox'] {
  width: 16px;
  height: 16px;
  border: 2px solid #ddd;
  border-radius: 3px;
}

.custom-date-input {
  margin-top: 0.5rem;
  padding: 0.5rem;
  border: 1px solid #ddd;
  border-radius: 4px;
  width: 100%;
}
</style>
