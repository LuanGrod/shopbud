import { getBackendApiUrl } from "./getBackendApiUrl";

export function buildBackendApiUrl(path: string, search = "") {
    const normalizedPath = path.startsWith("/") ? path : `/${path}`;
    return `${getBackendApiUrl()}${normalizedPath}${search}`;
}
