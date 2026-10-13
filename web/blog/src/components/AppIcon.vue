<script setup lang="ts">
// 本地图标集合；使用名称调用，不依赖额外图标库。
const paths = {
  leaf: ['M5 19C3 8 12 3 21 3c0 10-5 18-14 16', 'M5 21 16 10'],
  arrow: ['M4 12h16', 'm14 6 6 6-6 6'],
  diagonal: ['M6 18 18 6', 'M6 6h12v12'],
  search: ['M21 21l-5-5', 'M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0'],
  sun: ['M16 12a4 4 0 1 1-8 0 4 4 0 0 1 8 0', 'M12 2v2m0 16v2M2 12h2m16 0h2M5 5l1.5 1.5m11 11L19 19M5 19l1.5-1.5m11-11L19 5'],
  moon: ['M21 13a9 9 0 0 1-10-10 9 9 0 1 0 10 10Z'],
  menu: ['M4 6h16M4 12h16M4 18h16'],
  close: ['m6 6 12 12M18 6 6 18'],
  clock: ['M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0', 'M12 7v5l3 2'],
  calendar: ['M8 2v4m8-4v4M3 10h18', 'M5 4h14a2 2 0 0 1 2 2v14H3V6a2 2 0 0 1 2-2Z'],
  code: ['m8 5-6 7 6 7m8-14 6 7-6 7m-3-16-2 18'],
  pen: ['m15 5 4 4', 'm3 21 5-1L21 7a2.8 2.8 0 0 0-4-4L4 16l-1 5Z'],
  coffee: ['M3 8h13v7a6.5 6.5 0 0 1-13 0V8Z', 'M16 8h2a3 3 0 0 1 0 6h-2M6 3v2m4-2v2m4-2v2M2 22h16'],
  book: ['M12 5v16', 'M12 5C8 2 4 3 2 4v16c4-2 7-1 10 1 3-2 6-3 10-1V4c-2-1-6-2-10 1Z'],
  folder: ['M3 7V4h6l3 3h9v13H3V7Z'],
  chevron: ['m9 5 7 7-7 7'],
  up: ['M12 20V4m-6 6 6-6 6 6'],
} as const

defineProps<{ name: keyof typeof paths }>()
</script>

<template>
  <svg
    class="app-icon"
    viewBox="0 0 24 24"
    fill="none"
    stroke="currentColor"
    stroke-width="1.65"
    stroke-linecap="round"
    stroke-linejoin="round"
    aria-hidden="true"
  >
    <path v-for="(path, index) in paths[name]" :key="index" :d="path" />
  </svg>
</template>
