function getCookie(name: string) {
  const nameEQ = `${name}=`
  const ca = document.cookie.split(';')
  for (let i = 0; i < ca.length; i++) {
    let c = ca[i]
    while (c.charAt(0) === ' ') c = c.substring(1, c.length)
    if (c.indexOf(nameEQ) === 0) {
      const content = c.substring(nameEQ.length, c.length)
      return JSON.parse(decodeURIComponent(content))
    }
  }

  return undefined
}

function deleteCookies(name: string) {
  const deleteCookie = (name: string) => {
    document.cookie = `${name}=; expires=${(new Date(0)).toUTCString()};`
  }
  const findCookies = (name: string) => {
    const r: string[] = []
    // @ts-expect-error - this is a custom function
    document.cookie.replace(new RegExp(`(${name}[^= ]*) *(?=\=)`, 'g'), (a, b, ix) => {
      if (/[ ;]/.test(document.cookie.substring(ix - 1, 1)))
        r.push(a.trim())
    })
    return r
  }

  findCookies(name).forEach((fullName) => {
    deleteCookie(fullName)
  })
}

function matomo() {
  // const consent = getCookie(window.consentCookie)
  // const domain = window.matomo.url
  // const https = window.matomo.https
  // const url = `${https ? 'https' : 'http'}://${domain}/`
  // const id = window.matomo.id
  // @ts-expect-error - global variable
  console.log(appEnv);
  let consent = getCookie('cc_cookie')
  let domain = 'matomo.git-projects.xyz'
  let https = true
  let url = `${https ? 'https' : 'http'}://${domain}/`
  let id = 2

  if (consent && consent.categories && consent.categories.includes('analytics')) {
    const _paq = window._paq = window._paq || []
    /* tracker methods like "setCustomDimension" should be called before "trackPageView" */
    _paq.push(['trackPageView'])
    _paq.push(['enableLinkTracking']);
    (function () {
      const u = `${url}`
      _paq.push(['setTrackerUrl', `${u}matomo.php`])
      _paq.push(['setSiteId', `${id}`])
      const d = document
      const g = d.createElement('script')
      const s = d.getElementsByTagName('script')[0]
      g.async = true
      g.src = `${u}matomo.js`
      s.parentNode?.insertBefore(g, s)
    })()
  }
  // @ts-expect-error - this is a custom function
  else if (typeof ConsentCookie !== 'undefined') {
    deleteCookies('_pk')
  }
}

export {
  matomo,
}
