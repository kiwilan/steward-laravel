import { defineAppConfig } from '#app'

export default defineAppConfig({
  docus: {
    title: 'Laravel Steward',
    description: 'Laravel package with utility classes.',
    image: '/cover.jpg',
    socials: {
      twitter: 'ewilanriviere',
      github: 'kiwilan/laravel-steward',
      nuxt: {
        href: 'https://laravel.com',
        icon: 'simple-icons:laravel',
        label: 'Laravel',
      },
    },
    aside: {
      level: 0,
    },
    header: {
      logo: true,
      showLinkIcon: true,
      exclude: [],
    },
    footer: {
      credits: {
        text: 'Powered by Docus, made with ❤️ by Kiwilan',
        href: 'https://github.com/kiwilan/nuxt-svg-transformer',
      },
    },
  },
})
