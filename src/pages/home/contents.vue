<template>
  <div class="contents-container">
    <n-card>
      <template #header>
        <div style="display: flex; justify-content: space-between; align-items: center;">
          <span>
            <n-button text @click="goBack" style="margin-right: 8px;">
              ← 返回
            </n-button>
            {{ bookName }} - {{ chapterName }}
          </span>
        </div>
      </template>
      <div v-if="contents.length > 0">
        <n-collapse v-model:expanded-names="expandedNames" accordion>
          <n-collapse-item
            v-for="(content, index) in contents"
            :key="content.id"
            :name="content.id"
            :title="getContentTitle(content, index)"
          >
            <div class="content-detail">
              <div class="content-section">
                <h4 class="section-title">内容正文</h4>
                <div 
                  class="content-text" 
                  @mouseup="handleTextSelect(content, $event)"
                  ref="contentRef"
                >
                  {{ content.content }}
                </div>
              </div>
              
              <div class="annotation-section" v-if="content.annotations && content.annotations.length > 0">
                <h4 class="section-title">注解信息</h4>
                <div class="annotations-list">
                  <div v-for="annotation in content.annotations" :key="annotation.id" class="annotation-item">
                    <div class="annotation-name">{{ annotation.name }}</div>
                    <div class="annotation-desc">{{ annotation.desc }}</div>
                    <div class="annotation-dictionary" v-if="annotation.dictionary_name">
                      <n-tag size="small" type="info">字典: {{ annotation.dictionary_name }}</n-tag>
                    </div>
                  </div>
                </div>
              </div>
              <div class="no-annotation" v-else>
                <n-empty description="暂无注解" size="small" />
              </div>
            </div>
          </n-collapse-item>
        </n-collapse>
      </div>
      <n-empty v-else description="暂无内容" />
      <div class="pagination-wrapper" v-if="contents.length > 0">
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
    
    <!-- 添加注解的对话框 -->
    <n-modal v-model:show="showAnnotationModal" preset="card" title="添加注解" style="width: 600px;">
      <n-form :model="annotationForm" ref="annotationFormRef">
        <n-form-item label="选中文本（注解名）">
          <n-input v-model:value="annotationForm.name" readonly />
        </n-form-item>
        <n-form-item label="注解内容" required>
          <n-input
            v-model:value="annotationForm.desc"
            type="textarea"
            placeholder="请输入注解内容"
            :rows="4"
          />
        </n-form-item>
        <n-form-item label="标签">
          <n-select
            v-model:value="annotationForm.labelId"
            :options="labelOptions"
            placeholder="请选择标签"
            clearable
          />
        </n-form-item>
        <n-form-item label="开始位置">
          <n-input-number v-model:value="annotationForm.start" readonly />
        </n-form-item>
        <n-form-item label="结束位置">
          <n-input-number v-model:value="annotationForm.end" readonly />
        </n-form-item>
      </n-form>
      <template #footer>
        <n-space justify="end">
          <n-button @click="showAnnotationModal = false">取消</n-button>
          <n-button type="primary" @click="handleAddAnnotation">确定</n-button>
        </n-space>
      </template>
    </n-modal>

    <!-- 修改注解的对话框 -->
    <n-modal v-model:show="showEditAnnotationModal" preset="card" title="修改注解" style="width: 600px;">
      <n-form :model="editAnnotationForm" ref="editAnnotationFormRef">
        <n-form-item label="注解名">
          <n-input v-model:value="editAnnotationForm.name" readonly />
        </n-form-item>
        <n-form-item label="注解内容" required>
          <n-input
            v-model:value="editAnnotationForm.desc"
            type="textarea"
            placeholder="请输入注解内容"
            :rows="4"
          />
        </n-form-item>
        <n-form-item label="标签">
          <n-select
            v-model:value="editAnnotationForm.labelId"
            :options="labelOptions"
            placeholder="请选择标签"
            clearable
          />
        </n-form-item>
        <n-form-item label="开始位置">
          <n-input-number v-model:value="editAnnotationForm.start" readonly />
        </n-form-item>
        <n-form-item label="结束位置">
          <n-input-number v-model:value="editAnnotationForm.end" readonly />
        </n-form-item>
      </n-form>
      <template #footer>
        <n-space justify="end">
          <n-button @click="showEditAnnotationModal = false">取消</n-button>
          <n-button type="primary" @click="handleEditAnnotation">保存修改</n-button>
        </n-space>
      </template>
    </n-modal>
  </div>
