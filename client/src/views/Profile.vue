<template>
    <div class="profile-settings-container">
        <div class="settings-sidebar">
            <h3>Account Settings</h3>
            <ul class="settings-menu">
                <li :class="{active: activeTab === 'info'}" @click="activeTab = 'info'">Account Info</li>
                <li :class="{active: activeTab === 'email'}" @click="activeTab = 'email'">Change Email</li>
                <li :class="{active: activeTab === 'password'}" @click="activeTab = 'password'">Password</li>
            </ul>
        </div>
        <div class="settings-content">
            <div v-if="activeTab === 'info'">
                <h2 class="settings-title">Account Information</h2>
                <hr />
                <div class="profile-photo-section">
                    <div class="profile-photo-wrapper">
                        <img :src="user.avatar || '/images/default-avatar.png'" alt="Profile Photo" class="profile-photo" />
                        <label class="photo-upload-label">
                            <input type="file" accept="image/*" @change="onPhotoChange" hidden />
                            <span class="photo-upload-btn"><i class="fas fa-camera"></i></span>
                        </label>
                    </div>
                </div>
                <form class="profile-form" @submit.prevent="updateProfile">
                    <h4>Profile Information</h4>
                    <div class="form-row">
                        <div class="form-group">
                            <label>First Name:</label>
                            <input type="text" v-model="form.first_name" placeholder="Enter first name" />
                        </div>
                        <div class="form-group">
                            <label>Last Name:</label>
                            <input type="text" v-model="form.last_name" placeholder="Enter last name" />
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Website:</label>
                            <input type="text" v-model="form.website" placeholder="Enter website" />
                        </div>
                        <div class="form-group">
                            <label>Company:</label>
                            <input type="text" v-model="form.company" placeholder="Enter company name" />
                        </div>
                    </div>
                    <h4>Contact Details</h4>
                    <p class="contact-note">These details are private and only used to contact you for ticketing or prizes.</p>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Phone Number:</label>
                            <input type="text" v-model="form.phone" placeholder="Enter phone number" />
                        </div>
                        <div class="form-group">
                            <label>Address:</label>
                            <input type="text" v-model="form.address" placeholder="Enter address" />
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>City/Town:</label>
                            <input type="text" v-model="form.city" placeholder="Enter city" />
                        </div>
                        <div class="form-group">
                            <label>Country:</label>
                            <input type="text" v-model="form.country" placeholder="Enter country" />
                        </div>
                        <div class="form-group">
                            <label>Pincode:</label>
                            <input type="text" v-model="form.pincode" placeholder="Enter pincode" />
                        </div>
                    </div>
                    <button class="save-btn" type="submit">Save My Profile</button>
                </form>
            </div>
            <!-- Placeholders for other tabs -->
            <div v-else-if="activeTab === 'email'">
                <h2>Change Email</h2>
                <p>Coming soon...</p>
            </div>
            <div v-else-if="activeTab === 'password'">
                <h2>Change Password</h2>
                <p>Coming soon...</p>
            </div>
        </div>
    </div>
</template>

<script>
import { ref, onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useRouter } from 'vue-router'
import { useToast } from 'vue-toastification'

export default {
    name: 'Profile',
    setup() {
        const auth = useAuthStore()
        const router = useRouter()
        const toast = useToast()
        const activeTab = ref('info')
        const form = ref({
            first_name: '',
            last_name: '',
            website: '',
            company: '',
            phone: '',
            address: '',
            city: '',
            country: '',
            pincode: ''
        })
        const user = auth.user || {}

        const onPhotoChange = (e) => {
            // Placeholder for photo upload logic
            toast.info('Photo upload coming soon!')
        }

        const updateProfile = async () => {
            try {
                const response = await auth.updateProfile(form.value)
                if (response.success) {
                    toast.success('Profile updated successfully')
                } else {
                    toast.error(response.error)
                }
            } catch (error) {
                toast.error('Failed to update profile')
            }
        }

        onMounted(() => {
            if (!auth.isAuthenticated) {
                router.push('/login')
                return
            }
            // Populate form with user data if available
            Object.assign(form.value, user)
        })

        return {
            user,
            form,
            activeTab,
            onPhotoChange,
            updateProfile
        }
    }
}
</script>

<style scoped>

</style> 