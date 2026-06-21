type AuthFieldProps = {
    label: string;
    name: string;
    type: string;
    autoComplete: string;
    error?: string;
};

export function AuthField({
    label,
    name,
    type,
    autoComplete,
    error,
}: AuthFieldProps) {
    const errorId = `${name}-error`;

    return (
        <label className="block text-sm font-medium text-foreground">
            {label}
            <input
                name={name}
                type={type}
                autoComplete={autoComplete}
                aria-invalid={error ? "true" : "false"}
                aria-describedby={error ? errorId : undefined}
                className={`mt-2 min-h-12 w-full rounded-full border bg-surface px-4 text-base text-foreground shadow-[inset_0_0_0_1px_rgba(234,223,206,0.75)] outline-none ring-ring transition placeholder:text-muted-foreground focus:ring-2 ${
                    error
                        ? "border-destructive focus:border-destructive"
                        : "border-transparent focus:border-primary"
                }`}
            />
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
