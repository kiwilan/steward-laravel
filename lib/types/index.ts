import type {
  Config,
  RouteParam,
  RouteParamsWithQueryOverload,
} from 'ziggy-js'

export interface StewardOptions {
  // /**
  //  * Where JS scripts will be copied
  //  * @default './public/vendor/js'
  //  */
  // outputDirScripts?: string
  // /**
  //  * Where JS libraries will be copied
  //  * @default './resources/js'
  //  */
  // outputDirLibraries?: string
  /**
   * Whether to use inertia or not
   * @default false
   */
  inertia?: boolean
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
