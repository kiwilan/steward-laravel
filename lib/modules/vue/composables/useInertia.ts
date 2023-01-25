import type { IInertiaTyped } from '../../vue-plugin-inertia-typed'

export function useInertia() {
  const router = () => {
    return {
      get: (url: string, data?: any) => console.log(url, data),
    }
  }
  // const inertia = inject<IInertiaTyped>('inertia') as IInertiaTyped

  // return inertia

  return {
    router,
  }
}
