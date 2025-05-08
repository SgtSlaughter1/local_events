<template>
    <div class="dashboard-layout">
        <!-- Topbar -->
        <div class="dashboard-topbar">
            <div class="topbar-left">
                <button class="menu-toggle" @click="toggleSidebar">
                    <i class="fas fa-bars"></i>
                </button>
                <h1 class="page-title">{{ pageTitle }}</h1>
            </div>
            <div class="topbar-right">
                <div class="user-menu">
                    <div class="user-info" @click="toggleUserMenu">
                        <img :src="userAvatar" alt="User Avatar" class="avatar" />
                        <span class="username">{{ userName }}</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="user-dropdown" v-if="showUserMenu">
                        <router-link to="/dashboard/profile" class="dropdown-item">
                            <i class="fas fa-user"></i> Profile
                        </router-link>
                        <router-link to="/dashboard/settings" class="dropdown-item">
                            <i class="fas fa-cog"></i> Settings
                        </router-link>
                        <div class="dropdown-divider"></div>
                        <a href="#" class="dropdown-item" @click.prevent="handleLogout">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="dashboard-sidebar" :class="{ 'collapsed': isSidebarCollapsed }">
            <div class="sidebar-header">
                <h2 class="brand-name" :class="{ 'collapsed': isSidebarCollapsed }">Event Management</h2>
            </div>
            
            <!-- User Info in Sidebar -->
            <!-- <div class="sidebar-user-info" :class="{ 'collapsed': isSidebarCollapsed }">
                <img :src="userAvatar" alt="User Avatar" class="sidebar-avatar" />
                <div class="sidebar-user-details">
                    <h3 class="sidebar-username">{{ userName }}</h3>
                    <span class="sidebar-user-role">{{ userRole }}</span>
                </div>
            </div> -->

            <nav class="sidebar-nav">
                <!-- Home Link -->
                <router-link to="/" class="nav-item" :class="{ active: currentView === 'home' }">
                    <i class="fas fa-home"></i>
                    <span>Home</span>
                </router-link>

                <!-- Admin Navigation -->
                <template v-if="auth.isAdmin">
                    <router-link to="/dashboard" class="nav-item" :class="{ active: currentView === 'dashboard' }">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </router-link>
                    <router-link to="/dashboard/users" class="nav-item" :class="{ active: currentView === 'users' }">
                        <i class="fas fa-users"></i>
                        <span>Manage Users</span>
                    </router-link>
                    <router-link to="/dashboard/categories" class="nav-item" :class="{ active: currentView === 'categories' }">
                        <i class="fas fa-tags"></i>
                        <span>Categories</span>
                    </router-link>
                    <router-link to="/dashboard/analytics" class="nav-item" :class="{ active: currentView === 'analytics' }">
                        <i class="fas fa-chart-bar"></i>
                        <span>Analytics</span>
                    </router-link>
                </template>

                <!-- Organizer Navigation -->
                <template v-if="auth.isOrganizer">
                    <router-link to="/dashboard" class="nav-item" :class="{ active: currentView === 'dashboard' }">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </router-link>
                    <router-link to="/dashboard/my-events" class="nav-item" :class="{ active: currentView === 'my-events' }">
                        <i class="fas fa-calendar-alt"></i>
                        <span>My Events</span>
                    </router-link>
                    <router-link to="/dashboard/create-event" class="nav-item" :class="{ active: currentView === 'create-event' }">
                        <i class="fas fa-plus-circle"></i>
                        <span>Create Event</span>
                    </router-link>
                    <router-link to="/dashboard/my-bookings" class="nav-item" :class="{ active: currentView === 'my-bookings' }">
                        <i class="fas fa-ticket-alt"></i>
                        <span>Bookings</span>
                    </router-link>
                </template>

                <!-- Attendee Navigation -->
                <template v-if="auth.isAttendee">
                    <router-link to="/dashboard" class="nav-item" :class="{ active: currentView === 'dashboard' }">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </router-link>
                </template>

                <!-- Common Navigation Items -->
                <div class="nav-divider"></div>
                <router-link to="/dashboard/profile" class="nav-item" :class="{ active: currentView === 'profile' }">
                    <i class="fas fa-user"></i>
                    <span>Profile</span>
                </router-link>
                <router-link to="/dashboard/settings" class="nav-item" :class="{ active: currentView === 'settings' }">
                    <i class="fas fa-cog"></i>
                    <span>Settings</span>
                </router-link>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="dashboard-content" :class="{ 'expanded': isSidebarCollapsed }">
            <slot></slot>
        </div>
    </div>
