<script setup lang="ts">
import type { Article } from '../types/blog'
import { formatDate } from '../data/articles'
import AppIcon from './AppIcon.vue'
import ArticleCover from './ArticleCover.vue'

defineProps<{ article: Article }>()
const emit = defineEmits<{ read: [article: Article] }>()
</script>

<template>
  <article class="card article-card">
    <button class="cover-button" type="button" :aria-label="`阅读：${article.title}`" @click="emit('read', article)">
      <ArticleCover :variant="article.cover" />
    </button>
    <div class="card-body">
      <div class="article-topline">
        <span class="badge tag-badge">{{ article.tags[0] }}</span>
        <span class="read-duration">
          <AppIcon name="clock" />
          {{ article.readMinutes }} 分钟阅读
        </span>
      </div>
      <h3 class="card-title">
        <button type="button" class="title-button" @click="emit('read', article)">{{ article.title }}</button>
      </h3>
      <p class="card-text">{{ article.excerpt }}</p>
      <div class="article-meta">
        <time :datetime="article.date">{{ formatDate(article.date) }}</time>
        <span class="article-tags"># {{ article.tags[1] ?? article.tags[0] }}</span>
      </div>
    </div>
  </article>
</template>
