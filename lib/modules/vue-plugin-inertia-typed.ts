import type { Plugin } from 'vue'
import type {
  Config,
  RouteParam,
  RouteParamsWithQueryOverload,
} from 'ziggy-js'
import route from 'ziggy-js'
// import { router } from '@inertiajs/vue3'
import { useInertia } from './vue'

type Route = keyof ZiggyLaravelRoutes
// class Router {
//   static current(name?: string, params?: any) {
//     return name
//   }
// }
// const route = (name?: string, params?: any, absolute?: boolean, customZiggy?: any) => {
//   return Router
// }
// type Route = string
// type RouteParamsWithQueryOverload = Record<string, any>
// type RouteParam = string | number
// type Config = Record<string, any>

type RequestPayload = Record<string, any>
export interface IInertiaTyped {
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
}

const InertiaTyped: Plugin = {
  install: (app, options: InertiaTypedOptions) => {
    app.config.globalProperties.$route = (
      name: Route,
      params?: RouteParamsWithQueryOverload | RouteParam,
      absolute?: boolean,
      customZiggy?: Config,
    ) => route(name, params, absolute, customZiggy)
    app.config.globalProperties.$isRoute = (
      name: Route,
      params?: RouteParamsWithQueryOverload,
    ) => route().current(name, params)
    app.config.globalProperties.$currentRoute = () =>
      route().current() as string

    // const routing = {
    // get: (url: Route, data?: RequestPayload) => router.get(url, data),
    // post: (url: Route, data?: RequestPayload) => router.post(url, data),
    // patch: (url: Route, data?: RequestPayload) => router.patch(url, data),
    // put: (url: Route, data?: RequestPayload) => router.put(url, data),
    // delete: (url: Route) => router.delete(url),
    // }

    app.component('UseInertia', useInertia)

    app.provide('inertia', {
      route: app.config.globalProperties.$route,
      isRoute: app.config.globalProperties.$isRoute,
      currentRoute: app.config.globalProperties.$currentRoute,
      // routing,
    })

    return app
  },
}

export default InertiaTyped
