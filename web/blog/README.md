# 拾光 · 个人博客首页

Vue 3 + TypeScript + Vite + Bootstrap 5.3.8。使用 Bootstrap 栅格、卡片、按钮、导航和表单作为基础，界面采用传统 Bootstrap 博客布局，配合 Material Design 材质层次与点击反馈，以 `#660874` 为主色，使用不透明纯色、小圆角、标签筛选、带边框卡片及纯色点击反馈，没有渐变、半透明状态层或模糊遮罩。主题样式、图标与封面都在本地。当前环境无法安装 Bootstrap npm 包，因此在 `index.html` 中通过官方 CDN 加载固定版本 CSS（带 SRI 校验）；首次访问需联网。交互由 Vue 管理，不引入 Bootstrap JS。

## 启动与构建

```bash
npm install
npm run dev
npm run build
```

## 代码结构

- `src/App.vue`：首页布局、标签筛选、关键词搜索、加载更多及按月归档。
- `src/components/`：导航、文章卡片、封面、标签侧栏、原生 dialog 阅读弹窗与图标。
- `src/data/articles.ts`：示例文章、标签与日期格式；替换为自己的内容即可。
- `src/types/blog.ts`：文章与正文段落的 TypeScript 类型。
- `src/composables/useTheme.ts`：深浅色主题和本地偏好保存。
- `src/directives/ripple.ts`：Material 点击波纹及监听器清理。
- `src/style.css`：颜色角色、组件、布局和响应式样式，按职责分段。
- `public/images/`：本地纯色 SVG 插画。

侧栏标签在 `BlogSidebar.vue`，站点名称在 `BlogHeader.vue`，首页布局在 `App.vue`。文章目前为演示数据，尚未连接后台。
