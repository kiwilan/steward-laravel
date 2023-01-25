## Local test

With `link`

```bash
cd lib
npm link
cd ..
cd test
pnpm link @kiwilan/vite-plugin-laravel-steward
```

With `tgz`

```bash
# shortcut
pnpm package
cd test && pnpm remove @kiwilan/vite-plugin-laravel-steward
pnpm add @kiwilan/vite-plugin-laravel-steward@file:~/kiwilan-vite-plugin-laravel-steward-0.0.136.tgz
cd ..

# manually
rm ~/kiwilan-vite-plugin-laravel-steward-0.0.*.tgz
cd lib
pnpm build
npm pack --pack-destination ~
cd ..
cd test
pnpm add @kiwilan/vite-plugin-laravel-steward@file:~/kiwilan-vite-plugin-laravel-steward-0.0.136.tgz
```

## Docs

- <https://blog.logrocket.com/managing-full-stack-monorepo-pnpm>
