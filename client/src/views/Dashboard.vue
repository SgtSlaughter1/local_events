<template>
  <DashboardLayout :page-title="currentPageTitle">
    <!-- Admin Dashboard -->
    <template v-if="auth.isAdmin">
      <AdminDashboard v-if="currentView === 'dashboard'" />
      <Profile v-else-if="currentView === 'profile'" />
      <Settings v-else-if="currentView === 'settings'" />
    </template>

    <!-- Organizer Dashboard -->
    <template v-else-if="auth.isOrganizer">
      <OrganizerDashboard v-if="currentView === 'dashboard'" />
      <Profile v-else-if="currentView === 'profile'" />
      <Settings v-else-if="currentView === 'settings'" />
    </template>

    <!-- Attendee Dashboard -->
    <template v-else-if="auth.isAttendee">
      <Profile v-if="currentView === 'profile'" />
      <Settings v-else-if="currentView === 'settings'" />
    </template>

    <!-- No Access -->
    <template v-else>
      <div class="no-access">
        <h2>Access Denied</h2>
        <p>You don't have permission to access this area.</p>
      </div>
    </template>
  </DashboardLayout>
</template>

<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import DashboardLayout from '@/components/Dashboard/DashboardLayout.vue'
import AdminDashboard from '@/components/Dashboard/AdminDashboard.vue'
import OrganizerDashboard from '@/components/Dashboard/OrganizerDashboard.vue'
import Profile from '@/views/Profile.vue'
import Settings from '@/views/Settings.vue'

const route = useRoute()
const auth = useAuthStore()

const currentView = computed(() => {
  const path = route.path
  if (path === '/dashboard') return 'dashboard'
  if (path === '/dashboard/profile') return 'profile'
  if (path === '/dashboard/settings') return 'settings'
  return ''
})

const currentPageTitle = computed(() => {
  switch (currentView.value) {
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
.no-access {
  text-align: center;
  padding: 2rem;
}

.no-access h2 {
  color: var(--text-color);
  margin-bottom: 1rem;
}

.no-access p {
  color: #6c757d;
}
</style>
