export default defineAppConfig({
  docus: {
    title: 'Steward',
    description: 'Laravel package with utility classes.',
    image: 'https://steward-laravel.netlify.app/cover.jpg',
    socials: {
      twitter: 'ewilanriviere',
      github: 'kiwilan/steward-laravel',
    },
    github: {
      dir: '.starters/default/content',
      branch: 'main',
      repo: 'docus',
      owner: 'nuxt-themes',
      edit: true
    },
    aside: {
      level: 0,
      collapsed: false,
      exclude: []
    },
    main: {
      padded: true,
      fluid: true
    },
    header: {
      logo: true,
      showLinkIcon: true,
      exclude: [],
      fluid: true
    }
  }
})
