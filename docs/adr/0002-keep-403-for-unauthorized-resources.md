# Keep 403 For Unauthorized Resources

When an authenticated user requests a resource that exists but is not theirs, the API will keep returning `403 Forbidden` through Laravel authorization policies instead of disguising the resource as `404 Not Found`. This accepts the small information-disclosure tradeoff because the current protection priorities are authentication, policy authorization, validation, and rate limiting, and because changing all authorization failures to 404 would add behavior that is easy to misunderstand without a stronger compliance requirement.

## Considered Options

- Return `403 Forbidden` for resources the user is not authorized to access.
- Convert authorization failures to `404 Not Found` to reduce resource enumeration signals.
- Replace public-facing sequential IDs with UUIDs, ULIDs, or hash IDs.

We chose to keep `403 Forbidden` for now. The API already uses Sanctum authentication, policy authorization, and global rate limiting; returning 404 would not remove enumeration risk entirely while sequential IDs remain in use.

## Consequences

- Unauthorized access attempts may reveal that a resource exists.
- This is considered acceptable for the current product stage.
- If compliance or threat modeling later requires stronger enumeration resistance, revisit this decision alongside public identifier strategy.
