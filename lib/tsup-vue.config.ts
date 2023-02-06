import { defineConfig } from 'tsup'

export default defineConfig({
  name: 'vite-plugin-steward-laravel',
  entry: {
    index: 'src/vue/index.ts',
  },
  format: ['cjs', 'esm'],
  external: ['vue', '@inertiajs/vue3', 'ziggy-js'],
  outDir: 'vue',
  dts: true,
  minify: true,
  treeshake: true,
  splitting: true,
})
