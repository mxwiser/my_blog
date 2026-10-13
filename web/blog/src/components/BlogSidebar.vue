<script setup lang="ts">
import { computed } from 'vue'
import { articles } from '../data/articles'
import AppIcon from './AppIcon.vue'

defineProps<{ selectedTag: string | null }>()
const emit = defineEmits<{ tag: [tag: string] }>()
const tags = computed(() => [...new Set(articles.flatMap(article => article.tags))])
</script>

<template>
  <aside class="blog-sidebar" aria-label="文章标签">
    <section class="card sidebar-section">
      <h2>
        <span class="hash-icon">#</span>
        文章标签
      </h2>
      <div class="tag-cloud">
        <button
          v-for="tag in tags"
          :key="tag"
          v-ripple
          class="tag-button"
          :class="{ active: selectedTag === tag }"
          type="button"
          :aria-pressed="selectedTag === tag"
          @click="emit('tag', tag)"
        >
          {{ tag }}
        </button>
      </div>
    </section>
    <section class="sidebar-note">
      <AppIcon name="book" />
      <span class="eyebrow">A SMALL REMINDER</span>
      <blockquote>
        好想法来自好奇心，
        <br />
        也来自认真生活。
      </blockquote>
      <span class="note-credit">留一点时间，给自己。</span>
    </section>
  </aside>
</template>
