<template>
  <div class="chapters-container">
    <n-card>
      <template #header>
        <div style="display: flex; justify-content: space-between; align-items: center;">
          <span>
            <n-button text @click="goBack" style="margin-right: 8px;">
              ← 返回
            </n-button>
            {{ bookName }} - 章节目录
          </span>
        </div>
      </template>
      <div class="chapters-list">
        <div
          v-for="chapter in chapters"
          :key="chapter.id"
          class="chapter-item"
          @click="goToContents(chapter)"
        >
          <div class="chapter-name">{{ chapter.name }}</div>
        </div>
      </div>
      <div class="pagination-wrapper">
        <n-pagination
          v-model:page="pagination.page"
          v-model:page-size="pagination.pageSize"
          :item-count="pagination.itemCount"
          :show-size-picker="true"
          :page-sizes="[10, 20, 50, 100]"
          @update:page="handlePageChange"
          @update:page-size="handlePageSizeChange"
        />
      </div>
    </n-card>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'

const route = useRoute()
const router = useRouter()

const bookName = ref('')

const chapters = ref([])
const loading = ref(false)
const pagination = ref({
  page: 1,
  pageSize: 10,
  itemCount: 0
})

const fetchChapters = async () => {
  loading.value = true
  try {
    const bookId = route.query.book_id
    const response = await fetch(`/api/v1/chapters?page=${pagination.value.page}&pageSize=${pagination.value.pageSize}&books_id=${bookId}`)
    const result = await response.json()
    if (result.code === 10000) {
      chapters.value = result.data.list
      pagination.value.itemCount = result.data.total
    }
  } catch (error) {
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

const goBack = () => {
  router.back()
}

const goToContents = (chapter) => {
  router.push({
    path: '/home/contents',
    query: { chapter_id: chapter.id, chapter_name: chapter.name, book_name: bookName.value }
  })
}

onMounted(() => {
  bookName.value = route.query.book_name || '书籍'
  fetchChapters()
})
</script>

<style scoped>
.chapters-container {
  padding: 20px;
}

.chapters-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
  margin-bottom: 30px;
}

.chapter-item {
  background-color: #f5f5f5;
  border-radius: 8px;
  padding: 20px;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.chapter-item:hover {
  transform: translateX(4px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  background-color: #e8f5e9;
}

.chapter-name {
  font-size: 16px;
  font-weight: 500;
  color: #333;
}

.pagination-wrapper {
  display: flex;
  justify-content: center;
  margin-top: 20px;
}
</style>
