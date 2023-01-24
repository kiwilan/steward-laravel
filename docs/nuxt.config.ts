export default defineNuxtConfig({
  content: {
    highlight: {
      preload: ['diff', 'json', 'js', 'ts', 'css', 'shell', 'html', 'md', 'yaml', 'php'],
    },
  },
  css: ['@/assets/css/main.css'],
  extends: '@nuxt-themes/docus',
  imports: {
    autoImport: true,
  },
})
