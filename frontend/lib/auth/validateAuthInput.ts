import { z } from "zod";
import type { AuthFieldErrors, AuthInput, AuthMode } from "./auth-types";
import { getZodFieldErrors } from "./getZodFieldErrors";

const loginSchema = z.object({
    email: z
        .string()
        .trim()
        .min(1, "Informe seu e-mail.")
        .email("Informe um e-mail válido."),
    password: z
        .string()
        .min(1, "Informe sua senha.")
        .min(8, "A senha precisa ter pelo menos 8 caracteres."),
});

const registerSchema = loginSchema
    .extend({
        name: z
            .string()
            .trim()
            .min(1, "Informe seu nome.")
            .max(255, "O nome deve ter no máximo 255 caracteres."),
        password_confirmation: z.string().min(1, "Confirme sua senha."),
    })
    .refine((input) => input.password === input.password_confirmation, {
        path: ["password_confirmation"],
        message: "As senhas precisam ser iguais.",
    });

export function validateAuthInput(
    mode: AuthMode,
    input: AuthInput,
): AuthFieldErrors {
    const result =
        mode === "login"
            ? loginSchema.safeParse(input)
            : registerSchema.safeParse(input);

    if (result.success) {
        return {};
    }

    return getZodFieldErrors(result.error);
}
