import { defineConfig } from 'tsup'

export default defineConfig({
  name: '@kiwilan/steward-laravel',
  entry: {
    index: 'src/index.ts',
  },
  format: ['cjs', 'esm'],
  external: ['alpinejs'],
  outDir: 'dist',
  dts: true,
  minify: true,
  treeshake: true,
  splitting: true,
})
