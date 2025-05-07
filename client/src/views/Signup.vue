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

export default {
  name: 'Signup',
  components: {
    BaseButton,
    BaseInput,
    BaseSelect
  },
  setup() {
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

    const router = useRouter()

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

    const handleGoogleSignup = async () => {
      const toast = useToast()
      toast.info('Coming soon!')
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
</style>
