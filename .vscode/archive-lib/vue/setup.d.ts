import type { DefineComponent } from 'vue';
type Page = Promise<DefineComponent>;
/**
 * Resolve `createInertiaApp`.
 *
 * @example
 * createInertiaApp({
 *   resolve: name => appResolve(name, import.meta.globEager('./Pages/*.vue'))
 * })
 */
declare const appResolve: (name: string, glob: Record<string, unknown>) => Page;
/**
 * Title for `createInertiaApp`.
 *
 * @example
 * createInertiaApp({
 *   title: (title) => appTitle(title, 'Override Title')
 * })
 */
declare const appTitle: (title: string, overrideTitle?: string) => string;
export { appResolve, appTitle, };
