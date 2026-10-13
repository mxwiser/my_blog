<script setup lang="ts">
import { computed, nextTick, ref } from 'vue'
import BlogHeader from './components/BlogHeader.vue'
import BlogSidebar from './components/BlogSidebar.vue'
import ArticleCard from './components/ArticleCard.vue'
import ArticleCover from './components/ArticleCover.vue'
import ArticleReader from './components/ArticleReader.vue'
import AppIcon from './components/AppIcon.vue'
import { formatDate } from './utils/date'
import { useBlog } from './composables/useBlog'
import { useTheme } from './composables/useTheme'
import type { Article } from './types/blog'

const { theme, toggleTheme } = useTheme()
const { articles, tags, isLoading, loadError, loadBlog } = useBlog()
const featuredArticle = computed(() => articles.value.find(article => article.isFeatured) ?? articles.value[0])
const currentView = ref<'home' | 'archive'>('home')
const selectedTag = ref<string | null>(null)
const searchQuery = ref('')
const searchOpen = ref(false)
const searchInput = ref<HTMLInputElement | null>(null)
const visibleCount = ref(4)
const activeArticle = ref<Article | null>(null)

// 标签精确匹配，关键词搜索标题、摘要和标签；列表统一由 computed 派生。
const filteredArticles = computed(() => {
  const query = searchQuery.value.trim().toLocaleLowerCase()
  return articles.value.filter(article => {
    const matchesTag = selectedTag.value === null || article.tags.includes(selectedTag.value)
    const searchableText = [article.title, article.excerpt, ...article.tags].join(' ').toLocaleLowerCase()
    return matchesTag && (!query || searchableText.includes(query))
  })
})
const visibleArticles = computed(() => filteredArticles.value.slice(0, visibleCount.value))
const archiveGroups = computed(() => {
  const groups = new Map<string, Article[]>()
  filteredArticles.value.forEach(article => {
    const month = article.date.slice(0, 7)
    groups.set(month, [...(groups.get(month) ?? []), article])
  })
  return [...groups].map(([month, posts]) => ({
    label: `${month.slice(0, 4)} 年 ${Number(month.slice(5))} 月`, posts,
  }))
})

function scrollBehavior(): ScrollBehavior {
  return window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth'
}

function scrollToTop() {
  window.scrollTo({ top: 0, behavior: scrollBehavior() })
}

function scrollToSection(id: string) {
  document.getElementById(id)?.scrollIntoView({ behavior: scrollBehavior(), block: 'start' })
}

async function navigate(view: 'home' | 'archive') {
  currentView.value = view
  resetFilters()
  await nextTick()
  if (view === 'archive') scrollToSection('articles')
  else scrollToTop()
}

async function toggleSearch() {
  searchOpen.value = !searchOpen.value
  if (searchOpen.value) {
    await nextTick()
    scrollToSection('articles')
    searchInput.value?.focus({ preventScroll: true })
  } else {
    searchQuery.value = ''
  }
}

async function selectTag(tag: string) {
  selectedTag.value = selectedTag.value === tag ? null : tag
  visibleCount.value = 4
  await nextTick()
  scrollToSection('articles')
}

function resetFilters() {
  selectedTag.value = null
  searchQuery.value = ''
  visibleCount.value = 4
}
</script>

