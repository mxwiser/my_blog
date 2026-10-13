<script setup lang="ts">
import { ref, watch } from 'vue'
import type { Article } from '../types/blog'
import { formatDate } from '../utils/date'
import ArticleCover from './ArticleCover.vue'
import AppIcon from './AppIcon.vue'

const props = defineProps<{ article: Article | null }>()
const emit = defineEmits<{ close: [] }>()
const dialog = ref<HTMLDialogElement | null>(null)

// 原生 dialog 管理焦点与 Escape；Vue 管理当前阅读的文章。
watch(() => props.article, (article) => {
  if (article && dialog.value) {
    dialog.value.showModal()
    dialog.value.scrollTop = 0
  }
  else dialog.value?.close()
}, { flush: 'post' })

function close() {
  dialog.value?.close()
}

function closeOnBackdrop(event: MouseEvent) {
  if (event.target !== dialog.value || !dialog.value) return
  const bounds = dialog.value.getBoundingClientRect()
  if (event.clientX < bounds.left || event.clientX > bounds.right || event.clientY < bounds.top || event.clientY > bounds.bottom) close()
}
</script>

<template>
  <dialog
    ref="dialog"
    class="article-dialog"
    aria-labelledby="reader-title"
    @close="emit('close')"
    @click="closeOnBackdrop"
  >
    <template v-if="article">
      <div class="reader-toolbar">
        <span>拾光 / 文章</span>
        <button type="button" class="icon-button" aria-label="关闭文章" autofocus @click="close">
          <AppIcon name="close" />
        </button>
      </div>
      <ArticleCover :variant="article.cover" />
      <div class="reader-content">
        <span class="badge tag-badge">{{ article.tags.join(' · ') }}</span>
        <h2 id="reader-title">{{ article.title }}</h2>
        <p class="reader-meta">{{ formatDate(article.date) }} · {{ article.readMinutes }} 分钟阅读<template v-if="article.isSample"> · 示例文章</template></p>
        <p class="reader-intro">{{ article.excerpt }}</p>
        <section v-for="section in article.sections" :key="section.heading">
          <h3>{{ section.heading }}</h3>
          <p v-for="paragraph in section.paragraphs" :key="paragraph">{{ paragraph }}</p>
        </section>
        <div class="reader-end">
          <AppIcon name="leaf" />
          <span>感谢阅读，愿这里的文字带给你一点灵感。</span>
        </div>
      </div>
    </template>
  </dialog>
</template>
