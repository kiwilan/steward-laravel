import { Plugin } from 'vite';

interface Options {
}
declare function plugin(options?: Options): Plugin;

declare const log: (...args: any[]) => void;

export { plugin as Ziggy, log };
