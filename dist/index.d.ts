import { Plugin } from 'vite';
import { Plugin as Plugin$1 } from 'vue';
import { RouteParamsWithQueryOverload, RouteParam, Config } from 'ziggy-js';

interface Options {
}
declare function plugin(options?: Options): Plugin;

type Route = keyof ZiggyLaravelRoutes;
type RequestPayload = Record<string, any>;
interface IInertiaTyped {
    route: (name: Route, params?: RouteParamsWithQueryOverload | RouteParam, absolute?: boolean, customZiggy?: Config) => string;
    isRoute: (name: Route, params?: RouteParamsWithQueryOverload) => boolean;
    currentRoute: () => string;
    router: {
        get: (url: Route, data?: RequestPayload) => Promise<any>;
        post: (url: Route, data?: RequestPayload) => Promise<any>;
        patch: (url: Route, data?: RequestPayload) => Promise<any>;
        put: (url: Route, data?: RequestPayload) => Promise<any>;
        delete: (url: Route) => Promise<any>;
    };
}
interface PluginOptions {
    inject: boolean;
}
declare const InertiaTyped: Plugin$1;

declare const log: (...args: any[]) => void;

export { IInertiaTyped, InertiaTyped, PluginOptions, plugin as Ziggy, log };
