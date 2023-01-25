import type {
  Config,
  RouteParam,
  RouteParamsWithQueryOverload,
} from 'ziggy-js'

interface StewardOptionsInertia {
  /**
   * Enable JS `ziggy` routes.
   * @docs https://github.com/tighten/ziggy#advanced-setup
   * @default false
   */
  ziggyJs?: boolean
  /**
   * Enable `ziggy` routes types.
   * @default false
   */
  ziggyTypes?: boolean
  /**
   * Enable types for Eloquent models.
   * @default false
   */
  modelsTypes?: boolean
}
export interface StewardOptions {
  /**
   * Enable `@inertiajs/vue3` types.
   * @default false
   */
  inertia?: StewardOptionsInertia | false
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
  // router: {
  //   get: (url: Route, data?: RequestPayload) => Promise<any>
  //   post: (url: Route, data?: RequestPayload) => Promise<any>
  //   patch: (url: Route, data?: RequestPayload) => Promise<any>
  //   put: (url: Route, data?: RequestPayload) => Promise<any>
  //   delete: (url: Route) => Promise<any>
  // }
}
export interface InertiaTypedOptions {
  inject: boolean
  router: any
}
