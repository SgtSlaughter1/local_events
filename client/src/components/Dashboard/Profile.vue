<template>
  <div class="profile-container">
    <div class="profile-card">
      <div class="profile-header">
        <div class="profile-avatar">
          <img :src="userAvatar" alt="Profile Avatar" class="avatar" />
          <div class="avatar-overlay">
            <label for="avatar-upload" class="avatar-upload-btn">
              <i class="fas fa-camera"></i>
            </label>
            <input 
              type="file" 
              id="avatar-upload" 
              accept="image/*" 
              @change="handleAvatarUpload" 
              class="hidden"
            >
          </div>
        </div>
        <div class="profile-info">
          <h2 class="profile-name">{{ user.name }}</h2>
          <p class="profile-role">{{ userRole }}</p>
          <p class="profile-email">{{ user.email }}</p>
        </div>
      </div>

      <!-- Profile Update Form -->
      <div class="profile-section">
        <h3>Update Profile</h3>
        <form @submit.prevent="updateProfile" class="profile-form">
          <BaseInput
            v-model="profileForm.name"
            label="Full Name"
            type="text" 
            :error="profileErrors.name?.[0]"
            required
          />

          <BaseInput
            v-model="profileForm.email"
            label="Email Address"
            type="email" 
            :error="profileErrors.email?.[0]"
            required
          />

          <BaseInput
            v-model="profileForm.phone"
            label="Phone Number"
            type="tel" 
            :error="profileErrors.phone?.[0]"
          />

          <BaseTextarea
            v-model="profileForm.address"
            label="Address"
            :error="profileErrors.address?.[0]"
            rows="3"
          />

          <BaseButton
            type="submit"
            :loading="auth.loading"
            variant="primary"
            class="mt-4"
          >
            {{ auth.loading ? 'Updating...' : 'Update Profile' }}
          </BaseButton>
        </form>
      </div>

      <div class="profile-stats">
        <div class="stat-item">
          <i class="fas fa-calendar-check"></i>
          <div class="stat-info">
            <span class="stat-value">{{ stats.totalEvents }}</span>
            <span class="stat-label">Total Events</span>
          </div>
        </div>
        <div class="stat-item">
          <i class="fas fa-ticket-alt"></i>
          <div class="stat-info">
            <span class="stat-value">{{ stats.totalBookings }}</span>
            <span class="stat-label">Total Bookings</span>
          </div>
        </div>
        <div class="stat-item">
          <i class="fas fa-star"></i>
          <div class="stat-info">
            <span class="stat-value">{{ stats.rating }}</span>
            <span class="stat-label">Rating</span>
          </div>
        </div>
      </div>

      <!-- Recent Activity -->
      <div class="profile-section">
        <h3>Recent Activity</h3>
        <div class="activity-list">
          <div v-for="activity in recentActivity" :key="activity.id" class="activity-item">
            <div class="activity-icon" :class="activity.type">
              <i :class="activity.icon"></i>
            </div>
            <div class="activity-details">
              <p class="activity-text">{{ activity.text }}</p>
              <span class="activity-time">{{ activity.time }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useToast } from 'vue-toastification'
import axios from 'axios'
import BaseInput from '@/components/Base/BaseInput.vue'
import BaseTextarea from '@/components/Base/BaseTextarea.vue'
import BaseButton from '@/components/Base/BaseButton.vue'

const toast = useToast()
const auth = useAuthStore()
const user = computed(() => auth.user)

const userAvatar = computed(() => user.value?.avatar || '/images/avatar.svg')
const userRole = computed(() => {
  if (auth.isAdmin) return 'Administrator'
  if (auth.isOrganizer) return 'Event Organizer'
  if (auth.isAttendee) return 'Attendee'
  return 'User'
})

// Profile Form
const profileForm = ref({
  name: '',
  email: '',
  phone: '',
  address: ''
})
const profileErrors = ref({})

const stats = ref({
  totalEvents: 0,
  totalBookings: 0,
  rating: 0
})

const recentActivity = ref([])

onMounted(async () => {
  try {
    // Load profile form data
    if (auth.user) {
      profileForm.value = {
        name: auth.user.name || '',
        email: auth.user.email || '',
        phone: auth.user.phone || '',
        address: auth.user.address || ''
      }
    }

    // Fetch fresh data from API
    const { success, data } = await auth.fetchUser()
    if (success && data) {
      profileForm.value = {
        name: data.name || '',
        email: data.email || '',
        phone: data.phone || '',
        address: data.address || ''
      }
    }

    // Fetch user stats
    const statsResponse = await axios.get('/api/user/stats')
    stats.value = statsResponse.data.stats

    // Fetch recent activity
    const activityResponse = await axios.get('/api/user/activity')
    recentActivity.value = activityResponse.data.activities
  } catch (error) {
    console.error('Error loading profile data:', error)
    toast.error('Failed to load profile data. Please refresh the page.')
  }
})

