import { ref, watch } from 'vue'

type Theme = 'light' | 'dark'
const storageKey = 'shiguang-theme'

function getInitialTheme(): Theme {
  try {
    const saved = localStorage.getItem(storageKey)
    if (saved === 'light' || saved === 'dark') return saved
  } catch {
    // 浏览器禁用存储时，仍然可以正常切换主题。
  }
  return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'
}

export function useTheme() {
  const theme = ref<Theme>(getInitialTheme())

  watch(theme, (value) => {
    document.documentElement.dataset.theme = value
    document.documentElement.dataset.bsTheme = value
    document.querySelector('meta[name="theme-color"]')?.setAttribute(
      'content', value === 'dark' ? '#17121c' : '#faf8fc',
    )
    try {
      localStorage.setItem(storageKey, value)
    } catch {
      // 保存偏好失败不影响当前主题。
    }
  }, { immediate: true })

  function toggleTheme() {
    theme.value = theme.value === 'light' ? 'dark' : 'light'
  }

  return { theme, toggleTheme }
}
