import { defineConfig } from 'tsup'

export default defineConfig({
  name: '@kiwilan/steward-laravel',
  entry: {
    index: 'src/index.ts',
  },
  format: ['cjs', 'esm'],
  external: ['alpinejs', 'vanilla-cookieconsent'],
  outDir: 'dist',
  dts: true,
  minify: false,
  treeshake: true,
  splitting: true,
})
