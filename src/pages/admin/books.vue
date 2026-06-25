<template>
  <div class="books-list">
    <n-card>
      <template #header>
        <div style="display: flex; justify-content: space-between; align-items: center;">
          <span>书籍管理</span>
          <n-button type="primary" @click="handleAdd">添加书籍</n-button>
        </div>
      </template>
      <n-data-table
        :columns="columns"
        :data="books"
        :loading="loading"
        :pagination="pagination"
        @update:page="handlePageChange"
        @update:page-size="handlePageSizeChange"
        :remote="true"
      />
    </n-card>
    
    <n-modal v-model:show="showModal" :title="isEdit ? '编辑书籍' : '添加书籍'" preset="card" style="width: 600px;">
      <n-form :model="formData" :rules="rules" ref="formRef" label-placement="left" label-width="100px">
        <n-form-item label="书名" path="name">
          <n-input v-model:value="formData.name" placeholder="请输入书名" />
        </n-form-item>
        <n-form-item label="描述" path="desc">
          <n-input v-model:value="formData.desc" type="textarea" :rows="4" placeholder="请输入描述" />
        </n-form-item>
        <n-form-item label="版本" path="version">
          <n-input v-model:value="formData.version" placeholder="请输入版本" />
        </n-form-item>
        <n-form-item label="出版年份" path="pubyear">
          <n-input v-model:value="formData.pubyear" placeholder="请输入出版年份" />
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
import { useRouter } from 'vue-router'
import message, { dialog } from '@/utils/message'

const router = useRouter()

const formRef = ref(null)
const showModal = ref(false)
const isEdit = ref(false)
const currentId = ref(null)

const columns = [
  { title: 'ID', key: 'id', width: 80 },
  { 
    title: '书名', 
    key: 'name',
    render: (row) => h(
      'a',
      {
        style: { color: '#18a058', cursor: 'pointer', textDecoration: 'underline' },
        onClick: () => goToChapters(row)
      },
      row.name
    )
  },
  { title: '描述', key: 'desc', ellipsis: true },
  { title: '版本', key: 'version', width: 120 },
  { title: '出版年份', key: 'pubyear', width: 120 },
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

const books = ref([])
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
  desc: '',
  version: '',
  pubyear: '',
  status: 1
})

const rules = {
  name: { required: true, message: '请输入书名', trigger: 'blur' }
}

const fetchBooks = async () => {
  loading.value = true
  try {
    const response = await fetch(`/api/v1/books?page=${pagination.value.page}&pageSize=${pagination.value.pageSize}`)
    const result = await response.json()
    if (result.code === 10000) {
      books.value = result.data.list
      // 强制更新 pagination 对象以触发响应式更新
      pagination.value = {
        ...pagination.value,
        itemCount: result.data.total
      }
    }
  } catch (error) {
    message.error('获取书籍列表失败')
    console.error('获取书籍列表失败:', error)
  } finally {
    loading.value = false
  }
}

const handlePageChange = (page) => {
  pagination.value.page = page
  fetchBooks()
}

const handlePageSizeChange = (pageSize) => {
  pagination.value.pageSize = pageSize
  pagination.value.page = 1
  fetchBooks()
}

const handleAdd = () => {
  isEdit.value = false
  currentId.value = null
  formData.value = { 
    name: '', 
    desc: '', 
    version: '', 
    pubyear: '', 
    status: 1 
  }
  showModal.value = true
}

const handleEdit = (row) => {
  isEdit.value = true
  currentId.value = row.id
  formData.value = { 
    name: row.name, 
    desc: row.desc, 
    version: row.version, 
    pubyear: row.pubyear || '', 
    status: row.status 
  }
  showModal.value = true
}

const handleDelete = (id) => {
  dialog.warning({
    title: '确认删除',
    content: '确定要删除这本书籍吗？此操作不可恢复。',
    positiveText: '确定',
    negativeText: '取消',
    onPositiveClick: async () => {
      try {
        const response = await fetch(`/api/v1/books/${id}`, { method: 'DELETE' })
        const result = await response.json()
        if (result.code === 10000) {
          message.success('删除成功')
          fetchBooks()
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
    
    let url = '/api/v1/books'
    let method = 'POST'
    
    if (isEdit.value) {
      url = `/api/v1/books/${currentId.value}`
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
      fetchBooks()
    } else {
      message.error(result.msg || '操作失败')
    }
  } catch (error) {
    message.error('操作失败')
  }
}

const goToChapters = (row) => {
  router.push({
    path: '/admin/chapters',
    query: { book_id: row.id }
  })
}

onMounted(() => {
  fetchBooks()
})
</script>

<style scoped>
.books-list {
  padding: 20px;
}
</style>
