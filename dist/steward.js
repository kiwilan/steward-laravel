// lib/vite-color-mode.ts
import * as fs from "fs";
var DEFAULT_OPTIONS = {
  outputDir: "./public/js"
};
function plugin(userOptions = {}) {
  return {
    name: "vite-plugin-markdoc-content",
    async buildStart() {
      const opts = Object.assign({}, DEFAULT_OPTIONS, userOptions);
      const pathColorMode = "vendor/kiwilan/laravel-steward/resources/js/color-mode.js";
      const path = process.cwd();
      const fullPath = `${path}/${pathColorMode}`;
      await fs.promises.mkdir(opts.outputDir, { recursive: true }).catch(console.error);
      fs.copyFile(fullPath, `${opts.outputDir}/color-mode.js`, (err) => {
        if (err)
          throw err;
      });
    }
  };
}
var vite_color_mode_default = plugin;
export {
  vite_color_mode_default as colorMode
};
