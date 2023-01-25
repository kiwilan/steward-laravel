import { inject } from 'vue'
import type { IInertiaTyped, InertiaTypedOptions, RequestPayload, Route } from '@/types'

export const useInertia = async () => {
  const inertia = inject('inertia') as IInertiaTyped
  const options = inertia.options as InertiaTypedOptions
  const inertiaRouter = options.router
  const inertiaUsePage = options.usePage

  const usePage = async () => {
    return import('@inertiajs/vue3')
      .then(({ usePage }) => usePage())
  }

  const convertURL = (url: Route) => {
    return inertia.route(url)
  }
  const router = {
    get: (url: Route, data?: RequestPayload) => inertiaRouter?.get(convertURL(url), data),
    post: (url: Route, data?: RequestPayload) => inertiaRouter?.post(convertURL(url), data),
    patch: (url: Route, data?: RequestPayload) => inertiaRouter?.patch(convertURL(url), data),
    put: (url: Route, data?: RequestPayload) => inertiaRouter?.put(convertURL(url), data),
    delete: (url: Route) => inertiaRouter?.delete(convertURL(url)),
  }

  type Props = InertiaPage['props']
  type Jetstream = Props['jetstream']
  type User = Props['user']

  const page = await usePage() as unknown as InertiaPage
  const jetstream = page.props.jetstream as Jetstream
  const user = page.props.user as User

  return {
    router,
    options,
    route: inertia.route,
    isRoute: inertia.isRoute,
    currentRoute: inertia.currentRoute,
    page: {
      props: {
        jetstream,
        user,
      },
    },
  }
}
