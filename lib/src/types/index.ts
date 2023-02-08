import type {
  Config,
  RouteParam,
  RouteParamsWithQueryOverload,
} from 'ziggy-js'

export interface StewardOptions {
  /**
   * Generate `ziggy.js` file, it's native ziggy feature.
   * @docs https://github.com/tighten/ziggy#advanced-setup
   *
   * @default false
   */
  ziggyJs?: boolean
  /**
   * Generate `types-ziggy.d.ts` file
   *  - `ZiggyLaravelRoutes` interface for Laravel routes
   *  - an interface `InertiaPage` for `usePage` with Inertia
   *  - `$route`, `$isRoute`, `$currentRoute`, `$page`, `sessions` inject as `globalProperties` into Inertia if you install `InertiaTyped` Vue
   *
   * @default {
   *   output: 'resources/js',
   *   outputFile: 'types-ziggy.d.ts',
   * }
   */
  ziggyTypes?: {
    output?: string
    outputFile?: string
  } | false
  /**
   * Enable types for Eloquent models.
   *
   * @default {
   *   output: 'resources/js',
   *   outputFile: 'types-models.d.ts',
   *   modelsPath: 'app/Models',
   *   paginate: true,
   *   fakeTeam: false,
   * }
   */
  modelsTypes?: {
    output?: string
    outputFile?: string
    modelsPath?: string
    paginate?: boolean
    fakeTeam?: boolean
  } | false
  /**
   * Enable Vite autoreload on PHP files changes.
   *
   * @default {
   *  models: true,
   * controllers: true,
   * routes: true,
   * }
   */
  autoreload?: {
    models?: boolean
    controllers?: boolean
    routes?: boolean
  } | false
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
