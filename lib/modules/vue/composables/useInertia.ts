import { inject } from 'vue'
import type { IInertiaTyped, RequestPayload, Route } from '@/types'

export function useInertia() {
  const inertia = inject('inertia') as IInertiaTyped
  const options = inertia.options as any | undefined
  const inertiaRouter = options.router

  const convertURL = (url: Route) => `/${url}`
  const router = {
    get: (url: Route, data?: RequestPayload) => inertiaRouter?.get(convertURL(url), data),
    post: (url: Route, data?: RequestPayload) => inertiaRouter?.post(convertURL(url), data),
    patch: (url: Route, data?: RequestPayload) => inertiaRouter?.patch(convertURL(url), data),
    put: (url: Route, data?: RequestPayload) => inertiaRouter?.put(convertURL(url), data),
    delete: (url: Route) => inertiaRouter?.delete(convertURL(url)),
  }

  return {
    router,
    options,
    route: inertia.route,
    isRoute: inertia.isRoute,
    currentRoute: inertia.currentRoute,
  }
}