</template>

<script setup>
import { ref, onMounted, nextTick } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import message from '@/utils/message'

const route = useRoute()
const router = useRouter()

const bookName = ref('')
const chapterName = ref('')
const expandedNames = ref([])
const contentRef = ref(null)

const contents = ref([])
const loading = ref(false)
const pagination = ref({
  page: 1,
  pageSize: 10,
  itemCount: 0
})

// 添加注解相关
const showAnnotationModal = ref(false)
const annotationFormRef = ref(null)
const annotationForm = ref({
  contentId: null,
  name: '',
  desc: '',
  labelId: null,
  start: 0,
  end: 0
})

// 修改注解相关
const showEditAnnotationModal = ref(false)
const editAnnotationFormRef = ref(null)
const editAnnotationForm = ref({
  annotationId: null,
  contentId: null,
  name: '',
  desc: '',
  labelId: null,
  start: 0,
  end: 0
})
const labelOptions = ref([])

const fetchContents = async () => {
  loading.value = true
  try {
    const chapterId = route.query.chapter_id
    const response = await fetch(`/api/v1/contents?page=${pagination.value.page}&pageSize=${pagination.value.pageSize}&chapters_id=${chapterId}`)
    const result = await response.json()
    if (result.code === 10000) {
      contents.value = result.data.list
      pagination.value.itemCount = result.data.total
    }
  } catch (error) {
    console.error('获取内容列表失败:', error)
  } finally {
    loading.value = false
  }
}

const fetchLabels = async () => {
  try {
    const response = await fetch('/api/v1/labels')
    const result = await response.json()
    if (result.code === 10000) {
      labelOptions.value = result.data.list.map(label => ({
        label: label.name,
        value: label.id
      }))
    }
  } catch (error) {
    console.error('获取标签失败:', error)
  }
}

const formatContent = (content) => {
  if (!content) return ''
  return content.replace(/\n/g, '<br>')
}

const getContentTitle = (content, index) => {
  const page = pagination.value.page || 1
  const pageSize = pagination.value.pageSize || 10
  return `第 ${(page - 1) * pageSize + index + 1} 段`
}

const handleTextSelect = (content, event) => {
  const selection = window.getSelection()
  const selectedText = selection.toString().trim()
  
  if (!selectedText || selection.rangeCount === 0) return
  
  // 确保选区在当前内容区域内
  const targetEl = event.currentTarget
  const range = selection.getRangeAt(0)
  if (!targetEl.contains(range.startContainer) || !targetEl.contains(range.endContainer)) return
  
  // 通过遍历文本节点精确计算选区在原文中的真实偏移量
  let start = -1
  let end = -1
  let charCount = 0
  const walker = document.createTreeWalker(targetEl, NodeFilter.SHOW_TEXT, null)
  let node
  while ((node = walker.nextNode())) {
    if (node === range.startContainer) {
      start = charCount + range.startOffset
    }
    if (node === range.endContainer) {
      end = charCount + range.endOffset
      break
    }
    charCount += node.textContent.length
  }
  
  if (start < 0 || end < 0 || start >= end) return
  
  // 优先按位置精确匹配已有注解，其次按名称匹配
  const existingAnnotation = content.annotations && content.annotations.find(
    ann => (ann.start === start && ann.end === end) || ann.name === selectedText
  )
  
  if (existingAnnotation) {
    // 存在注解，展示修改对话框
    editAnnotationForm.value = {
      annotationId: existingAnnotation.id,
      contentId: content.id,
      name: existingAnnotation.name,
      desc: existingAnnotation.desc || '',
      labelId: existingAnnotation.labels_id || null,
      start: existingAnnotation.start ?? start,
      end: existingAnnotation.end ?? end
    }
    showEditAnnotationModal.value = true
  } else {
    // 不存在注解，展示新增对话框
    annotationForm.value = {
      contentId: content.id,
      name: selectedText,
      desc: '',
      labelId: null,
      start,
      end
    }
    showAnnotationModal.value = true
  }
}

