<template>
  <div class="settings-container">
    <div class="settings-card">
      <h2 class="settings-title">Account Settings</h2>
      
      <!-- Profile Settings -->
      <div class="settings-section">
        <h3>Profile Settings</h3>
        <form @submit.prevent="updateProfile" class="settings-form">
          <div class="form-group">
            <label for="name">Full Name</label>
            <input 
              type="text" 
              id="name" 
              v-model="profileForm.name" 
              class="form-control"
              :class="{ 'is-invalid': profileErrors.name }"
            >
            <div class="invalid-feedback" v-if="profileErrors.name">
              {{ profileErrors.name[0] }}
            </div>
          </div>

          <div class="form-group">
            <label for="email">Email Address</label>
            <input 
              type="email" 
              id="email" 
              v-model="profileForm.email" 
              class="form-control"
              :class="{ 'is-invalid': profileErrors.email }"
            >
            <div class="invalid-feedback" v-if="profileErrors.email">
              {{ profileErrors.email[0] }}
            </div>
          </div>

          <div class="form-group">
            <label for="phone">Phone Number</label>
            <input 
              type="tel" 
              id="phone" 
              v-model="profileForm.phone" 
              class="form-control"
              :class="{ 'is-invalid': profileErrors.phone }"
            >
            <div class="invalid-feedback" v-if="profileErrors.phone">
              {{ profileErrors.phone[0] }}
            </div>
          </div>

          <button type="submit" class="btn btn-primary" :disabled="profileLoading">
            <span v-if="profileLoading">Updating...</span>
            <span v-else>Update Profile</span>
          </button>
        </form>
      </div>

      <!-- Password Settings -->
      <div class="settings-section">
        <h3>Change Password</h3>
        <form @submit.prevent="updatePassword" class="settings-form">
          <div class="form-group">
            <label for="current_password">Current Password</label>
            <input 
              type="password" 
              id="current_password" 
              v-model="passwordForm.current_password" 
              class="form-control"
              :class="{ 'is-invalid': passwordErrors.current_password }"
            >
            <div class="invalid-feedback" v-if="passwordErrors.current_password">
              {{ passwordErrors.current_password[0] }}
            </div>
          </div>

          <div class="form-group">
            <label for="password">New Password</label>
            <input 
              type="password" 
              id="password" 
              v-model="passwordForm.password" 
              class="form-control"
              :class="{ 'is-invalid': passwordErrors.password }"
            >
            <div class="invalid-feedback" v-if="passwordErrors.password">
              {{ passwordErrors.password[0] }}
            </div>
          </div>

          <div class="form-group">
            <label for="password_confirmation">Confirm New Password</label>
            <input 
              type="password" 
              id="password_confirmation" 
              v-model="passwordForm.password_confirmation" 
              class="form-control"
              :class="{ 'is-invalid': passwordErrors.password_confirmation }"
            >
            <div class="invalid-feedback" v-if="passwordErrors.password_confirmation">
              {{ passwordErrors.password_confirmation[0] }}
            </div>
          </div>

          <button type="submit" class="btn btn-primary" :disabled="passwordLoading">
            <span v-if="passwordLoading">Updating...</span>
            <span v-else>Change Password</span>
          </button>
        </form>
      </div>

      <!-- Notification Settings -->
      <div class="settings-section">
        <h3>Notification Settings</h3>
        <div class="settings-form">
          <div class="form-group">
            <div class="form-check">
              <input 
                type="checkbox" 
                id="email_notifications" 
                v-model="notificationSettings.email_notifications" 
                class="form-check-input"
              >
              <label class="form-check-label" for="email_notifications">
                Email Notifications
              </label>
            </div>
          </div>

          <div class="form-group">
            <div class="form-check">
              <input 
                type="checkbox" 
                id="sms_notifications" 
                v-model="notificationSettings.sms_notifications" 
                class="form-check-input"
              >
              <label class="form-check-label" for="sms_notifications">
                SMS Notifications
              </label>
            </div>
          </div>

          <button @click="updateNotifications" class="btn btn-primary" :disabled="notificationLoading">
            <span v-if="notificationLoading">Updating...</span>
            <span v-else>Save Preferences</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import axios from 'axios'

