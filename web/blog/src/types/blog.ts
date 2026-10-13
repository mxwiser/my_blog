export interface ArticleSection {
  heading: string
  paragraphs: string[]
}

export interface Article {
  id: number
  title: string
  excerpt: string
  date: string
  readMinutes: number
  tags: string[]
  cover: 'landscape' | 'code' | 'design' | 'coffee' | 'notes'
  sections: ArticleSection[]
}
