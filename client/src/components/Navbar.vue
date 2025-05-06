<template>
  <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
      <router-link class="navbar-brand" to="/">Event Management</router-link>
      <button
        class="navbar-toggler"
        type="button"
        data-bs-toggle="collapse"
        data-bs-target="#navbarNav"
        aria-controls="navbarNav"
        aria-expanded="false"
        aria-label="Toggle navigation"
      >
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav me-auto">
          <li class="nav-item">
            <router-link class="nav-link" to="/">Home</router-link>
          </li>
          <li class="nav-item">
            <router-link class="nav-link" to="/events">Events</router-link>
          </li>
          <!-- Admin Navigation -->
          <!-- <template v-if="auth.isAdmin">
            <li class="nav-item">
              <router-link class="nav-link" to="/admin/users">Manage Users</router-link>
            </li>
          </template> -->
          <!-- Organizer Navigation -->
          <!-- <template v-if="auth.isOrganizer">
            <li class="nav-item">
              <router-link class="nav-link" to="/dashboard">Dashboard</router-link>
            </li>
          </template> -->
          <!-- Attendee Navigation -->
          <!-- <template v-if="auth.isAttendee">
            <li class="nav-item">
              <router-link class="nav-link" to="/attendee/bookings">My Bookings</router-link>
            </li>
          </template> -->
        </ul>
        <div class="d-flex align-items-center">
          <!-- Authenticated User Menu -->
          <template v-if="auth.isAuthenticated">
            <div class="dropdown">
              <button
                class="btn btn-outline-light dropdown-toggle"
                type="button"
                id="userMenu"
                data-bs-toggle="dropdown"
                aria-expanded="false"
              >
                {{ auth.user?.name }}
              </button>
              <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userMenu">
                <li>
                  <router-link class="dropdown-item" to="/dashboard">
                    <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                  </router-link>
                </li>
                <li>
                  <router-link class="dropdown-item" to="/profile">
                    <i class="fas fa-user me-2"></i>Profile
                  </router-link>
                </li>
                <li>
                  <router-link class="dropdown-item" to="/settings">
                    <i class="fas fa-cog me-2"></i>Settings
                  </router-link>
                </li>
                <li><hr class="dropdown-divider" /></li>
                <li>
                  <a class="dropdown-item" href="#" @click.prevent="handleLogout">
                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                  </a>
                </li>
              </ul>
            </div>
          </template>
          <!-- Guest Menu -->
          <template v-else>
            <router-link to="/login" class="btn btn-outline-light me-2">Login</router-link>
            <router-link to="/signup" class="btn btn-light">Register</router-link>
          </template>
        </div>
      </div>
    </div>
  </nav>
</template>

<script>
import { useAuthStore } from '@/stores/auth'
import { useToast } from 'vue-toastification'
import { useRouter } from 'vue-router'

export default {
  name: 'Navbar',
  setup() {
    const auth = useAuthStore()
    const toast = useToast()
    const router = useRouter()

    const handleLogout = async () => {
      try {
        const result = await auth.logout()
        if (result.success) {
          toast.success('Logged out successfully')
          router.push('/login')
        } else {
          toast.error(result.error)
        }
      } catch (error) {
        toast.error('An error occurred during logout')
      }
    }

    return {
      auth,
      handleLogout,
    }
  },
}
</script>

<style scoped>
.navbar {
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.dropdown-menu {
  min-width: 200px;
}

.dropdown-item {
  padding: 0.5rem 1rem;
}

.dropdown-item i {
  width: 20px;
  text-align: center;
}
</style>
