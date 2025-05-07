<template>
  <div class="settings-container">
    <div class="settings-card">
      <h2 class="settings-title">Account Settings</h2>
      
      <!-- Profile Settings -->
      <div class="settings-section">
        <h3>Profile Settings</h3>
        <form @submit.prevent="updateProfile" class="settings-form">
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

      <!-- Password Settings -->
      <div class="settings-section">
        <h3>Change Password</h3>
        <form @submit.prevent="updatePassword" class="settings-form">
          <BaseInput
              v-model="passwordForm.current_password" 
            label="Current Password"
              type="password" 
            :error="passwordErrors.current_password?.[0]"
            required
          />

          <BaseInput
              v-model="passwordForm.password" 
            label="New Password"
              type="password" 
            :error="passwordErrors.password?.[0]"
            required
          />

          <BaseInput
              v-model="passwordForm.password_confirmation" 
            label="Confirm New Password"
            type="password"
            :error="passwordErrors.password_confirmation?.[0]"
            required
          />

          <BaseButton
            type="submit"
            :loading="passwordLoading"
            variant="primary"
            class="mt-4"
          >
            {{ passwordLoading ? 'Updating...' : 'Change Password' }}
          </BaseButton>
        </form>
      </div>

      <!-- Notification Settings -->
      
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import axios from 'axios'
import BaseInput from '@/components/Base/BaseInput.vue'
import BaseTextarea from '@/components/Base/BaseTextarea.vue'
import BaseButton from '@/components/Base/BaseButton.vue'
import { useToast } from 'vue-toastification'

const toast = useToast()
const auth = useAuthStore()

// Profile Form
const profileForm = ref({
  name: '',
  email: '',
  phone: '',
  address: ''
})
const profileErrors = ref({})

// Password Form
const passwordForm = ref({
  current_password: '',
  password: '',
  password_confirmation: ''
})
const passwordErrors = ref({})
const passwordLoading = ref(false)

// Load user data
onMounted(async () => {
  try {
    // First try to get data from auth store
    if (auth.user) {
    profileForm.value = {
        name: auth.user.name || '',
        email: auth.user.email || '',
        phone: auth.user.phone || '',
        address: auth.user.address || ''
      }
    }

    // Then fetch fresh data from API
    const { success, data } = await auth.fetchUser()
    if (success && data) {
      profileForm.value = {
        name: data.name || '',
        email: data.email || '',
        phone: data.phone || '',
        address: data.address || ''
      }
    }
  } catch (error) {
    console.error('Error loading user data:', error)
    toast.error('Failed to load user data. Please refresh the page.')
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

// Update Password
const updatePassword = async () => {
  passwordLoading.value = true
  passwordErrors.value = {}
  
  try {
    const { success, error } = await auth.updatePassword(passwordForm.value)
    if (success) {
    passwordForm.value = {
      current_password: '',
      password: '',
      password_confirmation: ''
    }
      toast.success('Password updated successfully')
    } else {
      if (error?.response?.data?.errors) {
        passwordErrors.value = error.response.data.errors
        // Show first error message in toast
        const firstError = Object.values(error.response.data.errors)[0][0]
        toast.error(firstError)
      } else {
        toast.error(error?.response?.data?.message || 'Failed to update password. Please try again.')
      }
    }
  } catch (error) {
    if (error.response?.data?.errors) {
      passwordErrors.value = error.response.data.errors
      // Show first error message in toast
      const firstError = Object.values(error.response.data.errors)[0][0]
      toast.error(firstError)
    } else {
      toast.error('Failed to update password. Please try again.')
    }
  } finally {
    passwordLoading.value = false
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

.mt-4 {
  margin-top: 1rem;
}
</style> 