import { useRouter } from '#app'
import type { RouteType, TypedRouteList, TypedRouteParams } from '~~/.nuxt/typed-link'
import { routes } from '~~/.nuxt/typed-link-routes'

export const useTypedLink = () => {
  const router = useRouter()
  const push = (to: RouteType) => {
    router.push(to)
  }
  const replace = (to: RouteType) => {
    router.replace(to)
  }
  const go = (n: number) => {
    router.go(n)
  }
  const back = () => {
    router.back()
  }
  const forward = () => {
    router.forward()
  }

  return {
    push,
    replace,
    go,
    back,
    forward,
    routes
  }
}
