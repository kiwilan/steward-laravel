// import fs from 'fs'
import type { Plugin } from 'vite'
import type { StewardOptions } from '@/types'

// const outputDirScriptsDefault = './public/vendor/js'
// const outputDirLibrariesDefault = './resources/libs'

const DEFAULT_OPTIONS: StewardOptions = {
  // outputDirScripts: outputDirScriptsDefault,
  // outputDirLibraries: outputDirLibrariesDefault,
  inertia: false,
}

const Steward = (userOptions: StewardOptions = {}): Plugin => {
  return {
    name: 'vite-plugin-markdoc-content',
    async buildStart() {
      const opts: StewardOptions = Object.assign({}, DEFAULT_OPTIONS, userOptions)
      // const outputDirScripts = opts.outputDirScripts as string
      // const outputDirLibraries = opts.outputDirLibraries as string

      // const filesToCopy: {
      //   name: string
      //   path: string
      //   library: boolean
      // }[] = [
      //   {
      //     name: 'color-mode.js',
      //     path: 'lib/js/color-mode.js',
      //     library: false,
      //   },
      //   // {
      //   //   name: 'tiptap.js',
      //   //   path: 'dist/tiptap.cjs',
      //   //   library: true,
      //   // },
      // ]

      // const path = `${process.cwd()}/node_modules/@kiwilan/vite-plugin-laravel-steward`

      // await fs.promises.mkdir(outputDirScripts, { recursive: true }).catch(console.error)
      // await fs.promises.mkdir(outputDirLibraries, { recursive: true }).catch(console.error)

      // for (const file of filesToCopy) {
      //   const outputDir = file.library ? outputDirLibraries : outputDirScripts
      //   fs.copyFile(`${path}/${file.path}`, `${outputDir}/${file.name}`, (err) => {
      //     if (err)
      //       throw err
      //   })
      // }

      if (opts.inertia) {
        // eslint-disable-next-line @typescript-eslint/no-unused-vars
        let exec = (command: string, callback: (error: any, stdout: any) => void) => {}

        if (process.env.NODE_ENV !== 'production') {
          // eslint-disable-next-line @typescript-eslint/no-var-requires
          exec = require('child_process').exec
        }

        // import { exec } from 'child_process'
        const command = (command: string) => exec(
          command,
          (error) => {
            if (error) {
              console.error(`exec error: ${error}`)
              return
            }
            // eslint-disable-next-line no-console
            console.log(`${command} ready!`)
          },
        )

        command('php artisan ziggy:generate')
        command('php artisan generate:type models')
        command('php artisan generate:type ziggy')
      }
    },
    handleHotUpdate({ file, server }) {
      if (file.endsWith('.php'))
        server.restart()
    },
  }
}

export type { StewardOptions }
export {
  Steward,
}
