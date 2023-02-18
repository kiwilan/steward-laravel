declare namespace Route {
  export type List = {
    'login': { 'uri': 'login', 'methods': ["GET","HEAD"] }
    'logout': { 'uri': 'logout', 'methods': ["POST"] }
  };

  export type Name = 'login' | 'logout' | 'story';
  export type Uri = '/login' | '/logout' | '/story/{story}';
  export type Params = {
    'login': never,
    'logout': never,
    'story': {
      'story': string
    },
  };

  export interface Type {
    name: Route.Name
    params?: Route.Params[Route.Name]
    query?: Record<string, string | number | boolean>
    hash?: string
  }

  export type Method = 'GET' | 'POST' | 'PUT' | 'PATCH' | 'DELETE';
  export interface Entity { name: Route.Name; path: Route.Uri; params?: Route.Params[Route.Name],  method: Route.Method; }
}

declare namespace Inertia {
  export interface Page<T> {
    component: string;
    props: T;
    url: string;
    version: string;
    scrollRegions: string[];
    rememberedState: Record<string, unknown>;
    resolvedErrors: Record<string, unknown>;
  }
  export interface PageProps {
    user: any
    jetstream?: any
    [x: string]: unknown;
    errors: any;
  }
}


