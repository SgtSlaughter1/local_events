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
                            <button class="social-btn google" :class="{ loading: loading.google }"
                                @click="handleGoogleSignup"
                                :disabled="loading.google || loading.facebook || loading.email">
                                <img src="@/assets/google.svg" alt="Google">
                                Sign up with Google
                            </button>
                            <button class="social-btn facebook" :class="{ loading: loading.facebook }"
                                @click="handleFacebookSignup"
                                :disabled="loading.google || loading.facebook || loading.email">
                                <img src="@/assets/facebook.svg" alt="Facebook">
                                Sign up with Facebook
                            </button>
                        </div>
                        <div class="divider">
                            <span>OR</span>
                        </div>
                        <form @submit.prevent="handleSubmit">
                            <div class="form-group">
                                <label for="name">Full Name</label>
                                <input type="text" id="name" v-model="name" :class="{ error: errors.name }"
                                    placeholder="Enter your full name" @input="validateName" required>
                                <span v-if="errors.name" class="error-message">{{ errors.name }}</span>
                            </div>
                            <div class="form-group">
                                <label for="email">Email Address</label>
                                <input type="email" id="email" v-model="email" :class="{ error: errors.email }"
                                    placeholder="Enter your email" @input="validateEmail" required>
                                <span v-if="errors.email" class="error-message">{{ errors.email }}</span>
                            </div>
                            <div class="form-group">
                                <label for="password">Password</label>
                                <div class="password-input">
                                    <input :type="showPassword ? 'text' : 'password'" id="password" v-model="password"
                                        :class="{ error: errors.password }" placeholder="Enter your password"
                                        @input="validatePassword" required>
                                    <button type="button" class="toggle-password" @click="showPassword = !showPassword">
                                        <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                                    </button>
                                </div>
                                <span v-if="errors.password" class="error-message">{{ errors.password }}</span>
                            </div>
                            <button type="submit" class="submit-btn" :class="{ loading: loading.email }"
                                :disabled="loading.google || loading.facebook || loading.email || !isValid">
                                Create Account
                            </button>
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
export default {
    name: 'Signup',
    data() {
        return {
            name: '',
            email: '',
            password: '',
            showPassword: false,
            loading: {
                google: false,
                facebook: false,
                email: false
            },
            errors: {
                name: '',
                email: '',
                password: ''
            },
            error: null
        }
    },
    computed: {
        isValid() {
            return this.name &&
                this.email &&
                this.password &&
                !this.errors.name &&
                !this.errors.email &&
                !this.errors.password
        }
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

                // Replace this with your actual API call
                await new Promise(resolve => setTimeout(resolve, 1500)) // Simulate API call

                // Simulated API response
                const response = { success: true }

                if (response.success) {
                    // Handle successful signup
                    this.$router.push('/dashboard')
                }
            } catch (error) {
                this.error = 'Registration failed. Please try again.'
            } finally {
                this.loading.email = false
            }
        },
        async handleGoogleSignup() {
            try {
                this.loading.google = true
                this.error = null

                // Implement Google signup logic here
                await new Promise(resolve => setTimeout(resolve, 1500)) // Simulate API call

                this.$router.push('/dashboard')
            } catch (error) {
                this.error = 'Google signup failed. Please try again.'
            } finally {
                this.loading.google = false
            }
        },
        async handleFacebookSignup() {
            try {
                this.loading.facebook = true
                this.error = null

                // Implement Facebook signup logic here
                await new Promise(resolve => setTimeout(resolve, 1500)) // Simulate API call

                this.$router.push('/dashboard')
            } catch (error) {
                this.error = 'Facebook signup failed. Please try again.'
            } finally {
                this.loading.facebook = false
            }
        }
    }
}
</script>