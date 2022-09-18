"use strict";
var __create = Object.create;
var __defProp = Object.defineProperty;
var __getOwnPropDesc = Object.getOwnPropertyDescriptor;
var __getOwnPropNames = Object.getOwnPropertyNames;
var __getProtoOf = Object.getPrototypeOf;
var __hasOwnProp = Object.prototype.hasOwnProperty;
var __export = (target, all) => {
  for (var name in all)
    __defProp(target, name, { get: all[name], enumerable: true });
};
var __copyProps = (to, from, except, desc) => {
  if (from && typeof from === "object" || typeof from === "function") {
    for (let key of __getOwnPropNames(from))
      if (!__hasOwnProp.call(to, key) && key !== except)
        __defProp(to, key, { get: () => from[key], enumerable: !(desc = __getOwnPropDesc(from, key)) || desc.enumerable });
  }
  return to;
};
var __toESM = (mod, isNodeMode, target) => (target = mod != null ? __create(__getProtoOf(mod)) : {}, __copyProps(
  isNodeMode || !mod || !mod.__esModule ? __defProp(target, "default", { value: mod, enumerable: true }) : target,
  mod
));
var __toCommonJS = (mod) => __copyProps(__defProp({}, "__esModule", { value: true }), mod);

// lib/vite-steward.ts
var vite_steward_exports = {};
__export(vite_steward_exports, {
  steward: () => vite_steward_lib_default
});
module.exports = __toCommonJS(vite_steward_exports);

// lib/vite-steward-lib/index.ts
var import_fs = __toESM(require("fs"), 1);
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
      await import_fs.default.promises.mkdir(outputDirScripts, { recursive: true }).catch(console.error);
      await import_fs.default.promises.mkdir(outputDirLibraries, { recursive: true }).catch(console.error);
      for (const file of filesToCopy) {
        const outputDir = file.library ? outputDirLibraries : outputDirScripts;
        import_fs.default.copyFile(`${path}/${file.path}`, `${outputDir}/${file.name}`, (err) => {
          if (err)
            throw err;
        });
      }
    }
  };
}
var vite_steward_lib_default = plugin;
// Annotate the CommonJS export names for ESM import in node:
0 && (module.exports = {
  steward
});
