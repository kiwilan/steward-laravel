import { defineConfig } from 'tsup'

export default defineConfig({
  name: 'steward',
  entryPoints: {
    steward: 'lib/index.ts',
    tiptap: 'lib/tiptap/index.ts',
  },
  format: ['esm', 'cjs'],
  dts: true,
  minify: true,
})
