type RequestPayload = Record<string, any>;
export declare const useInertia: () => {
    ifetch: {
        get: (route: Route.Type, data?: RequestPayload) => void;
        post: (route: Route.Type, data?: RequestPayload) => void;
        patch: (route: Route.Type, data?: RequestPayload) => void;
        put: (route: Route.Type, data?: RequestPayload) => void;
        delete: (route: Route.Type) => void;
    };
    route: (route: Route.Type) => string;
    isRoute: (route: Route.Name) => boolean;
    currentRoute: () => Route.Entity | undefined;
    page: import("@inertiajs/core").Page<Inertia.PageProps>;
};
export {};
