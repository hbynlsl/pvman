import { createRouter, createWebHistory } from 'vue-router'
import { routes } from 'vue-router/auto-routes' // 自动生成的路由
// import routes from 'virtual:auto-router'
import { useAuthStore } from '@/stores/auth'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes,
})

router.beforeEach((to, from, next) => {
  const authStore = useAuthStore()
  const publicRoutes = ['/login']
  
  if (!authStore.isLoggedIn) {
    if (!publicRoutes.includes(to.path)) {
      next('/login')
    } else {
      next()
    }
  } else {
    if (to.path === '/login') {
      if (authStore.isAdmin) {
        next('/admin')
      } else {
        next('/home')
      }
    } else {
      next()
    }
  }
})

export default router
