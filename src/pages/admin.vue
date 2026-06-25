<template>
  <n-layout has-sider position="absolute" style="height: 100vh;">
    <n-layout-sider bordered content-style="padding: 24px;">
      <div class="logo">
        <h2>管理后台</h2>
      </div>
      <n-menu
        v-model:value="activeMenu"
        :options="menuOptions"
        @update:value="handleMenuChange"
      />
      <div style="margin-top: auto; padding-top: 24px;">
        <n-button type="error" block @click="handleLogout">退出登录</n-button>
      </div>
    </n-layout-sider>
    <n-layout-content content-style="padding: 0; min-height: 100vh;">
      <router-view />
    </n-layout-content>
  </n-layout>
</template>

<script setup>
import { ref, h, watch } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const route = useRoute()
const authStore = useAuthStore()

const activeMenu = ref('dashboard')

const menuOptions = [
  {
    label: '首页',
    key: 'dashboard',
    icon: () => h('span', '🏠')
  },
  {
    label: '书籍管理',
    key: 'books',
    icon: () => h('span', '📚')
  },
  {
    label: '用户管理',
    key: 'users',
    icon: () => h('span', '👥')
  },
  {
    label: '标签管理',
    key: 'labels',
    icon: () => h('span', '🏷️')
  }
]

const handleMenuChange = (key) => {
  activeMenu.value = key
  if (key === 'dashboard') {
    router.push('/admin')
  } else {
    router.push(`/admin/${key}`)
  }
}

const handleLogout = () => {
  authStore.logout()
  router.push('/login')
}

const updateActiveMenu = () => {
  const path = route.path
  if (path.includes('/admin/books') || path.includes('/admin/chapters') || path.includes('/admin/contents')) {
    activeMenu.value = 'books'
  } else if (path.includes('/admin/users')) {
    activeMenu.value = 'users'
  } else if (path.includes('/admin/labels')) {
    activeMenu.value = 'labels'
  } else {
    activeMenu.value = 'dashboard'
  }
}

watch(() => route.path, () => {
  updateActiveMenu()
})

updateActiveMenu()
</script>

<style scoped>
.logo {
  padding: 16px 0;
  margin-bottom: 24px;
  border-bottom: 1px solid #eee;
}

.logo h2 {
  margin: 0;
  font-size: 20px;
}
</style>
