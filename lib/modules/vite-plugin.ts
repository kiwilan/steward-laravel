import type { Plugin } from 'vite'
import type { StewardOptions } from '@/types'

const DEFAULT_OPTIONS: StewardOptions = {
  inertia: {
    modelsTypes: false,
    ziggyTypes: false,
    ziggyJs: false,
  },
}

const command = (command: string) => {
  let exec = (_command: string, _callback: (error: any, stdout: any) => void) => {}
  if (process.env.NODE_ENV !== 'production') {
    // eslint-disable-next-line @typescript-eslint/no-var-requires
    exec = require('child_process').exec
  }
  exec(
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
}

const Steward = (userOptions: StewardOptions = {}): Plugin => {
  return {
    name: 'vite-plugin-markdoc-content',
    async buildStart() {
      const opts: StewardOptions = Object.assign({}, DEFAULT_OPTIONS, userOptions)

      if (opts.inertia?.ziggyJs)
        command('php artisan ziggy:generate')
      if (opts.inertia?.modelsTypes)
        command('php artisan typescriptable:models')
      if (opts.inertia?.ziggyTypes)
        command('php artisan generate:type ziggy')
    },
    handleHotUpdate({ file, server }) {
      // if (file.endsWith('.php'))
      //   server.restart()
    },
  }
}

export type { StewardOptions }
export {
  Steward,
}
