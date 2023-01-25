import type { WriteStream } from 'fs'
import { createWriteStream } from 'fs'
import type { NuxtPage } from '@nuxt/schema'

interface Route {
  name?: string
  path: string
  params?: Record<string, string>
}

export class TypedRoute {
  private constructor(
    private nuxtRoutes: NuxtPage[],
    private typePath: string,
    private routePath: string,
    private routes: Route[] = [],
  ) {
    this.nuxtRoutes = nuxtRoutes
  }

  public static make(pages: NuxtPage[], typePath: string, routePath: string): TypedRoute {
    const typedRoute = new TypedRoute(pages, typePath, routePath)
    typedRoute.routes = typedRoute.setRoutes()
    typedRoute.createCache()

    return typedRoute
  }

  private setRoutes(): Route[] {
    const routes: Route[] = []
    const localeRaw = this.nuxtRoutes.find(route => route.name?.includes('___'))
    let locale: string | undefined

    let i18n = false
    let routeList: {
      name?: string
      path: string
      haveParams: boolean
      params: Record<string, string>
    }[] = []

    this.nuxtRoutes.forEach((route) => {
      let haveParams = false
      let params = {}
      if (route.path.includes(':')) {
        haveParams = true
        const path = route.path.replace(/\/$/, '')
        const pathParts = path.split('/')
        const pathParams = pathParts.filter(part => part.startsWith(':'))
        params = pathParams.reduce((acc, param) => {
          const key = param.replace(':', '')
          return { ...acc, [key]: 'string' }
        }, {})
      }

      if (route.name?.includes('___')) {
        i18n = true
        locale = localeRaw?.name?.split('___')[1] || undefined
      }

      routeList.push({
        name: route.name,
        path: route.path,
        haveParams,
        params,
      })
    })

    if (i18n) {
      routeList = routeList.filter(route => route.name?.includes(`___${locale}`))
      routeList.forEach((route) => {
        route.name = route.name?.replace(`___${locale}`, '')
      })
    }

    routeList.forEach((route) => {
      routes.push({
        name: route.name,
        path: route.path,
        params: route.haveParams ? route.params : undefined,
      })
    })

    return routes
  }

  private createCache() {
    const streamType = createWriteStream(this.typePath, { flags: 'w' })

    streamType.once('open', () => {
      this.setRouteListType(streamType)
      this.setRouteParamsType(streamType)
      this.setRouteType(streamType)
      streamType.end()
    })

    const streamRoute = createWriteStream(this.routePath, { flags: 'w' })
    streamRoute.once('open', () => {
      this.setRouteList(streamRoute)
      streamRoute.end()
    })
  }

  private setRouteList(stream: WriteStream) {
    stream.write('export const routes = {\n')
    this.routes.forEach((route) => {
      stream.write(`  '${route.name}': {\n`)
      stream.write(`    name: '${route.name}',\n`)
      stream.write(`    path: '${route.path}',\n`)
      if (route.params && Object.keys(route.params).length) {
        stream.write('    params: {\n')

        Object.keys(route.params).forEach((key) => {
          if (route.params)
            stream.write(`      ${key}: '${route.params[key]}',\n`)
        })
        stream.write('    },\n')
      }
      else {
        stream.write('    params: undefined,\n')
      }
      stream.write('  },\n')
    })
    stream.write('}\n')
  }

  private setRouteListType(stream: WriteStream) {
    stream.write('export type TypedRouteList =\n')
    this.routes.forEach((route) => {
      stream.write(`  | '${route.name}'\n`)
    })
  }

  private setRouteParamsType(stream: WriteStream) {
    stream.write('export type TypedRouteParams = {\n')
    this.routes.forEach((route) => {
      if (route.params) {
        stream.write(`  '${route.name}': {\n`)
        Object.keys(route.params).forEach((param) => {
          stream.write(`    ${param}: string | number\n`)
        })
        stream.write('  }\n')
      }
      else { stream.write(`  '${route.name}': never\n`) }
    })
    stream.write('}\n')
  }

  private setRouteType(stream: WriteStream) {
    stream.write('export interface RouteType {\n')
    stream.write('  name: TypedRouteList\n')
    stream.write('  params?: TypedRouteParams[TypedRouteList]\n')
    stream.write('  query?: Record<string, string | number | boolean>\n')
    stream.write('  hash?: string\n')
    stream.write('}\n')
  }
}
