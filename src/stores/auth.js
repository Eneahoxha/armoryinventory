import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import axios from 'axios'

export const useAuthStore = defineStore('auth', () => {
  // State
  const user = ref(null)
  const token = ref(localStorage.getItem('auth_token') || '')
  const loading = ref(false)
  const error = ref('')

  // Computed
  const isAuthenticated = computed(() => !!token.value && !!user.value)
  const userRole = computed(() => user.value?.ruolo || null)

  // Configure axios with default header
  const updateAxiosHeader = () => {
    if (token.value) {
      axios.defaults.headers.common['Authorization'] = `Bearer ${token.value}`
    } else {
      delete axios.defaults.headers.common['Authorization']
    }
  }

  // Methods
  const login = async (email, password) => {
    loading.value = true
    error.value = ''
    try {
      const response = await axios.post('/api/auth/login', { email, password })
      token.value = response.data.token
      user.value = response.data.user
      localStorage.setItem('auth_token', token.value)
      updateAxiosHeader()
      return true
    } catch (err) {
      error.value = err.response?.data?.error || 'Login fallito'
      return false
    } finally {
      loading.value = false
    }
  }

  const logout = () => {
    token.value = ''
    user.value = null
    localStorage.removeItem('auth_token')
    delete axios.defaults.headers.common['Authorization']
  }

  const validateToken = async () => {
    if (!token.value) return false
    try {
      const response = await axios.post('/api/auth/validate')
      user.value = response.data.user
      return true
    } catch (err) {
      logout()
      return false
    }
  }

  const hasRole = (requiredRoles) => {
    if (!isAuthenticated.value) return false
    if (!Array.isArray(requiredRoles)) requiredRoles = [requiredRoles]
    return requiredRoles.includes(userRole.value)
  }

  // Initialize on store creation
  updateAxiosHeader()

  return {
    user,
    token,
    loading,
    error,
    isAuthenticated,
    userRole,
    login,
    logout,
    validateToken,
    hasRole
  }
})
