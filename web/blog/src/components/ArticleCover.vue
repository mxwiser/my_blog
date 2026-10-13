<script setup lang="ts">
import type { Article } from '../types/blog'
import AppIcon from './AppIcon.vue'
defineProps<{ variant: Article['cover'] }>()
</script>

<template>
  <div class="article-cover" :class="`cover-${variant}`" aria-hidden="true">
    <img v-if="variant === 'landscape'" src="/images/landscape.svg" alt="" class="landscape-image" />
    <template v-else-if="variant === 'code'">
      <div class="code-window">
        <div class="window-dots">
          <i></i>
          <i></i>
          <i></i>
          <span>useInspiration.ts</span>
        </div>
        <div class="code-lines">
          <span>
            <b>const</b>
            curiosity =
            <em>ref</em>
            (true)
          </span>
          <span>
            <b>const</b>
            ideas =
            <em>computed</em>
            (() =&gt; {
          </span>
          <span>
            &nbsp;
            <b>return</b>
            explore(life)
          </span>
          <span>})</span>
        </div>
      </div>
      <span class="cover-caption">KEEP BUILDING. KEEP LEARNING.</span>
    </template>
    <template v-else-if="variant === 'design'">
      <div class="design-sheet">
        <span class="design-small">THE ART OF LESS</span>
        <span class="design-letter">
          Aa
          <span>.</span>
        </span>
        <div class="color-swatches">
          <i></i>
          <i></i>
          <i></i>
          <i></i>
        </div>
        <span class="design-baseline">Space to breathe.</span>
      </div>
      <span class="cover-caption">LESS, BUT BETTER.</span>
    </template>
    <template v-else-if="variant === 'coffee'">
      <div class="coffee-scene">
        <div class="coffee-book"></div>
        <div class="coffee-saucer">
          <div class="coffee-cup">
            <span></span>
          </div>
        </div>
        <div class="coffee-leaf leaf-one"></div>
        <div class="coffee-leaf leaf-two"></div>
      </div>
      <span class="cover-caption">A LITTLE MOMENT FOR YOURSELF.</span>
    </template>
    <template v-else>
      <div class="notes-sheet">
        <span>
          little things,
          <br />
          big ideas.
        </span>
        <div class="note-lines"></div>
        <AppIcon name="pen" />
      </div>
      <span class="cover-caption">MAKE ROOM FOR IDEAS.</span>
    </template>
  </div>
</template>
