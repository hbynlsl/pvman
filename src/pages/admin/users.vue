<template>
  <div class="users-list">
    <n-card>
      <template #header>
        <div style="display: flex; justify-content: space-between; align-items: center;">
          <span>用户管理</span>
          <n-button type="primary" @click="handleAdd">添加用户</n-button>
        </div>
      </template>
      <n-data-table
        :columns="columns"
        :data="users"
        :loading="loading"
        :pagination="pagination"
        @update:page="handlePageChange"
        @update:page-size="handlePageSizeChange"
        :remote="true"
      />
    </n-card>
    
    <n-modal v-model:show="showModal" :title="isEdit ? '编辑用户' : '添加用户'" preset="card" style="width: 600px;">
      <n-form :model="formData" :rules="rules" ref="formRef" label-placement="left" label-width="100px">
        <n-form-item label="用户名" path="name">
          <n-input v-model:value="formData.name" placeholder="请输入用户名" />
        </n-form-item>
        <n-form-item label="密码" path="pswd">
          <n-input v-model:value="formData.pswd" type="password" show-password-on="click" placeholder="请输入密码" />
        </n-form-item>
        <n-form-item label="昵称" path="nick">
          <n-input v-model:value="formData.nick" placeholder="请输入昵称" />
        </n-form-item>
        <n-form-item label="手机号" path="phone">
          <n-input v-model:value="formData.phone" placeholder="请输入手机号" />
        </n-form-item>
        <n-form-item label="邮箱" path="email">
          <n-input v-model:value="formData.email" placeholder="请输入邮箱" />
        </n-form-item>
        <n-form-item label="微信" path="wechat">
          <n-input v-model:value="formData.wechat" placeholder="请输入微信" />
        </n-form-item>
        <n-form-item label="角色" path="category">
          <n-select v-model:value="formData.category" :options="categoryOptions" placeholder="请选择角色" />
        </n-form-item>
        <n-form-item label="状态" path="status">
          <n-select v-model:value="formData.status" :options="statusOptions" placeholder="请选择状态" />
        </n-form-item>
      </n-form>
      <template #footer>
        <n-space justify="end">
          <n-button @click="showModal = false">取消</n-button>
          <n-button type="primary" @click="handleSubmit">提交</n-button>
        </n-space>
      </template>
    </n-modal>
  </div>
</template>

<script setup>
import { ref, onMounted, h } from 'vue'
import message, { dialog } from '@/utils/message'

const formRef = ref(null)
const showModal = ref(false)
const isEdit = ref(false)
const currentId = ref(null)

const columns = [
  { title: 'ID', key: 'id', width: 80 },
  { title: '用户名', key: 'name' },
  { title: '昵称', key: 'nick' },
  { title: '手机号', key: 'phone', width: 130 },
  { title: '邮箱', key: 'email' },
  { title: '角色', key: 'category', width: 100 },
  { 
    title: '状态', 
    key: 'status', 
    width: 100,
    render: (row) => h(
      'n-tag', 
      { type: row.status === 1 ? 'success' : 'error' },
      { default: () => row.status === 1 ? '启用' : '禁用' }
    )
  },
  {
    title: '操作',
    key: 'actions',
    width: 200,
    render: (row) => h('div', { style: { display: 'flex', gap: '8px' } }, [
      h('n-button', { size: 'small', onClick: () => handleEdit(row) }, { default: () => '编辑' }),
      h('n-button', { size: 'small', type: 'error', onClick: () => handleDelete(row.id) }, { default: () => '删除' })
    ])
  }
]

const statusOptions = [
  { label: '启用', value: 1 },
  { label: '禁用', value: 0 }
]

const categoryOptions = [
  { label: '普通用户', value: 'user' },
  { label: '管理员', value: 'admin' }
]

const users = ref([])
const loading = ref(false)
const pagination = ref({
  page: 1,
  pageSize: 10,
  itemCount: 0,
  showSizePicker: true,
  pageSizes: [10, 20, 50, 100],
  showQuickJumper: true,
  prefix: ({ itemCount }) => `共 ${itemCount} 条`
})

const formData = ref({
  name: '',
  pswd: '',
  nick: '',
  phone: '',
  email: '',
  wechat: '',
  category: 'user',
  status: 1
})

const rules = {
  name: { required: true, message: '请输入用户名', trigger: 'blur' }
}

const fetchUsers = async () => {
  loading.value = true
  try {
    const response = await fetch(`/api/v1/users?page=${pagination.value.page}&pageSize=${pagination.value.pageSize}`)
    const result = await response.json()
    if (result.code === 10000) {
      users.value = result.data.list
      // 强制更新 pagination 对象以触发响应式更新
      pagination.value = {
        ...pagination.value,
        itemCount: result.data.total
      }
    }
  } catch (error) {
    message.error('获取用户列表失败')
    console.error('获取用户列表失败:', error)
  } finally {
    loading.value = false
  }
}

const handlePageChange = (page) => {
  pagination.value.page = page
  fetchUsers()
}

const handlePageSizeChange = (pageSize) => {
  pagination.value.pageSize = pageSize
  pagination.value.page = 1
  fetchUsers()
}

const handleAdd = () => {
  isEdit.value = false
  currentId.value = null
  formData.value = { 
    name: '',
    pswd: '',
    nick: '',
    phone: '',
    email: '',
    wechat: '',
    category: 'user',
    status: 1 
  }
  showModal.value = true
}

const handleEdit = (row) => {
  isEdit.value = true
  currentId.value = row.id
  formData.value = { 
    name: row.name,
    pswd: row.pswd || '',
    nick: row.nick || '',
    phone: row.phone || '',
    email: row.email || '',
    wechat: row.wechat || '',
    category: row.category || 'user',
    status: row.status 
  }
  showModal.value = true
}

const handleDelete = (id) => {
  dialog.warning({
    title: '确认删除',
    content: '确定要删除这个用户吗？此操作不可恢复。',
    positiveText: '确定',
    negativeText: '取消',
    onPositiveClick: async () => {
      try {
        const response = await fetch(`/api/v1/users/${id}`, { method: 'DELETE' })
        const result = await response.json()
        if (result.code === 10000) {
          message.success('删除成功')
          fetchUsers()
        } else {
          message.error(result.msg || '删除失败')
        }
      } catch (error) {
        message.error('删除失败')
      }
    }
  })
}

const handleSubmit = async () => {
  try {
    await formRef.value?.validate()
    
    let url = '/api/v1/users'
    let method = 'POST'
    
    if (isEdit.value) {
      url = `/api/v1/users/${currentId.value}`
      method = 'PUT'
    }
    
    const response = await fetch(url, {
      method,
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(formData.value)
    })
    
    const result = await response.json()
    if (result.code === 10000) {
      message.success(isEdit.value ? '更新成功' : '添加成功')
      showModal.value = false
      fetchUsers()
    } else {
      message.error(result.msg || '操作失败')
    }
  } catch (error) {
    message.error('操作失败')
  }
}

onMounted(() => {
  fetchUsers()
})
</script>

<style scoped>
.users-list {
  padding: 20px;
}
</style>
