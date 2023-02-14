# @kiwilan/vite-plugin-steward-laravel

Refer to [steward-laravel](https://github.com/kiwilan/steward-laravel).

## Installation

[Typescriptable Laravel](https://github.com/kiwilan/typescriptable-laravel) is required.

```bash
npm install @kiwilan/vite-plugin-steward-laravel --save-dev
```

Or

```bash
pnpm add @kiwilan/vite-plugin-steward-laravel -D
```

## Usage

In your `vite.config.js`:

```js
import { defineConfig } from "vite";
import { Steward } from "@kiwilan/vite-plugin-steward-laravel";

export default defineConfig({
  plugins: [
    Steward({
      // Options
    }),
  ],
});
```

### Inertia

In your `resources/js/app.ts`, load `InertiaTyped` Vue plugin. You could use some helper like `appResolve` and `appTitle` to setup Inertia.

```ts
import "./bootstrap";
import "../css/app.css";

import { Head, Link, createInertiaApp, router } from "@inertiajs/vue3";
import {
  InertiaTyped,
  appResolve,
  appTitle,
} from "@kiwilan/vite-plugin-steward-laravel/vue";

createInertiaApp({
  title: (title) => appTitle(title),
  resolve: (name) =>
    appResolve(name, import.meta.glob("./Pages/**/*.vue", { eager: true })),
  setup({ el, App, props, plugin }) {
    const app = createApp({ render: () => h(App, props) })
      .use(plugin)
      .component("Head", Head)
      .component("InertiaLink", Link)
      .use(InertiaTyped, { router });

    app.mount(el);
  },
});
```

In Vue component, you could use `useInertia` to get useful Inertia methods.

```vue
<script setup lang="ts">
import { useInertia } from "@kiwilan/vite-plugin-steward-laravel/vue";

const { router, route, isRoute, currentRoute } = useInertia();
</script>
```

You have access to global methods injected into templates.

```vue
<template>
  <div>
    <h1>Current route is: {{ $currentRoute }}</h1>
    <InertiaLink
      :href="$route('typed-laravel-route')"
      :class="{{'bg-gray-100': $isRoute('typed-laravel-route')}}"
    >
      A Link
    </InertiaLink>
    <InertiaLink :href="$route('typed-laravel-route', { slug: 'model-slug' })">
      Another Link
    </InertiaLink>
  </div>
</template>
```
