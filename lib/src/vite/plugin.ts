import type { Plugin } from 'vite'
import type { StewardOptions } from '../types/index.js'

const DEFAULT_OPTIONS: StewardOptions = {
  ziggy: {
    js: false,
    types: false,
  },
  modelsTypes: {
    modelsPath: 'app/Models',
    output: 'resources/js',
    outputFile: 'types-models.d.ts',
    fakeTeam: false,
    paginate: true,
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

      if (opts.ziggy) {
        if (opts.ziggy.js)
          command('php artisan ziggy:generate')

        if (opts.ziggy.types)
          command('php artisan generate:type ziggy')
      }

      if (opts.modelsTypes) {
        const modelsTypesBase = 'php artisan typescriptable:models'
        const options = []
        if (opts.modelsTypes.modelsPath)
          options.push(`--models-path=${opts.modelsTypes.modelsPath}`)
        if (opts.modelsTypes.output)
          options.push(`--output=${opts.modelsTypes.output}`)
        if (opts.modelsTypes.outputFile)
          options.push(`--output-file=${opts.modelsTypes.outputFile}`)
        if (opts.modelsTypes.fakeTeam)
          options.push('--fake-team')
        if (opts.modelsTypes.paginate)
          options.push('--paginate')

        command(`${modelsTypesBase} ${options.join(' ')}`)
      }
    },
    handleHotUpdate({ file, server }) {
      if (file.endsWith('app/Models/**/*.php'))
        server.restart()
    },
  }
}

export type { StewardOptions }
export {
  Steward,
}
