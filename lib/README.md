# @kiwilan/vite-plugin-steward-laravel

Refer to [steward-laravel](https://github.com/kiwilan/steward-laravel).

## Installation

```bash
npm install @kiwilan/vite-plugin-steward-laravel --save-dev
```

```bash
pnpm add @kiwilan/vite-plugin-steward-laravel -D
```

## Usage

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
