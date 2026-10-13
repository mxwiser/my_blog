import { onMounted, onUnmounted, ref } from 'vue'
import { fetchArticles, fetchTags } from '../services/blogApi'
import type { Article, ArticleTag } from '../types/blog'

export function useBlog() {
  const articles = ref<Article[]>([])
  const tags = ref<ArticleTag[]>([])
  const isLoading = ref(true)
  const loadError = ref('')
  let controller: AbortController | null = null

  async function loadBlog() {
    controller?.abort()
    const request = new AbortController()
    controller = request
    isLoading.value = true
    loadError.value = ''
    try {
      const [posts, articleTags] = await Promise.all([
        fetchArticles(request.signal), fetchTags(request.signal),
      ])
      if (request.signal.aborted) return
      articles.value = posts
      tags.value = articleTags
    } catch (error) {
      if (!request.signal.aborted) {
        loadError.value = '暂时无法加载文章，请稍后重试。'
        console.error('Failed to load blog data:', error)
      }
    } finally {
      if (controller === request && !request.signal.aborted) isLoading.value = false
    }
  }

  onMounted(loadBlog)
  onUnmounted(() => controller?.abort())
  return { articles, tags, isLoading, loadError, loadBlog }
}
