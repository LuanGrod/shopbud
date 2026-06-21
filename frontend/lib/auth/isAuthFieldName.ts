import type { AuthFieldName } from "./auth-types";

export function isAuthFieldName(value: string): value is AuthFieldName {
    return (
        value === "name" ||
        value === "email" ||
        value === "password" ||
        value === "password_confirmation"
    );
}
