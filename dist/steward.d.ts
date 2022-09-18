import { Plugin } from 'vite';

interface Options {
    /**
     * Where JS files will be copied
     * @default './public/vendor/js'
     */
    outputDir?: string;
}
declare function plugin(userOptions?: Options): Plugin;

export { plugin as steward };
