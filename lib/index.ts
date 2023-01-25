import type { IInertiaTyped, InertiaTypedOptions, StewardOptions } from '@/types'
import { InertiaTyped, useInertia } from '@/modules/vue'
import { Steward } from '@/modules/vite-plugin'
// import EditorAlpinePlugin from '@/modules/editor'
// import './css/tiptap.css'

export type { IInertiaTyped, InertiaTypedOptions, StewardOptions }
export {
  Steward,
  InertiaTyped,
  useInertia,
}
