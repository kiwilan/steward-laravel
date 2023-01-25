import type { Plugin } from 'vue'
import type {
  Config,
  RouteParam,
  RouteParamsWithQueryOverload,
} from 'ziggy-js'
import route from 'ziggy-js'
import { router as routerInertia } from '@inertiajs/vue3'

type Route = keyof ZiggyLaravelRoutes
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
  router: {
    get: (url: Route, data?: RequestPayload) => Promise<any>
    post: (url: Route, data?: RequestPayload) => Promise<any>
    patch: (url: Route, data?: RequestPayload) => Promise<any>
    put: (url: Route, data?: RequestPayload) => Promise<any>
    delete: (url: Route) => Promise<any>
  }
}

export interface PluginOptions {
  inject: boolean
}

const InertiaTyped: Plugin = {
  install: (app, options: PluginOptions) => {
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

    const router = {
      get: (url: Route, data?: RequestPayload) => routerInertia.get(url, data),
      post: (url: Route, data?: RequestPayload) => routerInertia.post(url, data),
      patch: (url: Route, data?: RequestPayload) => routerInertia.patch(url, data),
      put: (url: Route, data?: RequestPayload) => routerInertia.put(url, data),
      delete: (url: Route) => routerInertia.delete(url),
    }

    app.provide('inertia', {
      route: app.config.globalProperties.$route,
      isRoute: app.config.globalProperties.$isRoute,
      currentRoute: app.config.globalProperties.$currentRoute,
      router,
    })

    return app
  },
}

export default InertiaTyped
