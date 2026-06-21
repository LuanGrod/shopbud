# Store Frontend Session In HttpOnly Cookie And Use A Thin BFF

The frontend should store the authenticated session token in a `HttpOnly` cookie owned by the Next.js app, and protected frontend data calls should go through a thin Next.js Backend-for-Frontend layer that forwards requests to the Laravel API with the Sanctum bearer token.

Laravel remains the source of truth for authentication, authorization, resource ownership, validation, and domain rules. Next.js owns the browser session boundary: route protection, login/register session cookie creation, logout cookie deletion, and translating the secure cookie into the `Authorization: Bearer <token>` header expected by Laravel.

This lets the Next.js `proxy.ts` file perform route redirects before protected pages render, while keeping the token unavailable to client-side JavaScript.

## Considered Options

- Store the Sanctum token in `localStorage` and call Laravel directly from the browser.
- Store the Sanctum token in a JavaScript-readable cookie and call Laravel directly from the browser.
- Configure Laravel Sanctum for first-party SPA cookie authentication and call Laravel directly from the browser.
- Store the Sanctum token in a Next.js `HttpOnly` cookie and call Laravel through a thin Next.js BFF/proxy layer.

We chose the thin BFF approach because the backend already exposes Sanctum bearer-token APIs, while the frontend needs Next.js `proxy.ts` route protection and a session token that is not readable by JavaScript. A `HttpOnly` cookie gives the server-side Next.js runtime access to the session without exposing the token to React components, TanStack Query hooks, browser extensions, or XSS payloads.

## Consequences

- Login and registration should call a Next.js route handler or server action, which calls Laravel and sets the `shopbud_token` cookie with `HttpOnly`, `Secure` in production, `SameSite=Lax`, and `Path=/`.
- Logout should call a Next.js route handler or server action that forwards logout to Laravel when possible, then deletes the `shopbud_token` cookie.
- Next.js `proxy.ts` should use the cookie as an optimistic route guard for protected app routes and redirect unauthenticated users to `/login`.
- Protected browser data calls should target Next.js endpoints, for example `/api/backend/templates`, instead of calling Laravel directly.
- The BFF layer should remain thin: it forwards requests, attaches `Authorization: Bearer <token>`, preserves Laravel status codes where practical, and does not duplicate domain validation or authorization rules.
- TanStack Query can still manage client-side cache, refresh, retries, invalidation, and optimistic updates; its query and mutation functions should fetch the Next.js BFF endpoints.
- Laravel policies, controllers, Form Requests, rate limits, and Sanctum remain the secure enforcement layer for API data access. The Next.js proxy is only a navigation/session guard and must not be treated as the only security boundary.
- If the project later switches Laravel to first-party SPA cookie authentication, this decision can be revisited to remove or reduce the BFF forwarding layer.
