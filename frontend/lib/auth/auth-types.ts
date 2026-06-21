export type AuthMode = "login" | "register";

export type AuthFieldName =
    | "name"
    | "email"
    | "password"
    | "password_confirmation";

export type AuthFieldErrors = Partial<Record<AuthFieldName, string>>;

export type AuthInput = Record<AuthFieldName, string>;