// Update Profile
const updateProfile = async () => {
  profileErrors.value = {}
  
  try {
    const { success, error } = await auth.updateProfile(profileForm.value)
    if (success) {
      toast.success('Profile updated successfully')
    } else {
      if (error?.response?.data?.errors) {
        profileErrors.value = error.response.data.errors
        // Show first error message in toast
        const firstError = Object.values(error.response.data.errors)[0][0]
        toast.error(firstError)
      } else {
        toast.error(error?.response?.data?.message || 'Failed to update profile. Please try again.')
      }
    }
  } catch (error) {
    if (error.response?.data?.errors) {
      profileErrors.value = error.response.data.errors
      // Show first error message in toast
      const firstError = Object.values(error.response.data.errors)[0][0]
      toast.error(firstError)
    } else {
      toast.error('Failed to update profile. Please try again.')
    }
  }
}

const handleAvatarUpload = async (event) => {
  const file = event.target.files[0]
  if (!file) return

  const formData = new FormData()
  formData.append('avatar', file)

  try {
    const response = await axios.post('/api/user/avatar', formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    })
    
    // Update the user's avatar in the auth store
    auth.setUser({ ...auth.user, avatar: response.data.avatar_url })
    
    // Show success message
    toast.success('Avatar updated successfully')
  } catch (error) {
    console.error('Error uploading avatar:', error)
    toast.error('Failed to upload avatar. Please try again.')
  }
}
</script>

<style scoped>
.profile-container {
  max-width: 800px;
  margin: 0 auto;
}

.profile-card {
  background: var(--white-color);
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  padding: 2rem;
}

.profile-header {
  display: flex;
  align-items: center;
  gap: 2rem;
  margin-bottom: 2rem;
}

.profile-avatar {
  position: relative;
  width: 120px;
  height: 120px;
}

.avatar {
  width: 100%;
  height: 100%;
  border-radius: 50%;
  object-fit: cover;
}

.avatar-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  opacity: 0;
  transition: opacity 0.2s;
}

.profile-avatar:hover .avatar-overlay {
  opacity: 1;
}

.avatar-upload-btn {
  color: white;
  cursor: pointer;
  font-size: 1.5rem;
}

.hidden {
  display: none;
}

.profile-info {
  flex: 1;
}

.profile-name {
  font-size: 1.75rem;
  font-weight: 600;
  margin: 0 0 0.5rem;
  color: var(--text-color);
}

.profile-role {
  font-size: 1rem;
  color: var(--primary-color);
  margin: 0 0 0.5rem;
}

.profile-email {
  font-size: 1rem;
  color: var(--text-color);
  margin: 0;
}

.profile-form {
  max-width: 500px;
  margin-bottom: 2rem;
}

.profile-stats {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 1.5rem;
  margin-bottom: 2rem;
  padding: 1.5rem;
  background: #f8f9fa;
  border-radius: 8px;
}

.stat-item {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.stat-item i {
  font-size: 1.5rem;
  color: var(--primary-color);
}

.stat-info {
  display: flex;
  flex-direction: column;
}

.stat-value {
  font-size: 1.25rem;
  font-weight: 600;
  color: var(--text-color);
}

.stat-label {
  font-size: 0.875rem;
  color: #6c757d;
}

.profile-section {
  margin-top: 2rem;
}

.profile-section h3 {
  font-size: 1.25rem;
  font-weight: 500;
  margin-bottom: 1.5rem;
  color: var(--text-color);
}

.activity-list {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.activity-item {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1rem;
  background: #f8f9fa;
  border-radius: 8px;
}

.activity-icon {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.25rem;
}

.activity-icon.event {
  background-color: #e3f2fd;
  color: #1976d2;
}

.activity-icon.booking {
  background-color: #e8f5e9;
  color: #2e7d32;
}

.activity-icon.rating {
  background-color: #fff3e0;
  color: #f57c00;
}

.activity-details {
  flex: 1;
}

.activity-text {
  margin: 0;
  color: var(--text-color);
}

.activity-time {
  font-size: 0.875rem;
  color: #6c757d;
}

.mt-4 {
  margin-top: 1rem;
}

@media (max-width: 768px) {
  .profile-header {
    flex-direction: column;
    text-align: center;
  }

  .profile-stats {
    grid-template-columns: 1fr;
  }
}
</style>
