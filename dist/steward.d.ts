interface Options {
    /**
     * Where `color-mode.js` will be copied
     * @default './public/js'
     */
    outputDir?: string;
}
declare function plugin(userOptions?: Options): {
    name: string;
    buildStart(): Promise<void>;
};

export { plugin as colorMode };