</template>

<script>
import { ref, computed } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useRouter, useRoute } from 'vue-router'
import { useToast } from 'vue-toastification'

export default {
    name: 'DashboardLayout',
    props: {
        pageTitle: {
            type: String,
            default: 'Dashboard'
        }
    },
    setup() {
        const auth = useAuthStore()
        const router = useRouter()
        const route = useRoute()
        const toast = useToast()
        const isSidebarCollapsed = ref(false)
        const showUserMenu = ref(false)

        const currentView = computed(() => {
            const path = route.path
            if (path === '/') return 'home'
            if (path === '/dashboard') return 'dashboard'
            if (path === '/dashboard/profile') return 'profile'
            if (path === '/dashboard/settings') return 'settings'
            if (path === '/dashboard/my-events') return 'my-events'
            if (path === '/dashboard/create-event') return 'create-event'
            if (path === '/dashboard/my-bookings') return 'my-bookings'
            if (path === '/dashboard/users') return 'users'
            if (path === '/dashboard/categories') return 'categories'
            if (path === '/dashboard/analytics') return 'analytics'
            return ''
        })

        const userAvatar = computed(() => auth.user?.avatar || '/images/avatar.svg')
        const userName = computed(() => auth.user?.name || 'User')
        const userRole = computed(() => {
            if (auth.isAdmin) return 'Administrator'
            if (auth.isOrganizer) return 'Event Organizer'
            if (auth.isAttendee) return 'Attendee'
            return 'User'
        })

        const toggleSidebar = () => {
            isSidebarCollapsed.value = !isSidebarCollapsed.value
        }

        const toggleUserMenu = () => {
            showUserMenu.value = !showUserMenu.value
        }

        const handleLogout = async () => {
            try {
                await auth.logout()
                toast.success('Logged out successfully')
                router.push('/login')
            } catch (error) {
                console.error('Logout failed:', error)
                toast.error('Failed to logout. Please try again.')
            }
        }

        return {
            auth,
            isSidebarCollapsed,
            showUserMenu,
            userAvatar,
            userName,
            userRole,
            currentView,
            toggleSidebar,
            toggleUserMenu,
            handleLogout
        }
    }
}
</script>

<style scoped>
.dashboard-layout {
    display: grid;
    grid-template-areas:
        "topbar topbar"
        "sidebar content";
    grid-template-columns: 250px 1fr;
    grid-template-rows: 60px 1fr;
    min-height: 100vh;
}

.dashboard-topbar {
    grid-area: topbar;
    background: var(--white-color);
    border-bottom: 1px solid #e0e0e0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0 1.5rem;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    height: 60px;
    z-index: 1000;
}

.topbar-left {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.menu-toggle {
    background: none;
    border: none;
    font-size: 1.25rem;
    color: var(--text-color);
    cursor: pointer;
    padding: 0.5rem;
}

.page-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--text-color);
    margin: 0;
}

.topbar-right {
    display: flex;
    align-items: center;
}

.user-menu {
    position: relative;
}

.user-info {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    cursor: pointer;
    padding: 0.5rem;
    border-radius: 0.5rem;
    transition: background-color 0.2s;
}

.user-info:hover {
    background-color: #f5f5f5;
}

.avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    object-fit: cover;
}

