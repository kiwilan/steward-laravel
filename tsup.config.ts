import { defineConfig } from 'tsup'

export default defineConfig({
  name: 'steward',
  entryPoints: {
    'vite-steward': 'lib/vite-steward.ts',
    'tiptap': 'lib/tiptap.ts',
  },
  format: ['esm', 'cjs'],
  dts: true,
  // minify: true,
})
