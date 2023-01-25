import type { Plugin } from 'vue'
import type {
  Config,
  RouteParam,
  RouteParamsWithQueryOverload,
} from 'ziggy-js'
import route from 'ziggy-js'
import type { InertiaTypedOptions, Route } from '@/types'

const InertiaTyped: Plugin = {
  install: (app, options: InertiaTypedOptions) => {
    app.config.globalProperties.$inertiaTypedOptions = options
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

    app.provide('inertia', {
      options: app.config.globalProperties.$inertiaTypedOptions,
      route: app.config.globalProperties.$route,
      isRoute: app.config.globalProperties.$isRoute,
      currentRoute: app.config.globalProperties.$currentRoute,
    })

    return app
  },
}

export default InertiaTyped
