import { ref, computed } from 'vue'
import { defineStore } from 'pinia'

export const useAuthStore = defineStore('auth', () => {
  const token = ref(localStorage.getItem('token') || '')
  const user = ref(JSON.parse(localStorage.getItem('user') || 'null'))
  const isAdmin = ref(localStorage.getItem('isAdmin') === 'true')

  const isLoggedIn = computed(() => !!token.value)

  function login(userData, userToken, isAdminRole = false) {
    token.value = userToken
    user.value = userData
    isAdmin.value = isAdminRole
    
    localStorage.setItem('token', userToken)
    localStorage.setItem('user', JSON.stringify(userData))
    localStorage.setItem('isAdmin', isAdminRole.toString())
  }

  function logout() {
    token.value = ''
    user.value = null
    isAdmin.value = false
    
    localStorage.removeItem('token')
    localStorage.removeItem('user')
    localStorage.removeItem('isAdmin')
  }

  return {
    token,
    user,
    isAdmin,
    isLoggedIn,
    login,
    logout
  }
})
