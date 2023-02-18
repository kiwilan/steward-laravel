export declare const useInertia: () => {
    ifetch: {
        get: (route: Route.Type, data?: any) => void;
        post: (route: Route.Type, data?: any) => void;
        patch: (route: Route.Type, data?: any) => void;
        put: (route: Route.Type, data?: any) => void;
        delete: (route: Route.Type) => void;
    };
    route: (route: Route.Type) => any;
    isRoute: (route: Route.Name) => boolean;
    currentRoute: () => Route.Entity | undefined;
    page: import("@inertiajs/core").Page<Inertia.PageProps>;
};
