<template>
  <div class="chapters-list">
    <n-card>
      <template #header>
        <div style="display: flex; justify-content: space-between; align-items: center;">
          <span>
            <n-button text @click="goBack" style="margin-right: 8px;">
              ← 返回
            </n-button>
            章节管理 - {{ bookInfo.name }}
          </span>
          <n-button type="primary" @click="handleAdd">添加章节</n-button>
        </div>
      </template>
      <n-data-table
        :columns="columns"
        :data="chapters"
        :loading="loading"
        :pagination="pagination"
        @update:page="handlePageChange"
        @update:page-size="handlePageSizeChange"
        :remote="true"
      />
    </n-card>
    
    <n-modal v-model:show="showModal" :title="isEdit ? '编辑章节' : '添加章节'" preset="card" style="width: 600px;">
      <n-form :model="formData" :rules="rules" ref="formRef" label-placement="left" label-width="100px">
        <n-form-item label="章节名称" path="name">
          <n-input v-model:value="formData.name" placeholder="请输入章节名称" />
        </n-form-item>
        <n-form-item label="排序" path="snum">
          <n-input v-model:value="formData.snum" placeholder="请输入排序号" />
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
import { useRoute, useRouter } from 'vue-router'
import message, { dialog } from '@/utils/message'

const route = useRoute()
const router = useRouter()
const formRef = ref(null)
const showModal = ref(false)
const isEdit = ref(false)
const currentId = ref(null)

const columns = [
  { title: 'ID', key: 'id', width: 80 },
  { 
    title: '章节名称', 
    key: 'name', 
    render: (row) => h(
      'a',
      {
        style: { color: '#18a058', cursor: 'pointer', textDecoration: 'underline' },
        onClick: () => goToContents(row)
      },
      row.name
    )
  },
  { title: '排序', key: 'snum', width: 100 },
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
//   { title: '创建时间', key: 'created_at', width: 180 },
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

const chapters = ref([])
const loading = ref(false)
const bookInfo = ref({ name: '' })
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
  parent_id: null,
  books_id: null,
  snum: '1',
  status: 1,
  users_id: null
})

const rules = {
  name: { required: true, message: '请输入章节名称', trigger: 'blur' }
}

const fetchBookInfo = async () => {
  try {
    const bookId = route.query.book_id
    const response = await fetch(`/api/v1/books/${bookId}`)
    const result = await response.json()
    if (result.code === 10000) {
      bookInfo.value = result.data
    }
  } catch (error) {
    console.error('获取书籍信息失败:', error)
  }
}

const fetchChapters = async () => {
  loading.value = true
  try {
    const bookId = route.query.book_id
    const response = await fetch(`/api/v1/chapters?page=${pagination.value.page}&pageSize=${pagination.value.pageSize}&books_id=${bookId}`)
    const result = await response.json()
    if (result.code === 10000) {
      chapters.value = result.data.list
      pagination.value = {
        ...pagination.value,
        itemCount: result.data.total
      }
    }
  } catch (error) {
    message.error('获取章节列表失败')
    console.error('获取章节列表失败:', error)
  } finally {
    loading.value = false
  }
}

const handlePageChange = (page) => {
  pagination.value.page = page
  fetchChapters()
}

const handlePageSizeChange = (pageSize) => {
  pagination.value.pageSize = pageSize
  pagination.value.page = 1
  fetchChapters()
}

const handleAdd = () => {
  isEdit.value = false
  currentId.value = null
  formData.value = { 
    name: '',
    parent_id: null,
    books_id: route.query.book_id || null,
    snum: '1',
    status: 1,
    users_id: null
  }
  showModal.value = true
}

const handleEdit = (row) => {
  isEdit.value = true
  currentId.value = row.id
  formData.value = { 
    name: row.name,
    parent_id: row.parent_id || null,
    books_id: row.books_id,
    snum: row.snum || '',
    status: row.status,
    users_id: row.users_id || null
  }
  showModal.value = true
}

const handleDelete = (id) => {
  dialog.warning({
    title: '确认删除',
    content: '确定要删除这个章节吗？此操作不可恢复。',
    positiveText: '确定',
    negativeText: '取消',
    onPositiveClick: async () => {
      try {
        const response = await fetch(`/api/v1/chapters/${id}`, { method: 'DELETE' })
        const result = await response.json()
        if (result.code === 10000) {
          message.success('删除成功')
          fetchChapters()
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
    
    let url = '/api/v1/chapters'
    let method = 'POST'
    
    if (isEdit.value) {
      url = `/api/v1/chapters/${currentId.value}`
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
      fetchChapters()
    } else {
      message.error(result.msg || '操作失败')
    }
  } catch (error) {
    message.error('操作失败')
  }
}

const goBack = () => {
  router.back()
}

const goToContents = (row) => {
  router.push({
    path: '/admin/contents',
    query: { chapter_id: row.id }
  })
}

onMounted(() => {
  fetchBookInfo()
  fetchChapters()
})
</script>

<style scoped>
.chapters-list {
  padding: 20px;
}
</style>
