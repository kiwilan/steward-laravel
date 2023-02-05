import { defineConfig } from 'tsup'

export default defineConfig({
  name: 'vite-plugin-steward-laravel',
  entry: {
    index: 'lib/index.ts',
    // 'style/index': 'src/style.ts',
    // 'tiptap/index': 'src/tiptap.ts',
  },
  format: ['cjs', 'esm'],
  external: ['vue', '@inertiajs/vue3', 'ziggy-js'],
  // outExtension() {
  //   return {
  //     js: '.cjs',
  //   }
  // },
  dts: true,
  minify: true,
  treeshake: true,
  splitting: true,
})
