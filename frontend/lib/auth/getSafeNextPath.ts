export function getSafeNextPath(search = window.location.search) {
    const next = new URLSearchParams(search).get("next");

    if (!next || !next.startsWith("/") || next.startsWith("//")) {
        return "/";
    }

    return next;
}
