import { Plugin } from 'vite';

interface StewardOptions {
    /**
     * Generate `ziggy.js` file, it's native ziggy feature.
     * @docs https://github.com/tighten/ziggy#advanced-setup
     *
     * @default false
     */
    ziggy?: boolean;
    /**
     * Enable types for Eloquent models.
     *
     * @default true
     */
    models?: boolean;
    /**
     * Enable types for Laravel Routes.
     *
     * @default true
     */
    routes?: boolean;
    /**
     * Enable types for Inertia.
     *
     * @default true
     */
    inertia?: boolean;
    /**
     * Enable Vite autoreload on PHP files changes.
     *
     * @default {
     *  models: true,
     *  controllers: true,
     *  routes: true,
     * }
     */
    autoreload?: {
        models?: boolean;
        controllers?: boolean;
        routes?: boolean;
    } | false;
}

declare const Steward: (userOptions?: StewardOptions) => Plugin;

export { Steward, StewardOptions };
