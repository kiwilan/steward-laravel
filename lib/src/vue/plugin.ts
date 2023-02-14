import type { Plugin } from 'vue'
import type {
  Config,
  RouteParam,
  RouteParamsWithQueryOverload,
} from 'ziggy-js'
import route from 'ziggy-js'
import { router } from '@inertiajs/vue3'
import type { InertiaTypedOptions, Route } from '../types/index.js'

const InertiaTyped: Plugin = {
  install: (app, options: InertiaTypedOptions) => {
    app.config.globalProperties.$inertiaTypedOptions = options
    app.config.globalProperties.$route = (
      name: Route,
      params?: RouteParamsWithQueryOverload | RouteParam,
      absolute?: boolean,
      customZiggy?: Config,
      // @ts-expect-error - Ziggy doesn't support this overload
    ) => route(name, params, absolute, customZiggy)
    app.config.globalProperties.$isRoute = (
      name: Route,
      params?: RouteParamsWithQueryOverload,
      // @ts-expect-error - Ziggy doesn't support this overload
    ) => route().current(name, params)
    app.config.globalProperties.$currentRoute = () =>
    // @ts-expect-error - Ziggy doesn't support this overload
      route().current() as string

    app.provide('inertia', {
      options: app.config.globalProperties.$inertiaTypedOptions,
      route: app.config.globalProperties.$route,
      isRoute: app.config.globalProperties.$isRoute,
      currentRoute: app.config.globalProperties.$currentRoute,
      router,
    })

    return app
  },
}

export default InertiaTyped
