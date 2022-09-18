// lib/vite-steward.ts
import fs from "fs";
var outputDir = "./public/vendor/js";
var DEFAULT_OPTIONS = {
  outputDir
};
function plugin(userOptions = {}) {
  return {
    name: "vite-plugin-markdoc-content",
    async buildStart() {
      const opts = Object.assign({}, DEFAULT_OPTIONS, userOptions);
      const filesToCopy = [
        {
          name: "color-mode.js",
          path: "vendor/kiwilan/laravel-steward/resources/js/color-mode.js"
        },
        {
          name: "tiptap.js",
          path: "vendor/kiwilan/laravel-steward/dist/tiptap.cjs"
        }
      ];
      const path = process.cwd();
      await fs.promises.mkdir(opts.outputDir ?? outputDir, { recursive: true }).catch(console.error);
      for (const file of filesToCopy) {
        await fs.promises.copyFile(`${path}/${file.path}`, `${opts.outputDir ?? outputDir}/${file.name}`).catch(console.error);
      }
    }
  };
}
var vite_steward_default = plugin;
export {
  vite_steward_default as steward
};
