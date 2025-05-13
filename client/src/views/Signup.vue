<template>
  <div class="auth-container">
    <div class="auth-card">
      <div class="auth-content">
        <div class="auth-left">
          <div class="brand">
            <!-- <img src="@/assets/logo.png" alt="Eventify" class="logo"> -->
          </div>
          <h2>Discover tailored events.</h2>
          <p>Sign up for personalized recommendations today!</p>
        </div>
        <div class="auth-right">
          <div class="auth-form">
            <h3>Create Account</h3>
            <div v-if="error" class="alert alert-danger">
              {{ error }}
            </div>
            <div class="social-buttons">
              <BaseButton
                variant="secondary"
                size="large"
                class="social-btn google"
                :loading="loading.google"
                :disabled="loading.google || loading.facebook || loading.email"
                @click="handleGoogleSignup"
              >
                <img src="@/assets/google.svg" alt="Google" />
                Sign up with Google
              </BaseButton>
              <BaseButton
                variant="secondary"
                size="large"
                class="social-btn facebook"
                :loading="loading.facebook"
                :disabled="loading.google || loading.facebook || loading.email"
                @click="handleFacebookSignup"
              >
                <img src="@/assets/facebook.svg" alt="Facebook" />
                Sign up with Facebook
              </BaseButton>
            </div>
            <div class="divider">
              <span>OR</span>
            </div>
            <form @submit.prevent="handleSubmit">
              <div class="form-group">
                <BaseInput
                  v-model="name"
                  label="Full Name"
                  placeholder="Enter your full name"
                  :error="errors.name"
                  @input="validateName"
                  required
                />
              </div>
              <div class="form-group">
                <BaseInput
                  v-model="email"
                  label="Email Address"
                  placeholder="Enter your email"
                  type="email"
                  :error="errors.email"
                  @input="validateEmail"
                  required
                />
              </div>
              <div class="form-group">
                <BaseInput
                  v-model="password"
                  :type="showPassword ? 'text' : 'password'"
                  label="Password"
                  placeholder="Enter your password"
                  :error="errors.password"
                  @input="validatePassword"
                  required
                >
                  <template #append>
                    <BaseButton
                      variant="secondary"
                      size="small"
                      class="toggle-password"
                      @click="showPassword = !showPassword"
                      type="button"
                    >
                      <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                    </BaseButton>
                  </template>
                </BaseInput>
              </div>
              <div class="form-group">
                <BaseSelect
                  v-model="usertype"
                  label="Role"
                  placeholder="Select user type"
                  :options="usertypeOptions"
                  :error="errors.usertype"
                  @change="validateUsertype"
                  required
                />
              </div>
              <BaseButton
                type="submit"
                variant="primary"
                size="large"
                class="submit-btn"
                :loading="loading.email"
                :disabled="loading.google || loading.facebook || loading.email || !isValid"
              >
                Create Account
              </BaseButton>
            </form>
            <p class="auth-switch">
              Already have an account? <router-link to="/login">Sign in</router-link>
            </p>
          </div>
        </div>
      </div>
    </div>

    <!-- Role Selection Modal -->
    <div v-if="showRoleModal" class="modal-overlay">
      <div class="modal-content">
        <h3>Select Your Role</h3>
        <p>Please choose how you'll use Eventify:</p>
        <div class="role-options">
          <div 
            v-for="type in usertypeOptions" 
            :key="type.value"
            class="role-option"
            :class="{ selected: selectedRole === type.value }"
            @click="selectedRole = type.value"
          >
            <div class="role-icon">
              <i :class="getRoleIcon(type.value)"></i>
            </div>
            <div class="role-info">
              <h4>{{ type.label }}</h4>
              <p>{{ getRoleDescription(type.value) }}</p>
            </div>
          </div>
        </div>
        <div class="modal-actions">
          <BaseButton
            variant="secondary"
            @click="cancelGoogleSignup"
          >
            Cancel
          </BaseButton>
          <BaseButton
            variant="primary"
            :disabled="!selectedRole"
            :loading="loading.google"
            @click="completeGoogleSignup"
          >
            Continue
          </BaseButton>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, reactive, computed, onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useToast } from 'vue-toastification'
import BaseButton from '@/components/Base/BaseButton.vue'
import BaseInput from '@/components/Base/BaseInput.vue'
import BaseSelect from '@/components/Base/BaseSelect.vue'
import api from '@/services/api'
import { useRouter } from 'vue-router'

// Add Google OAuth client ID
const GOOGLE_CLIENT_ID = import.meta.env.VITE_GOOGLE_CLIENT_ID

