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
                <label for="name">Full Name</label>
                <input
                  type="text"
                  id="name"
                  v-model="name"
                  :class="{ error: errors.name }"
                  placeholder="Enter your full name"
                  @input="validateName"
                  required
                />
                <span v-if="errors.name" class="error-message">{{ errors.name }}</span>
              </div>
              <div class="form-group">
                <label for="email">Email Address</label>
                <input
                  type="email"
                  id="email"
                  v-model="email"
                  :class="{ error: errors.email }"
                  placeholder="Enter your email"
                  @input="validateEmail"
                  required
                />
                <span v-if="errors.email" class="error-message">{{ errors.email }}</span>
              </div>
              <div class="form-group">
                <label for="password">Password</label>
                <div class="password-input">
                  <input
                    :type="showPassword ? 'text' : 'password'"
                    id="password"
                    v-model="password"
                    :class="{ error: errors.password }"
                    placeholder="Enter your password"
                    @input="validatePassword"
                    required
                  />
                  <BaseButton
                    variant="secondary"
                    size="small"
                    class="toggle-password"
                    @click="showPassword = !showPassword"
                  >
                    <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                  </BaseButton>
                </div>
                <span v-if="errors.password" class="error-message">{{ errors.password }}</span>
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
  </div>
</template>

<script>
import { useAuthStore } from '@/stores/auth'
import { useToast } from 'vue-toastification'
import BaseButton from '@/components/BaseButton.vue'

export default {
  name: 'Signup',
  components: {
    BaseButton
  },
  data() {
    return {
      name: '',
      email: '',
      password: '',
      showPassword: false,
      loading: {
        google: false,
        facebook: false,
        email: false,
      },
      errors: {
        name: '',
        email: '',
        password: '',
      },
      error: null,
    }
  },
  computed: {
    isValid() {
      return (
        this.name &&
        this.email &&
        this.password &&
        !this.errors.name &&
        !this.errors.email &&
        !this.errors.password
      )
    },
  },
  methods: {
    validateName() {
      if (!this.name) {
        this.errors.name = 'Name is required'
      } else if (this.name.length < 2) {
        this.errors.name = 'Name must be at least 2 characters'
      } else {
        this.errors.name = ''
      }
    },
    validateEmail() {
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
      if (!this.email) {
        this.errors.email = 'Email is required'
      } else if (!emailRegex.test(this.email)) {
        this.errors.email = 'Please enter a valid email address'
      } else {
        this.errors.email = ''
      }
    },
    validatePassword() {
      if (!this.password) {
        this.errors.password = 'Password is required'
      } else if (this.password.length < 6) {
        this.errors.password = 'Password must be at least 6 characters'
      } else {
        this.errors.password = ''
      }
    },
    async handleSubmit() {
      this.validateName()
      this.validateEmail()
      this.validatePassword()

      if (!this.isValid) return

      try {
        this.loading.email = true
        this.error = null

        const auth = useAuthStore()
        const toast = useToast()

        const result = await auth.register({
          name: this.name,
          email: this.email,
          password: this.password,
          user_type_id: 3, // Default to Attendee
          phone: '',
          address: '',
        })

        if (result.success) {
          toast.success('Registration successful! Please login to continue.')
          this.$router.push('/login')
        } else {
          this.error = result.error
          toast.error(result.error)
        }
      } catch (error) {
        this.error = 'Registration failed. Please try again.'
        const toast = useToast()
        toast.error(this.error)
      } finally {
        this.loading.email = false
      }
    },
    async handleGoogleSignup() {
      try {
        this.loading.google = true
        this.error = null
        const toast = useToast()

        // Implement Google signup logic here
        await new Promise((resolve) => setTimeout(resolve, 1500)) // Simulate API call

        toast.success('Google signup successful!')
        this.$router.push('/dashboard')
      } catch (error) {
        this.error = 'Google signup failed. Please try again.'
        const toast = useToast()
        toast.error(this.error)
      } finally {
        this.loading.google = false
      }
    },
    async handleFacebookSignup() {
      try {
        this.loading.facebook = true
        this.error = null
        const toast = useToast()

        // Implement Facebook signup logic here
        await new Promise((resolve) => setTimeout(resolve, 1500)) // Simulate API call

        toast.success('Facebook signup successful!')
        this.$router.push('/dashboard')
      } catch (error) {
        this.error = 'Facebook signup failed. Please try again.'
        const toast = useToast()
        toast.error(this.error)
      } finally {
        this.loading.facebook = false
      }
    },
  },
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
</style>
