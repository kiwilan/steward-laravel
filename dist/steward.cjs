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

// lib/index.ts
var lib_exports = {};
__export(lib_exports, {
  steward: () => vite_steward_default
});
module.exports = __toCommonJS(lib_exports);

// lib/vite-steward.ts
var import_fs = __toESM(require("fs"), 1);
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
      await import_fs.default.promises.mkdir(opts.outputDir ?? outputDir, { recursive: true }).catch(console.error);
      for (const file of filesToCopy) {
        await import_fs.default.promises.copyFile(`${path}/${file.path}`, `${opts.outputDir ?? outputDir}/${file.name}`).catch(console.error);
      }
    }
  };
}
var vite_steward_default = plugin;
// Annotate the CommonJS export names for ESM import in node:
0 && (module.exports = {
  steward
});
