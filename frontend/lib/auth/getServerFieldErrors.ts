import type { AuthFieldErrors } from "./auth-types";
import { isAuthFieldName } from "./isAuthFieldName";

export function getServerFieldErrors(data: unknown): AuthFieldErrors {
    if (!data || typeof data !== "object") {
        return {};
    }

    if (
        !("errors" in data) ||
        !data.errors ||
        typeof data.errors !== "object"
    ) {
        return {};
    }

    return Object.entries(data.errors).reduce<AuthFieldErrors>(
        (errors, [field, messages]) => {
            if (!isAuthFieldName(field) || !Array.isArray(messages)) {
                return errors;
            }

            const [firstMessage] = messages;
            if (typeof firstMessage === "string") {
                errors[field] = firstMessage;
            }

            return errors;
        },
        {},
    );
}
