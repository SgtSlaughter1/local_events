<template>
  <DashboardLayout :page-title="currentPageTitle">
    <!-- Dashboard Components -->
    <AdminDashboard v-if="currentView === 'dashboard' && auth.isAdmin" />
    <OrganizerDashboard v-else-if="currentView === 'dashboard' && auth.isOrganizer" />
    <AttendeeDashboard v-else-if="currentView === 'dashboard' && auth.isAttendee" />

    <!-- Admin Components -->
    <Users v-if="currentView === 'users' && auth.isAdmin" />
    <Categories v-if="currentView === 'categories' && auth.isAdmin" />
    <Analytics v-if="currentView === 'analytics' && auth.isAdmin" />

    <!-- Organizer Specific Components -->
    <template v-if="auth.isOrganizer">
      <MyEvents v-if="currentView === 'my-events'" />
      <CreateEvent v-else-if="currentView === 'create-event'" />
    </template>

    <!-- Global Components -->
    <Profile v-if="currentView === 'profile'" />
    <Settings v-else-if="currentView === 'settings'" />

    <!-- No Access -->
    <template v-if="!hasAccess">
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
import AttendeeDashboard from '@/components/Dashboard/AttendeeDashboard.vue'
import MyEvents from '../components/Dashboard/MyEvents.vue'
import CreateEvent from '../views/CreateEvent.vue'
import Profile from '@/components/Dashboard/Profile.vue'
import Settings from '@/components/Dashboard/Settings.vue'
import Users from '@/components/admin/Users.vue'
import Categories from '@/components/admin/Categories.vue'
import Analytics from '@/components/admin/Analytics.vue'

const route = useRoute()
const auth = useAuthStore()

const currentView = computed(() => {
  const path = route.path
  if (path === '/dashboard') return 'dashboard'
  if (path === '/dashboard/my-events') return 'my-events'
  if (path === '/dashboard/create-event') return 'create-event'
  if (path === '/dashboard/profile') return 'profile'
  if (path === '/dashboard/settings') return 'settings'
  if (path === '/dashboard/users') return 'users'
  if (path === '/dashboard/categories') return 'categories'
  if (path === '/dashboard/analytics') return 'analytics'
  return ''
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
    case 'users':
      return 'User Management'
    case 'categories':
      return 'Categories'
    case 'analytics':
      return 'Analytics'
    default:
      return 'Dashboard'
  }
})

const hasAccess = computed(() => {
  const view = currentView.value
  
  // Global components are accessible to all authenticated users
  if (view === 'profile' || view === 'settings') return true
  
  // Dashboard view is handled by role-specific components
  if (view === 'dashboard') return true
  
  // My Events and Create Event are accessible to organizers and admins
  if (view === 'my-events' || view === 'create-event') {
    return auth.isOrganizer || auth.isAdmin
  }

  // Admin-only views
  if (view === 'users' || view === 'categories' || view === 'analytics') {
    return auth.isAdmin
  }
  
  return false
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
