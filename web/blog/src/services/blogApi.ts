import type { Article, ArticleSection, ArticleTag } from '../types/blog'

const apiBase = (import.meta.env.VITE_API_BASE_URL ?? '/api').replace(/\/$/, '')

function isRecord(value: unknown): value is Record<string, unknown> {
  return typeof value === 'object' && value !== null
}
function isSection(value: unknown): value is ArticleSection {
  return isRecord(value) && typeof value.heading === 'string' && Array.isArray(value.paragraphs)
    && value.paragraphs.every(paragraph => typeof paragraph === 'string')
}
function isArticle(value: unknown): value is Article {
  return isRecord(value) && Number.isInteger(value.id) && typeof value.title === 'string'
    && typeof value.excerpt === 'string' && typeof value.date === 'string'
    && typeof value.readMinutes === 'number' && value.readMinutes > 0
    && typeof value.cover === 'string' && ['landscape', 'code', 'design', 'coffee', 'notes'].includes(value.cover)
    && Array.isArray(value.tags) && value.tags.every(tag => typeof tag === 'string')
    && Array.isArray(value.sections) && value.sections.every(isSection)
    && typeof value.isFeatured === 'boolean' && typeof value.isSample === 'boolean'
}
function isTag(value: unknown): value is ArticleTag {
  return isRecord(value) && typeof value.name === 'string' && typeof value.count === 'number'
    && Number.isInteger(value.count) && value.count >= 0
}

async function requestList<T>(path: string, validate: (value: unknown) => value is T, signal: AbortSignal): Promise<T[]> {
  const response = await fetch(`${apiBase}${path}`, { signal, headers: { Accept: 'application/json' } })
  if (!response.ok) throw new Error(`Blog API returned ${response.status}`)
  const payload: unknown = await response.json()
  if (!isRecord(payload) || !Array.isArray(payload.data) || !payload.data.every(validate)) {
    throw new Error('Blog API returned an invalid response')
  }
  return payload.data
}

export function fetchArticles(signal: AbortSignal): Promise<Article[]> {
  return requestList('/articles', isArticle, signal)
}
export function fetchTags(signal: AbortSignal): Promise<ArticleTag[]> {
  return requestList('/tags', isTag, signal)
}
