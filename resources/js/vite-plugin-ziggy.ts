import { exec } from 'child_process'
import type { Plugin } from 'vite'

interface Options {}

const DEFAULT_OPTIONS: Options = {
}

function plugin(options: Options = {}): Plugin {
  return {
    name: 'vite-plugin-ziggy',
    buildStart() {
      const opts: Options = Object.assign({}, DEFAULT_OPTIONS, options)

      const command = (command: string) => exec(
        command,
        (error, stdout) => {
          if (error) {
            console.error(`exec error: ${error}`)
            return
          }
          console.log(`${command} ready!`)
        },
      )

      command('php artisan ziggy:generate')
      command('php artisan generate:type models')
      command('php artisan generate:type ziggy')
    },
    handleHotUpdate({ file, server }) {
      if (file.endsWith('.md'))
        server.restart()
    },
  }
}

// export * from './types'
// export type { Options }
export default plugin
