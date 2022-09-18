// lib/vite-steward.ts
import fs from "fs";
var outputDirScriptsDefault = "./public/vendor/js";
var outputDirLibrariesDefault = "./resources/libs";
var DEFAULT_OPTIONS = {
  outputDirScripts: outputDirScriptsDefault,
  outputDirLibraries: outputDirLibrariesDefault
};
function plugin(userOptions = {}) {
  return {
    name: "vite-plugin-markdoc-content",
    async buildStart() {
      const opts = Object.assign({}, DEFAULT_OPTIONS, userOptions);
      const outputDirScripts = opts.outputDirScripts;
      const outputDirLibraries = opts.outputDirLibraries;
      const filesToCopy = [
        {
          name: "color-mode.js",
          path: "resources/js/color-mode.js",
          library: false
        },
        {
          name: "tiptap.js",
          path: "dist/tiptap.cjs",
          library: true
        }
      ];
      const path = `${process.cwd()}/vendor/kiwilan/laravel-steward`;
      await fs.promises.mkdir(outputDirScripts, { recursive: true }).catch(console.error);
      await fs.promises.mkdir(outputDirLibraries, { recursive: true }).catch(console.error);
      for (const file of filesToCopy) {
        const outputDir = file.library ? outputDirLibraries : outputDirScripts;
        fs.copyFile(`${path}/${file.path}`, `${outputDir}/${file.name}`, (err) => {
          if (err)
            throw err;
        });
      }
    }
  };
}
var vite_steward_default = plugin;
export {
  vite_steward_default as default
};
