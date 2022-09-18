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
  colorMode: () => vite_color_mode_default
});
module.exports = __toCommonJS(lib_exports);

// lib/vite-color-mode.ts
var fs = __toESM(require("fs"), 1);
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
// Annotate the CommonJS export names for ESM import in node:
0 && (module.exports = {
  colorMode
});
