# @kiwilan/steward-laravel

Refer to [steward-laravel](https://github.com/kiwilan/steward-laravel).

## Installation

```bash
npm install @kiwilan/steward-laravel --save-dev
```

Or

```bash
pnpm add @kiwilan/steward-laravel -D
```

## Usage

In your `vite.config.js`:

```js
import { defineConfig } from "vite";
import { Steward } from "@kiwilan/steward-laravel";

export default defineConfig({
  plugins: [
    Steward({
      // Options
    }),
  ],
});
```
