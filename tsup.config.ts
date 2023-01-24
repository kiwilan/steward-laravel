import { defineConfig } from 'tsup'

export default defineConfig({
  name: 'vite-plugin-laravel-steward',
  entry: {
    index: 'resources/js/index.ts',
  },
  format: ['cjs', 'iife'],
  dts: true,
  minify: false,
  treeshake: true,
  splitting: true,
})
