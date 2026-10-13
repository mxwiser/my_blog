import { createApp } from 'vue'
import './style.css'
import App from './App.vue'
import { ripple } from './directives/ripple'

createApp(App).directive('ripple', ripple).mount('#app')
