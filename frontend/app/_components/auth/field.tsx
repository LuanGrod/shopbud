"use client";

import type { Icon } from "@phosphor-icons/react";
import { EyeClosedIcon, EyeIcon } from "@phosphor-icons/react";
import { useState } from "react";

type AuthFieldProps = {
    label: string;
    name: string;
    type: string;
    autoComplete: string;
    error?: string;
    icon?: Icon;
};

export function AuthField({
    label,
    name,
    type,
    autoComplete,
    error,
    icon: FieldIcon,
}: AuthFieldProps) {
    const errorId = `${name}-error`;
    const isPasswordField = type === "password";
    const [isPasswordVisible, setIsPasswordVisible] = useState(false);
    const inputType =
        isPasswordField && isPasswordVisible ? "text" : type;
    const VisibilityIcon = isPasswordVisible ? EyeClosedIcon : EyeIcon;
    const iconClassName = error
        ? "text-destructive group-focus-within:text-destructive"
        : "text-border group-focus-within:text-primary";

    return (
        <label className="block text-sm font-medium text-foreground">
            {label}
            <span className="group relative mt-2 block">
                {FieldIcon ? (
                    <FieldIcon
                        aria-hidden="true"
                        size={20}
                        weight="bold"
                        className={`pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 transition ${iconClassName}`}
                    />
                ) : null}
                <input
                    name={name}
                    type={inputType}
                    autoComplete={autoComplete}
                    aria-invalid={error ? "true" : "false"}
                    aria-describedby={error ? errorId : undefined}
                    className={`min-h-12 w-full rounded-2xl border bg-surface py-3 text-base text-foreground shadow-[inset_0_0_0_1px_rgba(234,223,206,0.75)] outline-none ring-ring transition placeholder:text-muted-foreground focus:ring-2 ${
                        FieldIcon ? "pl-12" : "pl-4"
                    } ${isPasswordField ? "pr-12" : "pr-4"} ${
                        error
                            ? "border-destructive focus:border-destructive"
                            : "border-transparent focus:border-primary"
                    }`}
                />
                {isPasswordField ? (
                    <button
                        type="button"
                        onClick={() =>
                            setIsPasswordVisible((isVisible) => !isVisible)
                        }
                        className={`absolute right-4 top-1/2 flex size-6 -translate-y-1/2 items-center justify-center transition hover:text-foreground focus:outline-none focus:ring-2 focus:ring-ring ${iconClassName}`}
                        aria-label={
                            isPasswordVisible ? "Ocultar senha" : "Mostrar senha"
                        }
                    >
                        <VisibilityIcon aria-hidden="true" size={20} weight="bold" />
                    </button>
                ) : null}
            </span>
            {error ? (
                <span
                    id={errorId}
                    className="mt-2 block text-xs font-medium text-destructive"
                >
                    {error}
                </span>
            ) : null}
        </label>
    );
}