const auth = useAuthStore()

// Profile Form
const profileForm = ref({
  name: '',
  email: '',
  phone: ''
})
const profileErrors = ref({})
const profileLoading = ref(false)

// Password Form
const passwordForm = ref({
  current_password: '',
  password: '',
  password_confirmation: ''
})
const passwordErrors = ref({})
const passwordLoading = ref(false)

// Notification Settings
const notificationSettings = ref({
  email_notifications: true,
  sms_notifications: false
})
const notificationLoading = ref(false)

// Load user data
onMounted(async () => {
  try {
    const response = await axios.get('/api/user')
    const user = response.data
    profileForm.value = {
      name: user.name,
      email: user.email,
      phone: user.phone || ''
    }
    notificationSettings.value = {
      email_notifications: user.email_notifications ?? true,
      sms_notifications: user.sms_notifications ?? false
    }
  } catch (error) {
    console.error('Error loading user data:', error)
  }
})

// Update Profile
const updateProfile = async () => {
  profileLoading.value = true
  profileErrors.value = {}
  
  try {
    await axios.put('/api/user/profile', profileForm.value)
    auth.setUser({ ...auth.user, ...profileForm.value })
    // Show success message
  } catch (error) {
    if (error.response?.data?.errors) {
      profileErrors.value = error.response.data.errors
    }
  } finally {
    profileLoading.value = false
  }
}

// Update Password
const updatePassword = async () => {
  passwordLoading.value = true
  passwordErrors.value = {}
  
  try {
    await axios.put('/api/user/password', passwordForm.value)
    passwordForm.value = {
      current_password: '',
      password: '',
      password_confirmation: ''
    }
    // Show success message
  } catch (error) {
    if (error.response?.data?.errors) {
      passwordErrors.value = error.response.data.errors
    }
  } finally {
    passwordLoading.value = false
  }
}

// Update Notifications
const updateNotifications = async () => {
  notificationLoading.value = true
  
  try {
    await axios.put('/api/user/notifications', notificationSettings.value)
    // Show success message
  } catch (error) {
    console.error('Error updating notification settings:', error)
  } finally {
    notificationLoading.value = false
  }
}
</script>

<style scoped>
.settings-container {
  max-width: 800px;
  margin: 0 auto;
}

.settings-card {
  background: var(--white-color);
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  padding: 2rem;
}

.settings-title {
  font-size: 1.5rem;
  font-weight: 600;
  margin-bottom: 2rem;
  color: var(--text-color);
}

.settings-section {
  margin-bottom: 2rem;
  padding-bottom: 2rem;
  border-bottom: 1px solid #e0e0e0;
}

.settings-section:last-child {
  margin-bottom: 0;
  padding-bottom: 0;
  border-bottom: none;
}

.settings-section h3 {
  font-size: 1.25rem;
  font-weight: 500;
  margin-bottom: 1.5rem;
  color: var(--text-color);
}

.settings-form {
  max-width: 500px;
}

.form-group {
  margin-bottom: 1.5rem;
}

.form-group label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 500;
  color: var(--text-color);
}

.form-control {
  width: 100%;
  padding: 0.75rem;
  border: 1px solid #e0e0e0;
  border-radius: 4px;
  font-size: 1rem;
  transition: border-color 0.2s;
}

.form-control:focus {
  border-color: var(--primary-color);
  outline: none;
}

.form-control.is-invalid {
  border-color: #dc3545;
}

.invalid-feedback {
  color: #dc3545;
  font-size: 0.875rem;
  margin-top: 0.25rem;
}

.btn {
  padding: 0.75rem 1.5rem;
  border: none;
  border-radius: 4px;
  font-size: 1rem;
  font-weight: 500;
  cursor: pointer;
  transition: background-color 0.2s;
}

.btn-primary {
  background-color: var(--primary-color);
  color: white;
}

.btn-primary:hover {
  background-color: var(--primary-dark);
}

.btn-primary:disabled {
  background-color: #ccc;
  cursor: not-allowed;
}

.form-check {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin-bottom: 1rem;
}

.form-check-input {
  width: 1.25rem;
  height: 1.25rem;
}

.form-check-label {
  font-weight: normal;
}
</style> 