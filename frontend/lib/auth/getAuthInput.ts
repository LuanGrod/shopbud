import type { AuthInput } from "./auth-types";

export function getAuthInput(formData: FormData): AuthInput {
    return {
        name: String(formData.get("name") ?? ""),
        email: String(formData.get("email") ?? ""),
        password: String(formData.get("password") ?? ""),
        password_confirmation: String(
            formData.get("password_confirmation") ?? "",
        ),
    };
}