<template>
  <a class="skip-link" href="#main-content">跳到主要内容</a>
  <BlogHeader
    :theme="theme"
    :current-view="currentView"
    :search-open="searchOpen"
    @navigate="navigate"
    @search="toggleSearch"
    @toggle-theme="toggleTheme"
  />
  <main id="main-content" class="container blog-container">
    <h1 class="visually-hidden">拾光 · 个人博客</h1>
    <section v-if="isLoading" class="blog-status" role="status">
      <AppIcon name="book" />正在加载文章…
    </section>
    <section v-else-if="loadError" class="alert alert-danger blog-status" role="alert">
      <span>{{ loadError }}</span>
      <button v-ripple class="btn btn-outline-primary" type="button" @click="loadBlog">重新加载</button>
    </section>
    <div v-else class="row content-layout">
      <div class="col-12 col-lg-8 main-column">
        <section v-if="currentView === 'home' && featuredArticle" class="featured-section" aria-labelledby="featured-heading">
          <div class="section-heading">
            <div>
              <h2 id="featured-heading">精选文章</h2>
            </div>
            <span class="section-caption">本期精选</span>
          </div>
          <article class="card featured-card">
            <button
              class="featured-cover cover-button"
              type="button"
              :aria-label="`阅读：${featuredArticle.title}`"
              @click="activeArticle = featuredArticle ?? null"
            >
              <ArticleCover :variant="featuredArticle.cover" />
            </button>
            <div class="card-body">
              <span class="badge tag-badge">{{ featuredArticle.tags[0] }}</span>
              <h3>
                <button class="title-button" type="button" @click="activeArticle = featuredArticle ?? null">
                  {{ featuredArticle.title }}
                </button>
              </h3>
              <p>{{ featuredArticle.excerpt }}</p>
              <div class="featured-bottom">
                <span>
                  <time :datetime="featuredArticle.date">{{ formatDate(featuredArticle.date) }}</time>
                  · {{ featuredArticle.readMinutes }} 分钟阅读
                </span>
                <button v-ripple class="read-link" type="button" @click="activeArticle = featuredArticle ?? null">
                  阅读全文
                  <AppIcon name="arrow" />
                </button>
              </div>
            </div>
          </article>
        </section>
        <section id="articles" class="articles-section" aria-labelledby="articles-heading">
          <div class="section-heading">
            <div>
              <h2 id="articles-heading">
                {{ currentView === 'home' ? '最新文章' : '文章归档' }}
                <span class="heading-count">{{ articles.length }}</span>
              </h2>
            </div>
            <button
              v-if="currentView === 'home'"
              v-ripple
              class="read-link"
              type="button"
              @click="navigate('archive')"
            >
              查看归档
              <AppIcon name="arrow" />
            </button>
          </div>
          <div v-show="searchOpen" id="article-search" class="search-box">
            <AppIcon name="search" />
            <label class="visually-hidden" for="search-input">搜索标题、摘要或标签</label>
            <input
              id="search-input"
              ref="searchInput"
              v-model="searchQuery"
              class="form-control"
              type="search"
              placeholder="搜索标题、摘要或标签…"
              @input="visibleCount = 4"
              @keydown.escape="toggleSearch"
            />
            <button v-ripple class="icon-button" type="button" aria-label="关闭搜索" @click="toggleSearch">
              <AppIcon name="close" />
            </button>
          </div>
          <p v-if="selectedTag || searchQuery.trim()" class="search-summary" role="status">
            <span v-if="selectedTag">标签「{{ selectedTag }}」</span>
            <span v-if="searchQuery.trim()">关键词「{{ searchQuery.trim() }}」</span>
            · {{ filteredArticles.length }} 篇文章
            <button type="button" class="read-link" @click="resetFilters">清除筛选</button>
          </p>
          <div v-if="filteredArticles.length === 0" class="empty-state" role="status">
            <AppIcon name="search" />
            <h3>暂时没有找到相关文章</h3>
            <p>换个关键词，或试试其他标签吧。</p>
            <button v-ripple class="btn btn-outline-primary" type="button" @click="resetFilters">查看全部文章</button>
          </div>
          <template v-else-if="currentView === 'home'">
            <div class="row g-4 article-grid">
              <div v-for="article in visibleArticles" :key="article.id" class="col-12 col-sm-6">
                <ArticleCard :article="article" @read="activeArticle = $event" />
              </div>
            </div>
            <div class="load-more">
              <button
                v-if="visibleCount < filteredArticles.length"
                v-ripple
                class="btn btn-outline-primary"
                type="button"
                @click="visibleCount += 4"
              >
                加载更多
                <AppIcon name="arrow" />
              </button>
              <span v-else class="all-read">已经读到最后了，下一次更新再见。</span>
            </div>
          </template>
          <div v-else class="archive-list">
            <section v-for="group in archiveGroups" :key="group.label" class="archive-month">
              <h3>
                {{ group.label }}
                <span>{{ group.posts.length }} 篇</span>
              </h3>
              <button
                v-for="article in group.posts"
                :key="article.id"
                v-ripple
                type="button"
                class="archive-item"
                @click="activeArticle = article"
              >
                <time :datetime="article.date">{{ article.date.slice(5).replace('-', '.') }}</time>
                <span>
                  {{ article.title }}
                  <small>{{ article.tags.join(' · ') }} · {{ article.readMinutes }} 分钟阅读</small>
                </span>
                <AppIcon name="diagonal" />
              </button>
            </section>
          </div>
        </section>
      </div>
      <div class="col-12 col-lg-4 sidebar-column">
        <BlogSidebar :selected-tag="selectedTag" :tags="tags" @tag="selectTag" />
      </div>
    </div>
    <section class="closing-note">
      <AppIcon name="book" />
      <div>
        <h2>保持好奇，让想法在这里生长。</h2>
        <p>谢谢你来过，希望有一段文字，恰好与你相遇。</p>
      </div>
      <span>WRITE. THINK. REPEAT.</span>
    </section>
  </main>
  <footer class="site-footer">
    <div class="container blog-container">
      <p>
        © {{ new Date().getFullYear() }} 记录思考与日常
        <span class="demo-note">示例内容，可替换为你的故事</span>
      </p>
      <button v-ripple class="back-top" type="button" @click="scrollToTop">
        回到顶部
        <AppIcon name="up" />
      </button>
    </div>
  </footer>
  <ArticleReader :article="activeArticle" @close="activeArticle = null" />
</template>
