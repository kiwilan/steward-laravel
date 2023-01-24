// import Steward from './steward';
import Ziggy from './vite-plugin-ziggy'
import type { IInertiaTyped, PluginOptions } from './inertia-typed'
import InertiaTyped from './inertia-typed'

const log = (...args) => {
  console.log(...args)
}

export type { IInertiaTyped, PluginOptions }
export {
  log,
  Ziggy,
  InertiaTyped,
}
