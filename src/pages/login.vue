<template>
  <div class="login-container">
    <n-card class="login-card">
      <template #header>
        <h2 class="login-title">用户登录</h2>
      </template>
      
      <n-form
        ref="formRef"
        :model="formValue"
        :rules="rules"
        label-placement="left"
        label-width="auto"
      >
        <n-form-item label="用户名" path="username">
          <n-input
            v-model:value="formValue.username"
            placeholder="请输入用户名"
            size="large"
          />
        </n-form-item>
        
        <n-form-item label="密码" path="password">
          <n-input
            v-model:value="formValue.password"
            type="password"
            placeholder="请输入密码"
            size="large"
            show-password-on="click"
            @keyup.enter="handleLogin"
          />
        </n-form-item>
        
        <n-form-item>
          <n-button
            type="primary"
            size="large"
            :loading="loading"
            block
            @click="handleLogin"
          >
            登录
          </n-button>
        </n-form-item>
      </n-form>
    </n-card>
  </div>
</template>

<script setup>
import { reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import message from '@/utils/message'

const router = useRouter()
const authStore = useAuthStore()
const formRef = ref(null)
const loading = ref(false)

const formValue = reactive({
  username: '',
  password: ''
})

const rules = {
  username: {
    required: true,
    message: '请输入用户名',
    trigger: 'blur'
  },
  password: {
    required: true,
    message: '请输入密码',
    trigger: 'blur'
  }
}

const handleLogin = async () => {
  try {
    await formRef.value?.validate()
  } catch (error) {
    return
  }

  loading.value = true

  try {
    const response = await fetch('/api/v1/login', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({
        username: formValue.username,
        password: formValue.password
      })
    })

    const data = await response.json()

    if (data.code === 10000) {
      message.success('登录成功')
      
      const isAdminRole = data.data.user.category == 0
      
      // 保存登录状态
      authStore.login(data.datas || { username: formValue.username }, data.token || 'demo-token', isAdminRole)
      
      // 根据响应消息判断跳转路由
      if (isAdminRole) {
        router.push('/admin')
      } else {
        router.push('/home')
      }
    } else {
      message.error(data.msg || '登录失败，请重试')
    }
  } catch (error) {
    message.error('网络错误，请稍后重试')
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.login-container {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.login-card {
  width: 100%;
  max-width: 450px;
  padding: 20px;
}

.login-title {
  margin: 0;
  text-align: center;
  color: #333;
}
</style>
