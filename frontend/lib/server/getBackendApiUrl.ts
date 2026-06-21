import { trimTrailingSlash } from "./trimTrailingSlash";

export function getBackendApiUrl() {
    const baseUrl = trimTrailingSlash(
        process.env.SHOPBUD_API_URL ?? "http://localhost:8000",
    );

    return baseUrl.endsWith("/api") ? baseUrl : `${baseUrl}/api`;
}
