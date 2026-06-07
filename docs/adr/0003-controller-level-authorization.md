# Keep Authorization Checks In Controllers

Laravel Form Requests in the backend should validate request payloads, while controllers should call authorization policies explicitly before executing protected actions.

Policies remain the source of authorization rules for each resource. Controllers are responsible for invoking those policies with `$this->authorize(...)`, so opening a controller shows the public API action, the authorization check, and the mutation/query in one place.

Nested route ownership is handled separately from authorization. Scoped implicit bindings make invalid parent/child route combinations return `404 Not Found`, while policy denials for resources that exist but do not belong to the authenticated user continue to return `403 Forbidden`.

## Considered Options

- Put authorization in Form Request `authorize()` methods for actions that have request payloads.
- Put authorization in controllers for every protected controller action.
- Use route middleware or attributes for policy checks.

We chose controller-level authorization because this backend's API controllers are small and resource-oriented. Keeping every `$this->authorize(...)` call in the controller makes authorization easy to audit across `index`, `store`, `show`, `update`, `destroy`, and custom actions such as Sector reorder.

## Consequences

- Form Requests should normally return `true` from `authorize()` and focus on validation rules.
- Controllers should call `$this->authorize(...)` before creating, reading, updating, deleting, or reordering protected resources.
- Policies should be defined per resource when the resource has its own controller surface, even when the rule delegates to an aggregate root such as Template.
- Create actions that do not yet have a model instance should pass parent context to the policy, for example `[Sector::class, $template]` or `[Product::class, $sector]`.
- Scoped implicit bindings should be used for nested resources so parent/child mismatches are treated as invalid routes.
- Existing ADR 0002 still applies to authorization failures: when a resource is resolved but the authenticated user is not allowed to operate it, the API returns `403 Forbidden`.
