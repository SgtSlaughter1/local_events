<template>
  <DashboardLayout :page-title="currentPageTitle">
    <!-- Admin Dashboard -->
    <template v-if="auth.isAdmin">
      <div v-if="currentView === 'dashboard'">
        <AdminDashboard />
      </div>
      <div v-else-if="currentView === 'profile'">
        <Profile />
      </div>
      <div v-else-if="currentView === 'settings'">
        <Settings />
      </div>
    </template>
    <!-- Organizer Dashboard -->
    <template v-else-if="auth.isOrganizer">
      <div v-if="currentView === 'dashboard'">
        <OrganizerDashboard />
      </div>
      <div v-else-if="currentView === 'my-events'">
        <MyEvents />
      </div>
      <div v-else-if="currentView === 'create-event'">
        <CreateEvent />
      </div>
      <div v-else-if="currentView === 'profile'">
        <Profile />
      </div>
      <div v-else-if="currentView === 'settings'">
        <Settings />
      </div>
    </template>
    <!-- Attendee Dashboard -->
    <!-- <template v-else-if="auth.isAttendee">
      <AttendeeDashboard />
    </template> -->
    <!-- No Access -->
    <template v-else>
      <div class="text-center py-5">
        <h3>Access Denied</h3>
        <p>You don't have permission to access this dashboard.</p>
      </div>
    </template>
  </DashboardLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import DashboardLayout from '@/components/Dashboard/DashboardLayout.vue'
import AdminDashboard from '@/components/Dashboard/AdminDashboard.vue'
import OrganizerDashboard from '@/components/Dashboard/OrganizerDashboard.vue'
// import AttendeeDashboard from '@/components/Dashboard/AttendeeDashboard.vue'
import MyEvents from '@/views/MyEvents.vue'
import CreateEvent from '@/views/CreateEvent.vue'
import Profile from '@/views/Profile.vue'
import Settings from '@/views/Settings.vue'

const auth = useAuthStore()
const route = useRoute()

const currentView = computed(() => {
  const path = route.path
  if (path.includes('/my-events')) return 'my-events'
  if (path.includes('/create-event')) return 'create-event'
  if (path.includes('/profile')) return 'profile'
  if (path.includes('/settings')) return 'settings'
  return 'dashboard'
})

const currentPageTitle = computed(() => {
  switch (currentView.value) {
    case 'my-events':
      return 'My Events'
    case 'create-event':
      return 'Create Event'
    case 'profile':
      return 'Profile'
    case 'settings':
      return 'Settings'
    default:
      return 'Dashboard'
  }
})
</script>

<style scoped>
.dashboard-text {
  font-size: 2rem;
  font-weight: bold;
  text-align: center;
  /* margin-top: 40px; */
}
</style>
