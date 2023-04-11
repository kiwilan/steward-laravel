import type { PropType } from 'vue';
type RequestPayload = Record<string, any>;
type Method = 'get' | 'post' | 'put' | 'patch' | 'delete';
interface Attrs {
    onCancelToken?: (cancel: () => void) => void;
    onBefore?: (visit: any) => void;
    onStart?: (visit: any) => void;
    onProgress?: (event: any) => void;
    onFinish?: (visit: any) => void;
    onCancel?: (visit: any) => void;
    onSuccess?: (page: any) => void;
    onError?: (error: any) => void;
}
declare const _sfc_main: import("vue").DefineComponent<{
    to: PropType<Route.Type>;
    data: PropType<RequestPayload>;
    as: {
        type: StringConstructor;
        default: string;
    };
    queryStringArrayFormat: {
        type: PropType<"brackets" | "indices">;
        default: string;
    };
    method: {
        type: PropType<Method>;
        default: string;
    };
    replace: {
        type: BooleanConstructor;
        default: boolean;
    };
    preserveScroll: {
        type: BooleanConstructor;
        default: boolean;
    };
    preserveState: {
        type: BooleanConstructor;
        default: null;
    };
    only: {
        type: {
            (arrayLength: number): string[];
            (...items: string[]): string[];
            new (arrayLength: number): string[];
            new (...items: string[]): string[];
            isArray(arg: any): arg is any[];
            readonly prototype: any[];
            from<T>(arrayLike: ArrayLike<T>): T[];
            from<T_1, U>(arrayLike: ArrayLike<T_1>, mapfn: (v: T_1, k: number) => U, thisArg?: any): U[];
            from<T_2>(iterable: Iterable<T_2> | ArrayLike<T_2>): T_2[];
            from<T_3, U_1>(iterable: Iterable<T_3> | ArrayLike<T_3>, mapfn: (v: T_3, k: number) => U_1, thisArg?: any): U_1[];
            of<T_4>(...items: T_4[]): T_4[];
            readonly [Symbol.species]: ArrayConstructor;
        };
        default: () => never[];
    };
    headers: {
        type: ObjectConstructor;
        default: () => {};
    };
}, {
    props: any;
    route: (route: Route.Type) => string;
    ifetch: {
        get: (route: Route.Type, data?: {
            [x: string]: any;
        } | undefined) => void;
        post: (route: Route.Type, data?: {
            [x: string]: any;
        } | undefined) => void;
        patch: (route: Route.Type, data?: {
            [x: string]: any;
        } | undefined) => void;
        put: (route: Route.Type, data?: {
            [x: string]: any;
        } | undefined) => void;
        delete: (route: Route.Type) => void;
    };
    as: string;
    method: Method;
    routeStr: import("vue").ComputedRef<string>;
    dataRaw: import("vue").ComputedRef<RequestPayload>;
    attrs: Attrs;
    href: string;
    data: Record<string, import("@inertiajs/core").FormDataConvertible>;
    pushTo: (event: any) => void;
}, unknown, {}, {}, import("vue").ComponentOptionsMixin, import("vue").ComponentOptionsMixin, {}, string, import("vue").VNodeProps & import("vue").AllowedComponentProps & import("vue").ComponentCustomProps, Readonly<import("vue").ExtractPropTypes<{
    to: PropType<Route.Type>;
    data: PropType<RequestPayload>;
    as: {
        type: StringConstructor;
        default: string;
    };
    queryStringArrayFormat: {
        type: PropType<"brackets" | "indices">;
        default: string;
    };
    method: {
        type: PropType<Method>;
        default: string;
    };
    replace: {
        type: BooleanConstructor;
        default: boolean;
    };
    preserveScroll: {
        type: BooleanConstructor;
        default: boolean;
    };
    preserveState: {
        type: BooleanConstructor;
        default: null;
    };
    only: {
        type: {
            (arrayLength: number): string[];
            (...items: string[]): string[];
            new (arrayLength: number): string[];
            new (...items: string[]): string[];
            isArray(arg: any): arg is any[];
            readonly prototype: any[];
            from<T>(arrayLike: ArrayLike<T>): T[];
            from<T_1, U>(arrayLike: ArrayLike<T_1>, mapfn: (v: T_1, k: number) => U, thisArg?: any): U[];
            from<T_2>(iterable: Iterable<T_2> | ArrayLike<T_2>): T_2[];
            from<T_3, U_1>(iterable: Iterable<T_3> | ArrayLike<T_3>, mapfn: (v: T_3, k: number) => U_1, thisArg?: any): U_1[];
            of<T_4>(...items: T_4[]): T_4[];
            readonly [Symbol.species]: ArrayConstructor;
        };
        default: () => never[];
    };
    headers: {
        type: ObjectConstructor;
        default: () => {};
    };
}>>, {
    as: string;
    queryStringArrayFormat: "brackets" | "indices";
    method: Method;
    replace: boolean;
    preserveScroll: boolean;
    preserveState: boolean;
    only: string[];
    headers: Record<string, any>;
}>;
export default _sfc_main;
