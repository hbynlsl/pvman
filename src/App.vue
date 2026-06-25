<script setup>
import { RouterLink, RouterView, useRouter } from 'vue-router'
import { NButton } from 'naive-ui'
import { useAuthStore } from '@/stores/auth'
import message from '@/utils/message'

const router = useRouter()
const authStore = useAuthStore()

const handleLogout = () => {
  authStore.logout()
  message.success('已退出登录')
  router.push('/login')
}
</script>

<template>
  <header v-if="authStore.isLoggedIn">
    <div class="wrapper">
      <nav>
        <RouterLink to="/home" v-if="!authStore.isAdmin">Home</RouterLink>
        <RouterLink to="/admin" v-if="authStore.isAdmin">Admin</RouterLink>
        <NButton text @click="handleLogout">退出登录</NButton>
      </nav>
    </div>
  </header>

  <RouterView />
</template>

<style scoped>
header {
  padding: 20px;
  border-bottom: 1px solid #eee;
}

.wrapper {
  max-width: 1200px;
  margin: 0 auto;
}

nav {
  display: flex;
  gap: 20px;
  align-items: center;
}

nav a {
  text-decoration: none;
  color: #333;
  font-weight: 500;
}

nav a:hover {
  color: #667eea;
}
</style>
