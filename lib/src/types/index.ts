import type {
  Config,
  RouteParam,
  RouteParamsWithQueryOverload,
} from 'ziggy-js'

export interface StewardOptions {
  /**
   * Enable `ziggy` types.
   * - `js` will generate `ziggy.js` file, it's native ziggy feature.
   * - `types` will generate `types-ziggy.d.ts` file
   *    - `ZiggyLaravelRoutes` interface for Laravel routes
   *    - an interface `InertiaPage` for `usePage` with Inertia
   *    - `$route`, `$isRoute`, `$currentRoute`, `$page`, `sessions` inject as `globalProperties` into Inertia if you install `InertiaTyped` Vue `
   * @docs https://github.com/tighten/ziggy#advanced-setup
   *
   * @default {
   *   js: false,
   *   types: false,
   * }
   */
  ziggy?: {
    js?: boolean
    types?: boolean
  }
  /**
   * Enable types for Eloquent models.
   *
   * @default {
   *   modelsPath: 'app/Models',
   *   output: 'resources/js',
   *   outputFile: 'types-models.d.ts',
   *   fakeTeam: false,
   *   paginate: true,
   * }
   */
  modelsTypes?: {
    modelsPath?: string
    output?: string
    outputFile?: string
    fakeTeam?: boolean
    paginate?: boolean
  }
}

export type Route = keyof ZiggyLaravelRoutes
export type RequestPayload = Record<string, any>
export interface IInertiaTyped {
  options: InertiaTypedOptions
  route: (
    name: Route,
    params?: RouteParamsWithQueryOverload | RouteParam,
    absolute?: boolean,
    customZiggy?: Config
  ) => string
  isRoute: (name: Route, params?: RouteParamsWithQueryOverload) => boolean
  currentRoute: () => string
}
export interface InertiaTypedOptions {
  inject: boolean
  router: any
}
