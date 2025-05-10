<template>
  <div class="users-container">
    <div class="dashboard-content" :class="{ 'loading': userStore.isLoading }">
      <!-- Loading State -->
      <BaseLoading 
        :show="userStore.isLoading" 
        message="Loading users..."
      />

      <!-- Header Section -->
      <div class="header mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <div>
            <h2 class="mb-1">User Management</h2>
            <p class="text-muted mb-0">Manage and monitor user accounts</p>
          </div>
          <div class="d-flex gap-2">
            <div class="input-group">
              <span class="input-group-text bg-white">
                <i class="fas fa-search text-muted"></i>
              </span>
              <input 
                type="text" 
                class="form-control border-start-0" 
                placeholder="Search users..." 
                v-model="searchQuery"
                @input="handleSearch"
              >
            </div>
            <select class="form-select" v-model="roleFilter" @change="handleFilter">
              <option value="">All Roles</option>
              <option v-for="type in userTypes" :key="type.id" :value="type.id">
                {{ type.name }}
              </option>
            </select>
          </div>
        </div>

        <!-- Stats Cards -->
        <div class="row g-3 mb-4">
          <div class="col-md-3">
            <div class="card bg-primary text-white">
              <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                  <div>
                    <h6 class="card-title mb-0">Total Users</h6>
                    <h3 class="mb-0 mt-2">{{ userStore.getPagination.total }}</h3>
                  </div>
                  <i class="fas fa-users fa-2x opacity-50"></i>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="card bg-success text-white">
              <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                  <div>
                    <h6 class="card-title mb-0">Active Users</h6>
                    <h3 class="mb-0 mt-2">{{ activeUsers }}</h3>
                  </div>
                  <i class="fas fa-user-check fa-2x opacity-50"></i>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="card bg-info text-white">
              <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                  <div>
                    <h6 class="card-title mb-0">Organizers</h6>
                    <h3 class="mb-0 mt-2">{{ organizerCount }}</h3>
                  </div>
                  <i class="fas fa-user-tie fa-2x opacity-50"></i>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="card bg-warning text-white">
              <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                  <div>
                    <h6 class="card-title mb-0">Attendees</h6>
                    <h3 class="mb-0 mt-2">{{ attendeeCount }}</h3>
                  </div>
                  <i class="fas fa-user fa-2x opacity-50"></i>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Error State -->
      <div v-if="userStore.getError" class="alert alert-danger" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>
        {{ userStore.getError }}
      </div>

      <!-- Users Table -->
      <div v-else class="card">
        <div class="table-responsive">
          <table class="table table-hover mb-0">
            <thead>
              <tr>
                <th>User</th>
                <th>Role</th>
                <th>Status</th>
                <th>Joined</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="user in userStore.getUsers" :key="user.id">
                <td>
                  <div class="d-flex align-items-center">
                    <div class="avatar me-3">
                      <i class="fas fa-user-circle fa-2x" :class="getUserAvatarClass(user)"></i>
                    </div>
                    <div>
                      <div class="fw-bold">{{ user.name }}</div>
                      <div class="text-muted small">{{ user.email }}</div>
                      <div class="text-muted small">{{ user.phone || 'No phone' }}</div>
                    </div>
                  </div>
                </td>
                <td>
                  <span class="badge" :class="getRoleBadgeClass(user.user_type_id)">
                    {{ user.user_type?.name }}
                  </span>
                </td>
                <td>
                  <span class="badge" :class="user.is_active ? 'bg-success' : 'bg-danger'">
                    {{ user.is_active ? 'Active' : 'Inactive' }}
                  </span>
                </td>
                <td>
                  <div class="d-flex flex-column">
                    <span>{{ formatDate(user.created_at) }}</span>
                    <small class="text-muted">{{ formatTimeAgo(user.created_at) }}</small>
                  </div>
                </td>
                <td>
                  <div class="btn-group">
                    <BaseButton
                      variant="primary"
                      size="small"
                      @click="openUserDetails(user)"
                      title="View Details"
                    >
                      <i class="fas fa-eye"></i>
                    </BaseButton>
                    <BaseButton
                      variant="secondary"
                      size="small"
                      @click="openEditUser(user)"
                      title="Edit User"
                    >
                      <i class="fas fa-edit"></i>
                    </BaseButton>
                    <BaseButton
                      :variant="user.is_active ? 'danger' : 'success'"
                      size="small"
                      @click="toggleUserStatus(user)"
                      :title="user.is_active ? 'Deactivate User' : 'Activate User'"
                    >
                      <i :class="user.is_active ? 'fas fa-ban' : 'fas fa-check'"></i>
                    </BaseButton>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div class="card-footer d-flex justify-content-between align-items-center">
          <div class="text-muted">
            Showing {{ paginationInfo.from }} to {{ paginationInfo.to }} of {{ paginationInfo.total }} entries
          </div>
          <nav>
            <ul class="pagination mb-0">
              <li class="page-item" :class="{ disabled: currentPage === 1 }">
                <a class="page-link" href="#" @click.prevent="changePage(currentPage - 1)">
                  <i class="fas fa-chevron-left"></i>
                </a>
              </li>
              <li v-for="page in totalPages" :key="page" class="page-item" :class="{ active: currentPage === page }">
                <a class="page-link" href="#" @click.prevent="changePage(page)">{{ page }}</a>
              </li>
              <li class="page-item" :class="{ disabled: currentPage === totalPages }">
                <a class="page-link" href="#" @click.prevent="changePage(currentPage + 1)">
                  <i class="fas fa-chevron-right"></i>
                </a>
              </li>
            </ul>
          </nav>
        </div>
      </div>
    </div>

    <!-- User Details Modal -->
    <BaseModal
      v-model="showUserDetails"
      title="User Details"
      size="modal-lg"
    >
      <div v-if="selectedUser" class="row">
        <div class="col-md-6">
          <h6 class="mb-3">Basic Information</h6>
          <table class="table table-sm">
            <tr>
              <th>Name:</th>
              <td>{{ selectedUser.name }}</td>
            </tr>
            <tr>
              <th>Email:</th>
              <td>{{ selectedUser.email }}</td>
            </tr>
            <tr>
              <th>Phone:</th>
              <td>{{ selectedUser.phone || 'N/A' }}</td>
            </tr>
            <tr>
              <th>Address:</th>
              <td>{{ selectedUser.address || 'N/A' }}</td>
            </tr>
          </table>
        </div>
        <div class="col-md-6">
          <h6 class="mb-3">Account Information</h6>
          <table class="table table-sm">
            <tr>
              <th>Role:</th>
              <td>{{ selectedUser.user_type?.name }}</td>
            </tr>
            <tr>
              <th>Status:</th>
              <td>
                <span class="badge" :class="selectedUser.is_active ? 'bg-success' : 'bg-danger'">
                  {{ selectedUser.is_active ? 'Active' : 'Inactive' }}
                </span>
              </td>
            </tr>
            <tr>
              <th>Joined:</th>
              <td>{{ formatDate(selectedUser.created_at) }}</td>
            </tr>
          </table>
        </div>
      </div>
    </BaseModal>
  </div>
