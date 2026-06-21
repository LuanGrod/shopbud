import { z } from "zod";
import type { AuthFieldErrors } from "./auth-types";
import { isAuthFieldName } from "./isAuthFieldName";

export function getZodFieldErrors(error: z.ZodError): AuthFieldErrors {
    return error.issues.reduce<AuthFieldErrors>((errors, issue) => {
        const [field] = issue.path;

        if (
            typeof field !== "string" ||
            !isAuthFieldName(field) ||
            errors[field]
        ) {
            return errors;
        }

        errors[field] = issue.message;
        return errors;
    }, {});
}
