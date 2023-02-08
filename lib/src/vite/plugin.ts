import type { Plugin } from 'vite'
import type { StewardOptions } from '../types/index.js'

const DEFAULT_OPTIONS: StewardOptions = {
  ziggyJs: false,
  ziggyTypes: {
    output: 'resources/js',
    outputFile: 'types-ziggy.d.ts',
    skipRouter: false,
    skipPage: false,
    embed: false,
  },
  modelsTypes: {
    modelsPath: 'app/Models',
    output: 'resources/js',
    outputFile: 'types-models.d.ts',
    fakeTeam: false,
    paginate: true,
  },
  autoreload: {
    models: true,
    controllers: true,
    routes: true,
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
    name: 'vite-plugin-steward-laravel',
    async buildStart() {
      const opts: StewardOptions = Object.assign({}, DEFAULT_OPTIONS, userOptions)

      if (opts.ziggyJs)
        command('php artisan ziggy:generate')

      if (opts.ziggyTypes) {
        const ziggyTypesBase = 'php artisan typescriptable:ziggy'
        const options = []
        if (opts.ziggyTypes.output)
          options.push(`--output=${opts.ziggyTypes.output}`)
        if (opts.ziggyTypes.outputFile)
          options.push(`--output-file=${opts.ziggyTypes.outputFile}`)
        if (opts.ziggyTypes.skipRouter)
          options.push('--skip-router')
        if (opts.ziggyTypes.skipPage)
          options.push('--skip-page')
        if (opts.ziggyTypes.embed)
          options.push('--embed')

        command(`${ziggyTypesBase} ${options.join(' ')}`)
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
      const opts = Object.assign({}, DEFAULT_OPTIONS, userOptions)
      if (opts.autoreload) {
        if (opts.autoreload.models && file.endsWith('app/Models/**/*.php'))
          server.restart()
        if (opts.autoreload.controllers && file.endsWith('app/Http/Controllers/**/*.php'))
          server.restart()
        if (opts.autoreload.routes && file.endsWith('routes/**/*.php'))
          server.restart()
      }
    },
  }
}

export type { StewardOptions }
export {
  Steward,
}
