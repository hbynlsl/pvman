<template>
  <div class="home-container">
    <n-card title="文献古籍">
      <div class="books-grid">
        <div
          v-for="book in books"
          :key="book.id"
          class="book-item"
          @click="goToChapters(book)"
        >
          <div class="book-card">
            <div class="book-name">{{ book.name }}</div>
          </div>
        </div>
      </div>
      <div class="pagination-wrapper">
        <n-pagination
          v-model:page="pagination.page"
          v-model:page-size="pagination.pageSize"
          :item-count="pagination.itemCount"
          :show-size-picker="true"
          :page-sizes="[20, 40, 60, 80]"
          @update:page="handlePageChange"
          @update:page-size="handlePageSizeChange"
        />
      </div>
    </n-card>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()

const books = ref([])
const loading = ref(false)
const pagination = ref({
  page: 1,
  pageSize: 20,
  itemCount: 0
})

const fetchBooks = async () => {
  loading.value = true
  try {
    const response = await fetch(`/api/v1/books?page=${pagination.value.page}&pageSize=${pagination.value.pageSize}`)
    const result = await response.json()
    if (result.code === 10000) {
      books.value = result.data.list
      pagination.value.itemCount = result.data.total
    }
  } catch (error) {
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

const goToChapters = (book) => {
  router.push({
    path: '/home/chapters',
    query: { book_id: book.id, book_name: book.name }
  })
}

onMounted(() => {
  fetchBooks()
})
</script>

<style scoped>
.home-container {
  padding: 20px;
}

.books-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 20px;
  margin-bottom: 30px;
}

.book-item {
  cursor: pointer;
}

.book-card {
  background-color: #f5f5f5;
  border-radius: 8px;
  padding: 30px 20px;
  text-align: center;
  transition: all 0.3s ease;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.book-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.2);
  background-color: #e8f5e9;
}

.book-name {
  font-size: 18px;
  font-weight: 500;
  color: #333;
}

.pagination-wrapper {
  display: flex;
  justify-content: center;
  margin-top: 20px;
}
</style>
