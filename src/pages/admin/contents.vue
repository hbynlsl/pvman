<template>
  <div class="contents-list">
    <n-card>
      <template #header>
        <div style="display: flex; justify-content: space-between; align-items: center;">
          <span>
            <n-button text @click="goBack" style="margin-right: 8px;">
              ← 返回
            </n-button>
            内容管理 - {{ chapterInfo.name }}
          </span>
          <n-button type="primary" @click="handleAdd">添加内容</n-button>
        </div>
      </template>
      <n-data-table
        :columns="columns"
        :data="contents"
        :loading="loading"
        :pagination="pagination"
        @update:page="handlePageChange"
        @update:page-size="handlePageSizeChange"
        :remote="true"
      />
    </n-card>
    
    <n-modal v-model:show="showModal" :title="isEdit ? '编辑内容' : '添加内容'" preset="card" style="width: 800px;">
      <n-form :model="formData" :rules="rules" ref="formRef" label-placement="top">
        <n-form-item label="内容" path="content">
          <n-input
            v-model:value="formData.content"
            type="textarea"
            placeholder="请输入内容"
            :rows="10"
            style="width: 100%;"
          />
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
    title: '内容预览', 
    key: 'content', 
    render: (row) => h(
      'n-tooltip',
      {
        trigger: 'hover',
        placement: 'top'
      },
      {
        default: () => row.content || '暂无内容',
        trigger: () => h(
          'div',
          {
            style: {
              display: 'inline-block',
              width: '100%',
              overflow: 'hidden',
              textOverflow: 'ellipsis',
              whiteSpace: 'nowrap',
              lineHeight: '1.5'
            }
          },
          row.content || ''
        )
      }
    )
  },
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
  { title: '创建时间', key: 'created_at', width: 180 },
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

const contents = ref([])
const loading = ref(false)
const chapterInfo = ref({ name: '' })
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
  content: '',
  chapters_id: null,
  status: 1,
  users_id: null
})

const rules = {
  content: { required: true, message: '请输入内容', trigger: 'blur' }
}

const fetchChapterInfo = async () => {
  try {
    const chapterId = route.query.chapter_id
    const response = await fetch(`/api/v1/chapters/${chapterId}`)
    const result = await response.json()
    if (result.code === 10000) {
      chapterInfo.value = result.data
    }
  } catch (error) {
    console.error('获取章节信息失败:', error)
  }
}

const fetchContents = async () => {
  loading.value = true
  try {
    const chapterId = route.query.chapter_id
    const response = await fetch(`/api/v1/contents?page=${pagination.value.page}&pageSize=${pagination.value.pageSize}&chapters_id=${chapterId}`)
    const result = await response.json()
    if (result.code === 10000) {
      contents.value = result.data.list
      pagination.value = {
        ...pagination.value,
        itemCount: result.data.total
      }
    }
  } catch (error) {
    message.error('获取内容列表失败')
    console.error('获取内容列表失败:', error)
  } finally {
    loading.value = false
  }
}

const handlePageChange = (page) => {
  pagination.value.page = page
  fetchContents()
}

const handlePageSizeChange = (pageSize) => {
  pagination.value.pageSize = pageSize
  pagination.value.page = 1
  fetchContents()
}

const handleAdd = () => {
  isEdit.value = false
  currentId.value = null
  formData.value = { 
    content: '',
    chapters_id: route.query.chapter_id || null,
    status: 1,
    users_id: null
  }
  showModal.value = true
}

const handleEdit = (row) => {
  isEdit.value = true
  currentId.value = row.id
  formData.value = { 
    content: row.content,
    chapters_id: row.chapters_id,
    status: row.status,
    users_id: row.users_id || null
  }
  showModal.value = true
}

const handleDelete = (id) => {
  dialog.warning({
    title: '确认删除',
    content: '确定要删除该内容吗？此操作不可恢复。',
    positiveText: '确定',
    negativeText: '取消',
    onPositiveClick: async () => {
      try {
        const response = await fetch(`/api/v1/contents/${id}`, { method: 'DELETE' })
        const result = await response.json()
        if (result.code === 10000) {
          message.success('删除成功')
          fetchContents()
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
    
    let url = '/api/v1/contents'
    let method = 'POST'
    
    if (isEdit.value) {
      url = `/api/v1/contents/${currentId.value}`
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
      fetchContents()
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

onMounted(() => {
  fetchChapterInfo()
  fetchContents()
})
</script>

<style scoped>
.contents-list {
  padding: 20px;
}
</style>
