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

      const pathColorMode = 'vendor/kiwilan/laravel-steward/resources/js/color-mode.js'
      const pathLibrary = 'vendor/kiwilan/laravel-steward/dist/steward.cjs'

      const path = process.cwd()

      await fs.promises.mkdir(opts.outputDir ?? outputDir, { recursive: true }).catch(console.error)

      fs.copyFile(`${path}/${pathColorMode}`, `${opts.outputDir}/color-mode.js`, (err) => {
        if (err)
          throw err
      })
      fs.copyFile(`${path}/${pathLibrary}`, `${opts.outputDir}/steward.js`, (err) => {
        if (err)
          throw err
      })
    },
    // handleHotUpdate({ file, server }) {
    //   if (file.endsWith('.md'))
    //     server.restart()
    // },
  }
}

export type { Options }
export default plugin
