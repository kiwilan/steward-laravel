(function (exports, child_process) {
  'use strict';

  // resources/js/vite-plugin-ziggy.ts
  var DEFAULT_OPTIONS = {};
  function plugin(options = {}) {
    return {
      name: "vite-plugin-ziggy",
      buildStart() {
        Object.assign({}, DEFAULT_OPTIONS, options);
        const command = (command2) => child_process.exec(
          command2,
          (error, stdout) => {
            if (error) {
              console.error(`exec error: ${error}`);
              return;
            }
            console.log(`${command2} ready!`);
          }
        );
        command("php artisan ziggy:generate");
        command("php artisan generate:type models");
        command("php artisan generate:type ziggy");
      },
      handleHotUpdate({ file, server }) {
        if (file.endsWith(".md"))
          server.restart();
      }
    };
  }
  var vite_plugin_ziggy_default = plugin;

  // resources/js/index.ts
  var log = (...args) => {
    console.log(...args);
  };

  exports.Ziggy = vite_plugin_ziggy_default;
  exports.log = log;

  return exports;

})({}, child_process);
