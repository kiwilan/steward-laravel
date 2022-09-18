const r = {}, n = {
  outputDir: "./public/js"
};
function a(s = {}) {
  return {
    name: "vite-plugin-markdoc-content",
    async buildStart() {
      const o = Object.assign({}, n, s), c = "vendor/kiwilan/laravel-steward/resources/js/color-mode.js", e = `${process.cwd()}/${c}`;
      await r.promises.mkdir(o.outputDir, { recursive: !0 }).catch(console.error), r.copyFile(e, `${o.outputDir}/color-mode.js`, (t) => {
        if (t)
          throw t;
      });
    }
  };
}
export {
  a as colorMode
};
