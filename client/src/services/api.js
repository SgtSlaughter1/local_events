import axios from 'axios'
import router from '../router'

const api = axios.create({
  baseURL: import.meta.env.VITE_API_URL || 'http://localhost:8000',
})

// Request interceptor
api.interceptors.request.use(
  (config) => {
    // List of public routes that don't require authentication
    const publicRoutes = ['/api/events', '/api/categories', '/api/register', '/api/login'];
    
    // Only add token for non-public routes
    if (!publicRoutes.some(route => config.url.includes(route))) {
      const token = localStorage.getItem('token')
      if (token) {
        config.headers.Authorization = `Bearer ${token}`
      }
    }
    return config
  },
  (error) => {
    return Promise.reject(error)
  },
)

// Response interceptor
api.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      // Handle unauthorized access
      localStorage.removeItem('token')
      router.push('/login')
    }
    return Promise.reject(error)
  },
)

export default api
