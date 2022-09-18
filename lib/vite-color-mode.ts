import * as fs from 'fs'
// import type { Plugin } from 'vite'

interface Options {
  /**
   * Where `color-mode.js` will be copied
   * @default './public/js'
   */
  outputDir?: string
}

export const nodeModulesPathDefault = './node_modules/markdoc-content'
export const contentPathDefault = './content/'

const DEFAULT_OPTIONS: Options = {
  outputDir: './public/js',
}

function plugin(userOptions: Options = {}) {
  return {
    name: 'vite-plugin-markdoc-content',
    async buildStart() {
      const opts: Options = Object.assign({}, DEFAULT_OPTIONS, userOptions)

      const pathColorMode = 'vendor/kiwilan/laravel-steward/resources/js/color-mode.js'
      const path = process.cwd()
      const fullPath = `${path}/${pathColorMode}`

      await fs.promises.mkdir(opts.outputDir, { recursive: true }).catch(console.error)

      fs.copyFile(fullPath, `${opts.outputDir}/color-mode.js`, (err) => {
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