export default {
  name: 'Signup',
  components: {
    BaseButton,
    BaseInput,
    BaseSelect
  },
  setup() {
    const router = useRouter()
    const name = ref('')
    const email = ref('')
    const password = ref('')
    const usertype = ref('')
    const showPassword = ref(false)
    const usertypeOptions = ref([])
    const loading = reactive({
      google: false,
      facebook: false,
      email: false,
    })
    const errors = reactive({
      name: '',
      email: '',
      password: '',
      usertype: '',
    })
    const error = ref(null)
    const showRoleModal = ref(false)
    const selectedRole = ref(null)
    const googleUserData = ref(null)

    const isValid = computed(() =>
      name.value &&
      email.value &&
      password.value &&
      usertype.value &&
      !errors.name &&
      !errors.email &&
      !errors.password &&
      !errors.usertype
    )

    onMounted(async () => {
      try {
        const res = await api.get('/api/user-types')
        usertypeOptions.value = res.data.usertypes.map(type => ({
          value: type.id,
          label: type.name
        }))
      } catch (e) {
        usertypeOptions.value = []
      }
    })

    const validateName = () => {
      if (!name.value) {
        errors.name = 'Name is required'
      } else if (name.value.length < 2) {
        errors.name = 'Name must be at least 2 characters'
      } else {
        errors.name = ''
      }
    }
    const validateEmail = () => {
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
      if (!email.value) {
        errors.email = 'Email is required'
      } else if (!emailRegex.test(email.value)) {
        errors.email = 'Please enter a valid email address'
      } else {
        errors.email = ''
      }
    }
    const validatePassword = () => {
      if (!password.value) {
        errors.password = 'Password is required'
      } else if (password.value.length < 6) {
        errors.password = 'Password must be at least 6 characters'
      } else {
        errors.password = ''
      }
    }
    const validateUsertype = () => {
      if (!usertype.value) {
        errors.usertype = 'User type is required'
      } else {
        errors.usertype = ''
      }
    }

    const handleSubmit = async () => {
      validateName()
      validateEmail()
      validatePassword()
      validateUsertype()
      if (!isValid.value) return

      try {
        loading.email = true
        error.value = null

        const auth = useAuthStore()
        const toast = useToast()

        const result = await auth.register({
          name: name.value,
          email: email.value,
          password: password.value,
          user_type_id: usertype.value,
          phone: '',
          address: '',
        })

        if (result.success) {
          toast.success('Registration successful! Please login to continue.')
          router.push('/login')
        } else {
          error.value = result.error
          toast.error(result.error)
        }
      } catch (err) {
        error.value = 'Registration failed. Please try again.'
        const toast = useToast()
        toast.error(error.value)
      } finally {
        loading.email = false
      }
    }

    const getRoleIcon = (roleId) => {
      const icons = {
        2: 'fas fa-calendar-check', // Organizer
        3: 'fas fa-ticket-alt'      // Attendee
      }
      return icons[roleId] || 'fas fa-user'
    }

    const getRoleDescription = (roleId) => {
      const descriptions = {
        2: 'Create and manage events, handle registrations, and track attendance.',
        3: 'Discover and register for events, manage your bookings, and leave reviews.'
      }
      return descriptions[roleId] || ''
    }

    const handleGoogleSignup = async () => {
      try {
        loading.google = true
        error.value = null

        // Load the Google API client
        await new Promise((resolve, reject) => {
          const script = document.createElement('script')
          script.src = 'https://accounts.google.com/gsi/client'
          script.onload = resolve
          script.onerror = reject
          document.head.appendChild(script)
        })

        // Initialize Google Sign-In
        const client = google.accounts.oauth2.initTokenClient({
          client_id: import.meta.env.VITE_GOOGLE_CLIENT_ID,
          scope: 'email profile',
          callback: async (response) => {
            if (response.error) {
              throw new Error(response.error)
            }

            try {
              // Get user info from Google
              const userInfo = await fetch('https://www.googleapis.com/oauth2/v3/userinfo', {
                headers: { Authorization: `Bearer ${response.access_token}` }
              }).then(res => res.json())

              // Store the Google user data
              googleUserData.value = {
                google_id: userInfo.sub,
                name: userInfo.name,
                email: userInfo.email,
                google_token: response.access_token,
                google_refresh_token: response.refresh_token
              }

              // Show role selection modal
              showRoleModal.value = true
            } catch (err) {
              error.value = 'Failed to get user information from Google'
              const toast = useToast()
              toast.error(error.value)
            }
          }
        })

        // Trigger Google Sign-In
        client.requestAccessToken()
      } catch (err) {
        error.value = 'Failed to initialize Google Sign-In'
        const toast = useToast()
        toast.error(error.value)
      } finally {
        loading.google = false
      }
    }

    const completeGoogleSignup = async () => {
      if (!selectedRole.value || !googleUserData.value) return

      try {
        loading.google = true
        error.value = null

        const result = await api.post('/api/google-auth', {
          ...googleUserData.value,
          user_type_id: selectedRole.value
        })

        if (result.data) {
          // Store the token and user data
          localStorage.setItem('token', result.data.token)
          localStorage.setItem('user', JSON.stringify(result.data.user))

          const toast = useToast()
          toast.success('Registration successful!')
          router.push('/dashboard')
        }
      } catch (err) {
        error.value = err.response?.data?.message || 'Google signup failed'
        const toast = useToast()
        toast.error(error.value)
      } finally {
        loading.google = false
        showRoleModal.value = false
        googleUserData.value = null
        selectedRole.value = null
      }
    }

    const cancelGoogleSignup = () => {
      showRoleModal.value = false
      googleUserData.value = null
      selectedRole.value = null
    }

    const handleFacebookSignup = async () => {
      const toast = useToast()
      toast.info('Coming soon!')
    }

    return {
      name,
      email,
      password,
      usertype,
      usertypeOptions,
      showPassword,
      loading,
      errors,
      error,
      isValid,
      validateName,
      validateEmail,
      validatePassword,
      validateUsertype,
      handleSubmit,
      handleGoogleSignup,
      handleFacebookSignup,
      showRoleModal,
      selectedRole,
      getRoleIcon,
      getRoleDescription,
      completeGoogleSignup,
      cancelGoogleSignup
    }
  }
}
</script>

