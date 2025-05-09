import axios from 'axios'
import router from '../router'

const api = axios.create({
  baseURL: import.meta.env.VITE_API_URL || 'http://localhost:8000',
})

// Request interceptor
api.interceptors.request.use(
  (config) => {
    // List of public routes that don't require authentication
    const publicRoutes = [
      { path: '/api/events', method: 'GET' },
      { path: '/api/categories', method: 'GET' },
      { path: '/api/register', method: 'POST' },
      { path: '/api/login', method: 'POST' }
    ];
    
    // Check if the current request matches any public route
    const isPublicRoute = publicRoutes.some(route => 
      config.url.includes(route.path) && config.method.toLowerCase() === route.method.toLowerCase()
    );

    // Only add token for non-public routes
    if (!isPublicRoute) {
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
      // Clear auth data
      localStorage.removeItem('token')
      localStorage.removeItem('user')
      
      // Remove token from axios defaults
      delete api.defaults.headers.common['Authorization']
      
      // Redirect to login
      router.push('/login')
    }
    return Promise.reject(error)
  },
)

export default api
