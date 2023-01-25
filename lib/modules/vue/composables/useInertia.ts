import { inject } from 'vue'
import type { IInertiaTyped, RequestPayload, Route } from '@/types'

export function useInertia() {
  const inertia = inject('inertia') as IInertiaTyped
  const options = inertia.options as any | undefined
  const inertiaRouter = options.router

  const router = {
    get: (url: Route, data?: RequestPayload) => inertiaRouter?.get(url, data),
    post: (url: Route, data?: RequestPayload) => inertiaRouter?.post(url, data),
    patch: (url: Route, data?: RequestPayload) => inertiaRouter?.patch(url, data),
    put: (url: Route, data?: RequestPayload) => inertiaRouter?.put(url, data),
    delete: (url: Route) => inertiaRouter?.delete(url),
  }

  return {
    router,
    options,
    route: inertia.route,
    isRoute: inertia.isRoute,
    currentRoute: inertia.currentRoute,
  }
}
