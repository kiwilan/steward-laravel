## Local test

With `link`

```bash
cd lib
npm link
cd ..
cd test
pnpm link @kiwilan/vite-plugin-steward-laravel
```

With `tgz`

```bash
# shortcut
pnpm package
cd test && pnpm remove @kiwilan/vite-plugin-steward-laravel
pnpm add @kiwilan/vite-plugin-steward-laravel@file:~/kiwilan-vite-plugin-steward-laravel-0.0.138.tgz
cd ..

# manually
rm ~/kiwilan-vite-plugin-steward-laravel-0.0.*.tgz
cd lib
pnpm build
npm pack --pack-destination ~
cd ..
cd test
pnpm add @kiwilan/vite-plugin-steward-laravel@file:~/kiwilan-vite-plugin-steward-laravel-0.0.136.tgz
```

## Docs

- <https://blog.logrocket.com/managing-full-stack-monorepo-pnpm>
