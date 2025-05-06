<template>
  <div>
    <AdminDashboard v-if="isAdmin" />
    <OrganizerDashboard v-else-if="isOrganizer" />
    <div v-else class="dashboard-text">You do not have access to this dashboard.</div>
  </div>
</template>

<script>
import { useAuthStore } from '@/stores/auth'
import AdminDashboard from '@/components/Dashboard/AdminDashboard.vue'
import OrganizerDashboard from '@/components/Dashboard/OrganizerDashboard.vue'

export default {
  name: 'Dashboard',
  components: {
    AdminDashboard,
    OrganizerDashboard,
  },
  setup() {
    const auth = useAuthStore()
    return {
      isAdmin: auth.isAdmin,
      isOrganizer: auth.isOrganizer,
    }
  },
}
</script>

<style scoped>
.dashboard-text {
  font-size: 2rem;
  font-weight: bold;
  text-align: center;
  margin-top: 40px;
}
</style>