const handleAddAnnotation = async () => {
  if (!annotationForm.value.name || !annotationForm.value.desc) {
    message.error('请填写注解名称和内容')
    return
  }
  
  try {
    const response = await fetch('/api/v1/contents/annotation', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        content_id: annotationForm.value.contentId,
        name: annotationForm.value.name,
        desc: annotationForm.value.desc,
        label_id: annotationForm.value.labelId,
        start: annotationForm.value.start,
        end: annotationForm.value.end
      })
    })
    
    const result = await response.json()
    if (result.code === 10000) {
      message.success('添加成功')
      showAnnotationModal.value = false
      fetchContents() // 重新加载内容列表
    } else {
      message.error(result.msg || '添加失败')
    }
  } catch (error) {
    console.error('添加注解失败:', error)
    message.error('添加失败')
  }
}

const handleEditAnnotation = async () => {
  if (!editAnnotationForm.value.name || !editAnnotationForm.value.desc) {
    message.error('请填写注解名称和内容')
    return
  }
  
  try {
    const response = await fetch('/api/v1/contents/annotation', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        content_id: editAnnotationForm.value.contentId,
        name: editAnnotationForm.value.name,
        desc: editAnnotationForm.value.desc,
        label_id: editAnnotationForm.value.labelId,
        start: editAnnotationForm.value.start,
        end: editAnnotationForm.value.end
      })
    })
    
    const result = await response.json()
    if (result.code === 10000) {
      message.success('修改成功')
      showEditAnnotationModal.value = false
      fetchContents()
    } else {
      message.error(result.msg || '修改失败')
    }
  } catch (error) {
    console.error('修改注解失败:', error)
    message.error('修改失败')
  }
}

const handlePageChange = (page) => {
  pagination.value.page = page
  expandedNames.value = []
  fetchContents()
}

const handlePageSizeChange = (pageSize) => {
  pagination.value.pageSize = pageSize
  pagination.value.page = 1
  expandedNames.value = []
  fetchContents()
}

const goBack = () => {
  router.back()
}

onMounted(() => {
  bookName.value = route.query.book_name || '书籍'
  chapterName.value = route.query.chapter_name || '章节'
  fetchContents()
  fetchLabels()
})
</script>

<style scoped>
.contents-container {
  padding: 20px;
}

.content-detail {
  padding: 10px 0;
}

.content-section {
  margin-bottom: 24px;
}

.section-title {
  font-size: 16px;
  font-weight: 600;
  color: #333;
  margin-bottom: 12px;
  padding-bottom: 8px;
  border-bottom: 2px solid #e8f5e9;
}

.content-text {
  font-size: 15px;
  color: #555;
  line-height: 1.8;
  white-space: pre-wrap;
  user-select: text;
}

.annotation-section {
  margin-top: 20px;
}

.annotations-list {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.annotation-item {
  background-color: #f9f9f9;
  border-radius: 8px;
  padding: 16px;
}

.annotation-name {
  font-size: 15px;
  font-weight: 600;
  color: #333;
  margin-bottom: 8px;
}

.annotation-desc {
  font-size: 14px;
  color: #666;
  line-height: 1.6;
  margin-bottom: 8px;
}

.no-annotation {
  margin-top: 20px;
}

.pagination-wrapper {
  display: flex;
  justify-content: center;
  margin-top: 30px;
}
</style>
