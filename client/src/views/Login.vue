<template>
  <div class="auth-container">
    <div class="auth-card">
      <div class="auth-content">
        <div class="auth-left">
          <div class="brand">
            <!-- <img src="@/assets/logo.png" alt="Eventify" class="logo"> -->
          </div>
          <h2>Discover tailored events.</h2>
          <p>Sign in for personalized recommendations today!</p>
        </div>
        <div class="auth-right">
          <div class="auth-form">
            <h3>Login</h3>
            <div v-if="error" class="alert alert-danger">
              {{ error }}
            </div>
            <div class="social-buttons">
              <button
                class="social-btn google"
                :class="{ loading: loading.google }"
                @click="handleGoogleLogin"
                :disabled="loading.google || loading.facebook || loading.email"
              >
                <img src="@/assets/google.svg" alt="Google" />
                Sign in with Google
              </button>
              <button
                class="social-btn facebook"
                :class="{ loading: loading.facebook }"
                @click="handleFacebookLogin"
                :disabled="loading.google || loading.facebook || loading.email"
              >
                <img src="@/assets/facebook.svg" alt="Facebook" />
                Sign in with Facebook
              </button>
            </div>
            <div class="divider">
              <span>OR</span>
            </div>
            <form @submit.prevent="handleSubmit">
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
                  <button
                    type="button"
                    class="toggle-password"
                    @click="showPassword = !showPassword"
                  >
                    <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                  </button>
                </div>
                <span v-if="errors.password" class="error-message">{{ errors.password }}</span>
              </div>
              <button
                type="submit"
                class="submit-btn"
                :class="{ loading: loading.email }"
                :disabled="loading.google || loading.facebook || loading.email || !isValid"
              >
                Login
              </button>
            </form>
            <p class="auth-switch">
              Don't have an account? <router-link to="/signup">Sign up</router-link>
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

export default {
  name: 'Login',
  data() {
    return {
      email: '',
      password: '',
      showPassword: false,
      loading: {
        google: false,
        facebook: false,
        email: false,
      },
      errors: {
        email: '',
        password: '',
      },
      error: null,
    }
  },
  computed: {
    isValid() {
      return this.email && this.password && !this.errors.email && !this.errors.password
    },
  },
  methods: {
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
      this.validateEmail()
      this.validatePassword()

      if (!this.isValid) return

      try {
        this.loading.email = true
        this.error = null

        const auth = useAuthStore()
        const toast = useToast()

        const result = await auth.login({
          email: this.email,
          password: this.password,
        })

        if (result.success) {
          toast.success('Login successful!')
          this.$router.push('/dashboard')
        } else {
          this.error = result.error
          toast.error(result.error)
        }
      } catch (error) {
        this.error = 'Invalid email or password'
        const toast = useToast()
        toast.error(this.error)
      } finally {
        this.loading.email = false
      }
    },
    async handleGoogleLogin() {
      try {
        this.loading.google = true
        this.error = null
        const toast = useToast()

        // Implement Google login logic here
        await new Promise((resolve) => setTimeout(resolve, 1500)) // Simulate API call

        toast.success('Google login successful!')
        this.$router.push('/dashboard')
      } catch (error) {
        this.error = 'Google login failed. Please try again.'
        const toast = useToast()
        toast.error(this.error)
      } finally {
        this.loading.google = false
      }
    },
    async handleFacebookLogin() {
      try {
        this.loading.facebook = true
        this.error = null
        const toast = useToast()

        // Implement Facebook login logic here
        await new Promise((resolve) => setTimeout(resolve, 1500)) // Simulate API call

        toast.success('Facebook login successful!')
        this.$router.push('/dashboard')
      } catch (error) {
        this.error = 'Facebook login failed. Please try again.'
        const toast = useToast()
        toast.error(this.error)
      } finally {
        this.loading.facebook = false
      }
    },
  },
}
</script>