<style scoped>
.auth-container {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background-color: var(--light-color);
  padding: 2rem;
}

.auth-card {
  background: var(--white-color);
  border-radius: 1rem;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  width: 100%;
  max-width: 1000px;
  overflow: hidden;
}

.auth-content {
  display: flex;
  min-height: 600px;
}

.auth-left {
  flex: 1;
  background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
  color: var(--white-color);
  padding: 3rem;
  display: flex;
  flex-direction: column;
  justify-content: center;
}

.auth-right {
  flex: 1;
  padding: 3rem;
}

.auth-form {
  max-width: 400px;
  margin: 0 auto;
}

.social-buttons {
  display: flex;
  flex-direction: column;
  gap: 1rem;
  margin-bottom: 1.5rem;
}

.social-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  padding: 0.75rem;
  border-radius: 0.5rem;
  font-weight: 500;
  transition: all 0.3s ease;
}

.social-btn img {
  width: 24px;
  height: 24px;
}

.social-btn.google {
  background-color: var(--white-color);
  color: var(--text-color);
  border: 1px solid #e0e0e0;
}

.social-btn.facebook {
  background-color: #1877f2;
  color: var(--white-color);
}

.divider {
  display: flex;
  align-items: center;
  text-align: center;
  margin: 1.5rem 0;
}

.divider::before,
.divider::after {
  content: '';
  flex: 1;
  border-bottom: 1px solid #e0e0e0;
}

.divider span {
  padding: 0 1rem;
  color: #666;
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

.form-group input {
  width: 100%;
  padding: 0.75rem;
  border: 1px solid #e0e0e0;
  border-radius: 0.5rem;
  font-size: 1rem;
  transition: all 0.3s ease;
}

.form-group input:focus {
  outline: none;
  border-color: var(--primary-color);
  box-shadow: 0 0 0 2px rgba(255, 224, 71, 0.25);
}

.form-group input.error {
  border-color: #dc3545;
}

.error-message {
  color: #dc3545;
  font-size: 0.875rem;
  margin-top: 0.25rem;
}

.password-input {
  position: relative;
}

.toggle-password {
  position: absolute;
  right: 0.75rem;
  top: 50%;
  transform: translateY(-50%);
  background: none;
  border: none;
  color: #666;
  cursor: pointer;
}

.submit-btn {
  width: 100%;
  margin-top: 1.5rem;
}

.auth-switch {
  text-align: center;
  margin-top: 1.5rem;
  color: #666;
}

.auth-switch a {
  color: var(--primary-color);
  text-decoration: none;
  font-weight: 500;
}

.auth-switch a:hover {
  text-decoration: underline;
}

.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.modal-content {
  background: white;
  padding: 2.5rem;
  border-radius: 1rem;
  width: 90%;
  max-width: 600px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.modal-content h3 {
  margin: 0 0 0.5rem 0;
  color: var(--text-color);
  font-size: 1.5rem;
}

.modal-content p {
  margin: 0 0 1.5rem 0;
  color: #666;
}

.role-options {
  display: grid;
  gap: 1rem;
  margin: 1.5rem 0;
}

.role-option {
  padding: 1.5rem;
  border: 2px solid #e0e0e0;
  border-radius: 0.5rem;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: flex-start;
  gap: 1rem;
}

.role-icon {
  font-size: 1.5rem;
  color: var(--primary-color);
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  background-color: rgba(255, 224, 71, 0.1);
  border-radius: 50%;
}

.role-info {
  flex: 1;
}

.role-info h4 {
  margin: 0 0 0.5rem 0;
  color: var(--text-color);
  font-size: 1.1rem;
}

.role-info p {
  margin: 0;
  color: #666;
  font-size: 0.875rem;
  line-height: 1.4;
}

.role-option:hover {
  border-color: var(--primary-color);
  background-color: rgba(255, 224, 71, 0.05);
}

.role-option.selected {
  border-color: var(--primary-color);
  background-color: rgba(255, 224, 71, 0.1);
}

.modal-actions {
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
  margin-top: 2rem;
  padding-top: 1.5rem;
  border-top: 1px solid #e0e0e0;
}
</style>
