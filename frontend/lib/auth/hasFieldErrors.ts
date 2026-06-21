import type { AuthFieldErrors } from "./auth-types";

export function hasFieldErrors(errors: AuthFieldErrors) {
    return Object.keys(errors).length > 0;
}
