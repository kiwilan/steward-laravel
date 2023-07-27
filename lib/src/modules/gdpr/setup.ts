import 'vanilla-cookieconsent/dist/cookieconsent.css'
import { languages, matomo } from './config/index.js'
import type { CookieConsentType } from './alpine.js'

/**
 * Setup the GDPR module
 */
async function setup() {
  let cookie = {} as CookieConsentType
  try {
    cookie = await import('vanilla-cookieconsent')
  }
  catch (error) {
    return
  }

  cookie.run({
    onConsent() {
      matomo()
    },

    onChange() {
      matomo()
      setTimeout(() => {
        location.reload()
      }, 500)
    },

    cookie: {
      name: 'cc_cookie',
      domain: window.location.hostname,
      path: '/',
      expiresAfterDays: 182,
      sameSite: 'Lax',
    },

    categories: {
      necessary: {
        enabled: true, // this category is enabled by default
        readOnly: true, // this category cannot be disabled
      },
      analytics: {
        enabled: true,
        readOnly: false,

        // Delete specific cookies when the user opts-out of this category
        autoClear: {
          cookies: [
            {
              name: /^_pk/,
            },
            {
              name: /^_ga/, // regex: match all cookies starting with '_ga'
            },
            {
              name: '_gid', // string: exact cookie name
            },
          ],
        },
      },
    },

    guiOptions: {
      consentModal: {
        layout: 'box',
        position: 'bottom right',
        flipButtons: true,
        equalWeightButtons: false,
      },
      preferencesModal: {
        layout: 'box',
        position: 'right',
        flipButtons: true,
        equalWeightButtons: false,
      },
    },

    language: {
      default: 'en',
      autoDetect: 'document',

      translations: {
        ...languages,
      },
    },
  })
}

export default setup
