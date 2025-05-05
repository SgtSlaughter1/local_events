<template>
  <div class="auth-container">
    <div class="auth-card">
      <div class="auth-content">
        <div class="auth-left">
          <div class="brand">
            <!-- <img src="@/assets/logo.png" alt="Eventify" class="logo"> -->
          </div>
          <h2>Welcome back!</h2>
          <p>Sign in to access your personalized events.</p>
        </div>
        <div class="auth-right">
          <div class="auth-form">
            <h3>Sign In</h3>
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
                @click="handleGoogleLogin"
              >
                <img src="@/assets/google.svg" alt="Google" />
                Sign in with Google
              </BaseButton>
              <BaseButton
                variant="secondary"
                size="large"
                class="social-btn facebook"
                :loading="loading.facebook"
                :disabled="loading.google || loading.facebook || loading.email"
                @click="handleFacebookLogin"
              >
                <img src="@/assets/facebook.svg" alt="Facebook" />
                Sign in with Facebook
              </BaseButton>
            </div>
            <div class="divider">
              <span>OR</span>
            </div>
            <form @submit.prevent="handleSubmit">
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
              <BaseButton
                type="submit"
                variant="primary"
                size="large"
                class="submit-btn"
                :loading="loading.email"
                :disabled="loading.google || loading.facebook || loading.email || !isValid"
              >
                Sign In
              </BaseButton>
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
import { ref, reactive, computed } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useToast } from 'vue-toastification'
import BaseButton from '@/components/Base/BaseButton.vue'
import BaseInput from '@/components/Base/BaseInput.vue'

export default {
  name: 'Login',
  components: {
    BaseButton,
    BaseInput
  },
  setup() {
    const email = ref('')
    const password = ref('')
    const showPassword = ref(false)
    const loading = reactive({
      google: false,
      facebook: false,
      email: false,
    })
    const errors = reactive({
      email: '',
      password: '',
    })
    const error = ref(null)

    const isValid = computed(() =>
      email.value &&
      password.value &&
      !errors.email &&
      !errors.password
    )

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

    const handleSubmit = async () => {
      validateEmail()
      validatePassword()
      if (!isValid.value) return

      try {
        loading.email = true
        error.value = null

        const auth = useAuthStore()
        const toast = useToast()

        const result = await auth.login({
          email: email.value,
          password: password.value,
        })

        if (result.success) {
          toast.success('Login successful!')
          window.location.href = '/dashboard'
        } else {
          error.value = result.error
          toast.error(result.error)
        }
      } catch (err) {
        error.value = 'Login failed. Please try again.'
        const toast = useToast()
        toast.error(error.value)
      } finally {
        loading.email = false
      }
    }

    const handleGoogleLogin = async () => {
      const toast = useToast()
      toast.info('Coming soon!')
    }
    const handleFacebookLogin = async () => {
      const toast = useToast()
      toast.info('Coming soon!')
    }

    return {
      email,
      password,
      showPassword,
      loading,
      errors,
      error,
      isValid,
      validateEmail,
      validatePassword,
      handleSubmit,
      handleGoogleLogin,
      handleFacebookLogin,
    }
  }
}
</script>