.username {
    font-weight: 500;
    color: var(--text-color);
}

.user-dropdown {
    position: absolute;
    top: 100%;
    right: 0;
    background: var(--white-color);
    border: 1px solid #e0e0e0;
    border-radius: 0.5rem;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    min-width: 200px;
    margin-top: 0.5rem;
}

.dropdown-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem 1rem;
    color: var(--text-color);
    text-decoration: none;
    transition: background-color 0.2s;
}

.dropdown-item:hover {
    background-color: #f5f5f5;
}

.dropdown-divider {
    height: 1px;
    background-color: #e0e0e0;
    margin: 0.5rem 0;
}

.dashboard-sidebar {
    grid-area: sidebar;
    background: var(--primary-color);
    color: var(--white-color);
    padding: 1.5rem;
    position: fixed;
    top: 60px;
    left: 0;
    bottom: 0;
    width: 250px;
    transition: all 0.3s ease;
    z-index: 900;
}

.sidebar-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 2rem;
}

.brand-name {
    font-size: 1.25rem;
    font-weight: 600;
    margin: 0;
    transition: opacity 0.3s ease;
}

.brand-name.collapsed {
    opacity: 0;
    width: 0;
    overflow: hidden;
}

.sidebar-nav {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.nav-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 0.75rem 1rem;
    color: var(--white-color);
    text-decoration: none;
    border-radius: 0.5rem;
    transition: all 0.2s;
}

.nav-item span {
    transition: opacity 0.3s ease;
}

.nav-item span.collapsed {
    opacity: 0;
    width: 0;
    overflow: hidden;
}

.nav-item:hover {
    background-color: rgba(255, 255, 255, 0.1);
}

.nav-item.active {
    background-color: var(--accent-color);
    color: var(--primary-color);
}

.dashboard-content {
    grid-area: content;
    padding: 1rem;
    transition: margin-left 0.3s ease;
}

.dashboard-content.expanded {
    margin-left: 80px;
}

/* Collapsed Sidebar Styles */
.dashboard-sidebar.collapsed {
    width: 80px;
    padding: 1.5rem 0.75rem;
}

.dashboard-sidebar.collapsed .nav-item {
    justify-content: center;
    padding: 0.75rem;
}

.dashboard-sidebar.collapsed .nav-item span {
    display: none;
}

.dashboard-sidebar.collapsed .nav-item i {
    margin: 0;
    font-size: 1.25rem;
}

/* Responsive Styles */
@media (max-width: 768px) {
    .dashboard-layout {
        grid-template-columns: 1fr;
    }

    .dashboard-sidebar {
        transform: translateX(-250px);
    }

    .dashboard-sidebar.collapsed {
        transform: translateX(0);
        width: 250px;
    }

    .dashboard-content {
        margin-left: 0;
    }

    .dashboard-content.expanded {
        margin-left: 0;
    }

    .username {
        display: none;
    }
}

.sidebar-user-info {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    margin-bottom: 1.5rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    transition: all 0.3s ease;
}

.sidebar-user-info.collapsed {
    justify-content: center;
    padding: 1rem 0.5rem;
}

.sidebar-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
}

.sidebar-user-details {
    transition: opacity 0.3s ease;
}

.sidebar-user-info.collapsed .sidebar-user-details {
    opacity: 0;
    width: 0;
    overflow: hidden;
}

.sidebar-username {
    font-size: 1rem;
    font-weight: 600;
    margin: 0;
    color: var(--white-color);
}

.sidebar-user-role {
    font-size: 0.875rem;
    color: rgba(255, 255, 255, 0.7);
}

/* Add styles for home link */
.nav-item i.fa-home {
    font-size: 1.25rem;
}

/* Update active state for home link */
.nav-item.active {
    background-color: var(--accent-color);
    color: var(--primary-color);
}

/* Ensure home link is visible in collapsed state */
.dashboard-sidebar.collapsed .nav-item i.fa-home {
    margin: 0;
}
</style>