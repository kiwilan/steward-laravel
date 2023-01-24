import { defineBuildConfig } from 'unbuild'

export default defineBuildConfig({
  name: 'laravel-steward',
  entries: [
    './resources/js/index',
  ],
  declaration: true,
  failOnWarn: false,
})
