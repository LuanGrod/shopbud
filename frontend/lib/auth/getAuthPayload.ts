import type { AuthInput, AuthMode } from "./auth-types";

export function getAuthPayload(mode: AuthMode, input: AuthInput) {
    if (mode === "login") {
        return {
            email: input.email.trim(),
            password: input.password,
        };
    }

    return {
        name: input.name.trim(),
        email: input.email.trim(),
        password: input.password,
        password_confirmation: input.password_confirmation,
    };
}
