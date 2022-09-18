import fs from 'fs'
import type { Plugin } from 'vite'

interface Options {
  /**
   * Where JS scripts will be copied
   * @default './public/vendor/js'
   */
  outputDirScripts?: string
  /**
   * Where JS libraries will be copied
   * @default './resources/js'
   */
  outputDirLibraries?: string
}

const outputDirScripts = './public/vendor/js'
const outputDirLibraries = './resources/js'

const DEFAULT_OPTIONS: Options = {
  outputDirScripts,
  outputDirLibraries,
}

function plugin(userOptions: Options = {}): Plugin {
  return {
    name: 'vite-plugin-markdoc-content',
    async buildStart() {
      const opts: Options = Object.assign({}, DEFAULT_OPTIONS, userOptions)

      const filesToCopy: {
        name: string
        path: string
        library: boolean
      }[] = [
        {
          name: 'color-mode.js',
          path: 'resources/js/color-mode.js',
          library: false,
        },
        {
          name: 'tiptap.js',
          path: 'dist/tiptap.cjs',
          library: true,
        },
      ]

      const path = `${process.cwd()}/vendor/kiwilan/laravel-steward`

      await fs.promises.mkdir(opts.outputDirScripts ?? outputDirScripts, { recursive: true }).catch(console.error)
      await fs.promises.mkdir(opts.outputDirLibraries ?? outputDirLibraries, { recursive: true }).catch(console.error)

      for (const file of filesToCopy) {
        const outputDir = file.library ? opts.outputDirLibraries : opts.outputDirScripts as string
        fs.copyFile(`${path}/${file.path}`, `${outputDir}/${file.name}`, (err) => {
          if (err)
            throw err
        })
      }
    },
  }
}

export type { Options }
export default plugin
