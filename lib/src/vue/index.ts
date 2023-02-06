import type { IInertiaTyped, InertiaTypedOptions } from '../types/index.js'
// import InertiaTyped from './plugin.js'
import { appResolve, appTitle } from './setup.js'
import { useInertia } from './composables/useInertia.js'

export type { IInertiaTyped, InertiaTypedOptions }
export {
  // InertiaTyped,
  appResolve,
  appTitle,
  useInertia,
}
