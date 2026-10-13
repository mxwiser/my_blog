import type { ObjectDirective } from 'vue'

// 使用纯色扩散反馈，不叠加透明层；组件卸载时移除监听器。
const listeners = new WeakMap<HTMLElement, {
  pointer: (event: PointerEvent) => void
  keyboard: (event: KeyboardEvent) => void
}>()

function animateRipple(element: HTMLElement, x: number, y: number) {
  if (element.matches(':disabled') || window.matchMedia('(prefers-reduced-motion: reduce)').matches) return

  const size = Math.hypot(element.clientWidth, element.clientHeight) * 2
  const ripple = document.createElement('span')
  ripple.className = 'md-ripple'
  ripple.setAttribute('aria-hidden', 'true')
  Object.assign(ripple.style, {
    width: `${size}px`, height: `${size}px`,
    left: `${x - size / 2}px`, top: `${y - size / 2}px`,
  })
  element.append(ripple)
  const animation = ripple.animate([
    { transform: 'scale(0)' },
    { transform: 'scale(1)' },
  ], { duration: 480, easing: 'cubic-bezier(0.2, 0, 0, 1)' })
  animation.onfinish = () => ripple.remove()
}

export const ripple: ObjectDirective<HTMLElement> = {
  mounted(element) {
    element.classList.add('ripple-target')
    const pointer = (event: PointerEvent) => {
      const bounds = element.getBoundingClientRect()
      animateRipple(element, event.clientX - bounds.left, event.clientY - bounds.top)
    }
    const keyboard = (event: KeyboardEvent) => {
      if (!event.repeat && (event.key === 'Enter' || event.key === ' ')) {
        animateRipple(element, element.clientWidth / 2, element.clientHeight / 2)
      }
    }
    listeners.set(element, { pointer, keyboard })
    element.addEventListener('pointerdown', pointer)
    element.addEventListener('keydown', keyboard)
  },
  unmounted(element) {
    const handlers = listeners.get(element)
    if (!handlers) return
    element.removeEventListener('pointerdown', handlers.pointer)
    element.removeEventListener('keydown', handlers.keyboard)
    listeners.delete(element)
  },
}
