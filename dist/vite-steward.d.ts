import { Plugin } from 'vite';

interface Options {
    /**
     * Where JS scripts will be copied
     * @default './public/vendor/js'
     */
    outputDirScripts?: string;
    /**
     * Where JS libraries will be copied
     * @default './resources/js'
     */
    outputDirLibraries?: string;
}
declare function plugin(userOptions?: Options): Plugin;

export { plugin as ViteSteward };
