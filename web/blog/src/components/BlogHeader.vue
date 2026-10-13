<script setup lang="ts">
import { ref } from 'vue'
import AppIcon from './AppIcon.vue'

defineProps<{
  theme: 'light' | 'dark'
  currentView: 'home' | 'archive'
  searchOpen: boolean
}>()
const emit = defineEmits<{
  navigate: [view: 'home' | 'archive']
  search: []
  toggleTheme: []
}>()
const menuOpen = ref(false)

function navigate(view: 'home' | 'archive') {
  menuOpen.value = false
  emit('navigate', view)
}
</script>

<template>
  <header class="site-header">
    <nav class="navbar navbar-expand-md app-bar container blog-container" data-bs-theme="dark" aria-label="主导航">
      <a class="navbar-brand brand" href="#" aria-label="拾光首页" @click.prevent="navigate('home')">
        <span class="brand-mark">
          <AppIcon name="book" />
        </span>
        <span class="brand-name">
          拾光
          <span class="brand-english">PERSONAL BLOG</span>
        </span>
      </a>
      <div id="main-navigation" class="nav main-navigation" :class="{ 'is-open': menuOpen }">
        <a
          class="nav-link"
          :class="{ active: currentView === 'home' }"
          :aria-current="currentView === 'home' ? 'page' : undefined"
          href="#"
          @click.prevent="navigate('home')"
        >
          首页
        </a>
        <a
          class="nav-link"
          :class="{ active: currentView === 'archive' }"
          :aria-current="currentView === 'archive' ? 'page' : undefined"
          href="#articles"
          @click.prevent="navigate('archive')"
        >
          文章归档
        </a>
      </div>
      <div class="header-actions">
        <button
          v-ripple
          class="btn btn-outline-primary header-search"
          type="button"
          :aria-expanded="searchOpen"
          aria-controls="article-search"
          @click="emit('search')"
        >
          <AppIcon name="search" />
          <span>搜索文章</span>
        </button>
        <button
          v-ripple
          class="icon-button"
          type="button"
          :aria-label="theme === 'light' ? '切换深色模式' : '切换浅色模式'"
          @click="emit('toggleTheme')"
        >
          <AppIcon :name="theme === 'light' ? 'moon' : 'sun'" />
        </button>
        <button
          v-ripple
          class="icon-button mobile-menu"
          type="button"
          :aria-expanded="menuOpen"
          aria-controls="main-navigation"
          aria-label="展开或收起导航"
          @click="menuOpen = !menuOpen"
        >
          <AppIcon :name="menuOpen ? 'close' : 'menu'" />
        </button>
      </div>
    </nav>
  </header>
</template>
