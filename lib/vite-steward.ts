import fs from 'fs'
import type { Plugin } from 'vite'

interface Options {
  /**
   * Where JS files will be copied
   * @default './public/vendor/js'
   */
  outputDir?: string
}

const outputDir = './public/vendor/js'
const DEFAULT_OPTIONS: Options = {
  outputDir,
}

function plugin(userOptions: Options = {}): Plugin {
  return {
    name: 'vite-plugin-markdoc-content',
    async buildStart() {
      const opts: Options = Object.assign({}, DEFAULT_OPTIONS, userOptions)

      const filesToCopy: {
        name: string
        path: string
      }[] = [
        {
          name: 'color-mode.js',
          path: 'resources/js/color-mode.js',
        },
        {
          name: 'tiptap.js',
          path: 'dist/tiptap.cjs',
        },
      ]

      const path = `${process.cwd()}/vendor/kiwilan/laravel-steward`

      await fs.promises.mkdir(opts.outputDir ?? outputDir, { recursive: true }).catch(console.error)

      for (const file of filesToCopy) {
        fs.copyFile(`${path}/${file.path}`, `${opts.outputDir}/${file.name}`, (err) => {
          if (err)
            throw err
        })
      }
    },
  }
}

export type { Options }
export default plugin