</template>

<script setup>
import { onMounted, computed, ref } from 'vue'
import { useUserStore } from '@/stores/user'
import BaseModal from '@/components/Base/BaseModal.vue'
import BaseButton from '@/components/Base/BaseButton.vue'
import BaseLoading from '@/components/Base/BaseLoading.vue'
import { formatDate, formatTimeAgo } from '@/utils/formatters'
import api from '@/services/api'

const userStore = useUserStore()
const searchQuery = ref('')
const roleFilter = ref('')
const selectedUser = ref(null)
const showUserDetails = ref(false)
const userTypes = ref([])
const searchTimeout = ref(null)

// Computed properties
const currentPage = computed(() => userStore.getPagination.current_page)
const totalPages = computed(() => Math.ceil(userStore.getPagination.total / userStore.getPagination.per_page))
const paginationInfo = computed(() => {
  const { current_page, total, per_page } = userStore.getPagination
  const from = (current_page - 1) * per_page + 1
  const to = Math.min(current_page * per_page, total)
  return { from, to, total }
})

// User statistics
const activeUsers = computed(() => {
  return userStore.getUsers.filter(user => user.is_active).length
})

const organizerCount = computed(() => {
  return userStore.getUsers.filter(user => user.user_type_id === 2).length
})

const attendeeCount = computed(() => {
  return userStore.getUsers.filter(user => user.user_type_id === 3).length
})

// Methods
const changePage = async (page) => {
  if (page < 1 || page > totalPages.value) return
  await fetchUsers(page)
}

const fetchUsers = async (page = 1) => {
  await userStore.fetchUsers({
    page,
    search: searchQuery.value,
    role: roleFilter.value
  })
}

const handleSearch = () => {
  // Debounce search to avoid too many API calls
  if (searchTimeout.value) clearTimeout(searchTimeout.value)
  searchTimeout.value = setTimeout(() => {
    fetchUsers(1)
  }, 300)
}

const handleFilter = () => {
  fetchUsers(1)
}

const getRoleBadgeClass = (userTypeId) => {
  const classes = {
    2: 'bg-primary', // Organizer
    3: 'bg-success', // Attendee
    4: 'bg-info'     // Vendor
  }
  return classes[userTypeId] || 'bg-secondary'
}

const getUserAvatarClass = (user) => {
  const classes = {
    2: 'text-primary', // Organizer
    3: 'text-success', // Attendee
    4: 'text-info'     // Vendor
  }
  return classes[user.user_type_id] || 'text-secondary'
}

const openUserDetails = (user) => {
  selectedUser.value = user
  showUserDetails.value = true
}

const openEditUser = (user) => {
  // Implement edit user functionality
  console.log('Edit user:', user)
}

const toggleUserStatus = async (user) => {
  // Implement toggle user status functionality
  console.log('Toggle status for:', user)
}

// Initialize
onMounted(async () => {
  try {
    // Fetch user types
    const res = await api.get('/api/user-types')
    userTypes.value = res.data.usertypes

    // Fetch initial users
    await fetchUsers()
  } catch (error) {
    console.error('Error initializing:', error)
  }
})
</script>

<style scoped>
.users-container {
  padding: 1rem;
}

.dashboard-content {
  position: relative;
  min-height: 400px;
}

.dashboard-content.loading {
  pointer-events: none;
}

.table th {
  font-weight: 600;
  background-color: #f8f9fa;
  border-bottom: 2px solid #dee2e6;
}

.badge {
  padding: 0.5em 0.75em;
  font-weight: 500;
}

.btn-group {
  display: flex;
  gap: 0.25rem;
}

.page-link {
  color: #0d6efd;
  cursor: pointer;
  padding: 0.5rem 0.75rem;
}

.page-item.active .page-link {
  background-color: #0d6efd;
  border-color: #0d6efd;
}

.avatar {
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.modal-body .table th {
  width: 120px;
  background: none;
  border-bottom: 1px solid #dee2e6;
}

.card {
  border: none;
  box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.input-group-text {
  border-right: none;
}

.form-control:focus {
  box-shadow: none;
  border-color: #dee2e6;
}

.table > :not(caption) > * > * {
  padding: 1rem;
}

.fa-2x {
  font-size: 1.5em;
}
</style> 