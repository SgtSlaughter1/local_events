import { createRouter, createWebHistory } from 'vue-router'
import LandingPage from '../views/LandingPage.vue'
import Login from '../views/Login.vue'
import Signup from '../views/Signup.vue'
import Events from '../views/Events.vue'
import EventDetails from '../views/EventDetails.vue'
import Profile from '../views/Profile.vue'
import Dashboard from '../views/Dashboard.vue'
import CreateEvent from '../views/CreateEvent.vue'
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
    path: '/my-events',
    name: 'MyEvents',
    component: () => import('@/views/MyEvents.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/create-event',
    name: 'createEvent',
    component: CreateEvent,
    meta: { requiresAuth: true },
  },
]

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes,
  scrollBehavior() {
    return { top: 0 }
  },
})

export default router
