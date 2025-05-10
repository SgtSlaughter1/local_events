import { createRouter, createWebHistory } from 'vue-router'
import LandingPage from '../views/LandingPage.vue'
import Login from '../views/Login.vue'
import Signup from '../views/Signup.vue'
import Events from '../views/Events.vue'
import EventDetails from '../views/EventDetails.vue'
import Profile from '../components/Dashboard/Profile.vue'
import Dashboard from '../views/Dashboard.vue'
import CreateEvent from '../views/CreateEvent.vue'
import TicketBooking from '../components/Ticket/TicketBooking.vue'
import Checkout from '../components/Ticket/Checkout.vue'
import PaymentSuccess from '../components/Ticket/PaymentSuccess.vue'
import AdminEvents from '../components/admin/Events.vue'
import AttendeeDetails from '../components/Ticket/AttendeeDetails.vue'
import OrderSummary from '../components/Ticket/OrderSummary.vue'
import { useAuthStore } from '../stores/auth'

const routes = [
  {
    path: '/',
    name: 'home',
    component: LandingPage,
  },
  {
    path: '/login',
    name: 'login',
    component: Login,
  },
  {
    path: '/signup',
    name: 'signup',
    component: Signup,
  },
  {
    path: '/events',
    name: 'events',
    component: Events,
  },
  {
    path: '/events/:id',
    name: 'EventDetails',
    component: EventDetails,
    props: true,
  },
  {
    path: '/events/:id/book',
    name: 'ticket-booking',
    component: TicketBooking,
    props: true,
  },
  {
    path: '/events/:id/attendee',
    name: 'attendee',
    component: AttendeeDetails,
    props: true,
  },
  {
    path: '/events/:id/summary',
    name: 'summary',
    component: OrderSummary,
    props: true,
  },
  {
    path: '/events/:id/checkout',
    name: 'checkout',
    component: Checkout,
    props: true,
    meta: { requiresAuth: true },
  },
  {
    path: '/events/:id/payment-success',
    name: 'payment-success',
    component: PaymentSuccess,
    props: true,
    meta: { requiresAuth: true },
  },
  {
    path: '/profile',
    name: 'profile',
    component: Profile,
    meta: { requiresAuth: true },
  },
  {
    path: '/dashboard',
    name: 'dashboard',
    component: Dashboard,
    meta: { requiresAuth: true },
  },
  {
    path: '/dashboard/my-events',
    name: 'dashboard-my-events',
    component: Dashboard,
    meta: { requiresAuth: true }
  },
  {
    path: '/dashboard/create-event',
    name: 'dashboard-create-event',
    component: Dashboard,
    meta: { requiresAuth: true },
  },
  {
    path: '/dashboard/profile',
    name: 'dashboard-profile',
    component: Dashboard,
    meta: { requiresAuth: true },
  },
  {
    path: '/dashboard/settings',
    name: 'dashboard-settings',
    component: Dashboard,
    meta: { requiresAuth: true },
  },
  {
    path: '/dashboard/users',
    name: 'dashboard-users',
    component: Dashboard,
    meta: { requiresAuth: true, requiresAdmin: true },
  },
  {
    path: '/dashboard/categories',
    name: 'dashboard-categories',
    component: Dashboard,
    meta: { requiresAuth: true, requiresAdmin: true },
  },
  {
    path: '/dashboard/analytics',
    name: 'dashboard-analytics',
    component: Dashboard,
    meta: { requiresAuth: true, requiresAdmin: true },
  },
  {
    path: '/dashboard/my-bookings',
    name: 'dashboard-my-bookings',
    component: Dashboard,
    meta: { requiresAuth: true, requiresOrganizer: true },
  },
  {
    path: '/dashboard/events',
    name: 'dashboard-events',
    component: Dashboard,
    meta: { requiresAuth: true, requiresAdmin: true },
  },
]

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes,
  scrollBehavior() {
    return { top: 0 }
  },
})

// Navigation guard for admin routes
router.beforeEach((to, from, next) => {
  const auth = useAuthStore()
  
  if (to.meta.requiresAuth && !auth.isAuthenticated) {
    next('/login')
  } else if (to.meta.requiresAdmin && !auth.isAdmin) {
    next('/dashboard')
  } else if (to.meta.requiresOrganizer && !auth.isOrganizer) {
    next('/dashboard')
  } else {
    next()
  }
})

export default router
