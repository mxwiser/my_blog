<script setup lang="ts">
import type { ArticleTag } from '../types/blog'
import AppIcon from './AppIcon.vue'

defineProps<{ selectedTag: string | null; tags: ArticleTag[] }>()
const emit = defineEmits<{ tag: [tag: string] }>()
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
          :key="tag.name"
          v-ripple
          class="tag-button"
          :class="{ active: selectedTag === tag.name }"
          type="button"
          :aria-pressed="selectedTag === tag.name"
          @click="emit('tag', tag.name)"
        >
          {{ tag.name }}
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
