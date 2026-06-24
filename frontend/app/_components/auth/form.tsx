"use client";

import { getAuthErrorMessage } from "@/lib/auth/getAuthErrorMessage";
import { getAuthInput } from "@/lib/auth/getAuthInput";
import { getAuthPayload } from "@/lib/auth/getAuthPayload";
import { getSafeNextPath } from "@/lib/auth/getSafeNextPath";
import { getServerFieldErrors } from "@/lib/auth/getServerFieldErrors";
import { hasFieldErrors } from "@/lib/auth/hasFieldErrors";
import type {
    AuthFieldErrors,
    AuthFieldName,
    AuthMode,
} from "@/lib/auth/auth-types";
import { validateAuthInput } from "@/lib/auth/validateAuthInput";
import { readJsonResponse } from "@/lib/http/readJsonResponse";
import { useRouter } from "next/navigation";
import { FormEvent, useState } from "react";
import { toast } from "sonner";
import { AuthField } from "./field";
import {
    EnvelopeSimpleIcon,
    KeyholeIcon,
    LockKeyIcon,
    UserCirclePlusIcon,
    UserIcon,
    type Icon,
} from "@phosphor-icons/react";

type AuthFormProps = {
    initialMode?: AuthMode;
};

type AuthFieldConfig = {
    label: string;
    name: AuthFieldName;
    type: string;
    autoComplete: string;
    icon: Icon;
};

const fieldsByMode = {
    login: [
        {
            label: "E-mail",
            name: "email",
            type: "email",
            autoComplete: "email",
            icon: EnvelopeSimpleIcon,
        },
        {
            label: "Senha",
            name: "password",
            type: "password",
            autoComplete: "current-password",
            icon: LockKeyIcon,
        },
    ],
    register: [
        {
            label: "Nome",
            name: "name",
            type: "text",
            autoComplete: "name",
            icon: UserIcon,
        },
        {
            label: "E-mail",
            name: "email",
            type: "email",
            autoComplete: "email",
            icon: EnvelopeSimpleIcon,
        },
        {
            label: "Senha",
            name: "password",
            type: "password",
            autoComplete: "new-password",
            icon: LockKeyIcon,
        },
        {
            label: "Confirmar senha",
            name: "password_confirmation",
            type: "password",
            autoComplete: "new-password",
            icon: LockKeyIcon,
        },
    ],
} satisfies Record<AuthMode, AuthFieldConfig[]>;

const copyByMode = {
    login: {
        title: "Entrar no ShopBud",
        subtitle: "Seu companheiro de compras está pronto para ajudar.",
        submit: "Entrar",
        submitting: "Entrando...",
    },
    register: {
        title: "Criar sua conta",
        subtitle: "Comece a montar seus templates de supermercado.",
        submit: "Criar conta",
        submitting: "Criando conta...",
    },
};

export function AuthForm({ initialMode = "login" }: AuthFormProps) {
    const router = useRouter();
    const [mode, setMode] = useState<AuthMode>(initialMode);
    const [fieldErrors, setFieldErrors] = useState<AuthFieldErrors>({});
    const [isSubmitting, setIsSubmitting] = useState(false);
    const copy = copyByMode[mode];

    function selectMode(nextMode: AuthMode) {
        setFieldErrors({});
        setMode(nextMode);
    }

    async function handleSubmit(event: FormEvent<HTMLFormElement>) {
        event.preventDefault();
        setFieldErrors({});

        const input = getAuthInput(new FormData(event.currentTarget));
        const validationErrors = validateAuthInput(mode, input);

        if (hasFieldErrors(validationErrors)) {
            setFieldErrors(validationErrors);
            return;
        }

        setIsSubmitting(true);
        try {
            const response = await fetch(
                mode === "login" ? "/api/auth/login" : "/api/auth/register",
                {
                    method: "POST",
                    headers: {
                        Accept: "application/json",
                        "Content-Type": "application/json",
                    },
                    body: JSON.stringify(getAuthPayload(mode, input)),
                },
            );

            const data = await readJsonResponse(response);

            if (!response.ok) {
                const serverFieldErrors = getServerFieldErrors(data);
                if (hasFieldErrors(serverFieldErrors)) {
                    setFieldErrors(serverFieldErrors);
                }
                toast.error(getAuthErrorMessage(data));
                return;
            }

            router.replace(getSafeNextPath());
            router.refresh();
        } catch {
            toast.error("Não foi possível conectar ao servidor.");
        } finally {
            setIsSubmitting(false);
        }
    }

    return (
        <form className="space-y-5" noValidate onSubmit={handleSubmit}>
            <div className="text-center">
                <h1 className="font-heading text-3xl font-bold leading-tight text-foreground">
                    {copy.title}
                </h1>
                <p className="mx-auto mt-2 max-w-64 text-sm leading-5 text-muted-foreground">
                    {copy.subtitle}
                </p>
            </div>

            <div className="grid min-h-14 grid-cols-2 rounded-full bg-surface p-1 shadow-md">
                <button
                    type="button"
                    onClick={() => selectMode("login")}
                    className={`flex justify-center items-center gap-2 rounded-full text-sm font-semibold transition ${
                        mode === "login"
                            ? "bg-primary text-primary-foreground shadow-[0_2px_8px_rgba(0,0,0,0.08)]"
                            : "text-foreground"
                    }`}
                >
                    <KeyholeIcon size={32} />
                    Entrar
                </button>
                <button
                    type="button"
                    onClick={() => selectMode("register")}
                    className={`flex justify-center items-center gap-2 rounded-full text-sm font-semibold transition ${
                        mode === "register"
                            ? "bg-primary text-primary-foreground shadow-[0_2px_8px_rgba(0,0,0,0.08)]"
                            : "text-foreground"
                    }`}
                >
                    <UserCirclePlusIcon size={32} />
                    Criar conta
                </button>
            </div>

            {fieldsByMode[mode].map((field) => (
                <div key={field.name}>
                    <AuthField {...field} error={fieldErrors[field.name]} />
                    {mode === "login" && field.name === "password" ? (
                        <div className="mt-2 text-right">
                            <a
                                href="#"
                                className="text-sm font-semibold text-primary transition hover:text-foreground"
                            >
                                Esqueci minha senha
                            </a>
                        </div>
                    ) : null}
                </div>
            ))}

            <button
                type="submit"
                disabled={isSubmitting}
                className="min-h-14 w-full rounded-full bg-primary px-4 text-sm font-bold text-primary-foreground shadow-[0_2px_8px_rgba(0,0,0,0.08)] transition disabled:cursor-not-allowed disabled:opacity-70"
            >
                {isSubmitting ? copy.submitting : copy.submit}
            </button>
        </form>
    );
}
