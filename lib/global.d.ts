import type { Alpine as AlpineType } from 'alpinejs'

export { }

/**
 * From https://bobbyhadz.com/blog/typescript-make-types-global
 */
declare global {
  const Alpine: AlpineType
  interface Window {
    Alpine: AlpineType
    app: {
      url: string
      urlStorage: string
    }
    _paq: any
    consentCookie: string
    matomo: {
      url: string
      id: string
      https: boolean
    }
  }
}
