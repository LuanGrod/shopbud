export async function readJsonResponse(response: Response) {
    const contentType = response.headers.get("Content-Type") ?? "";

    if (!contentType.includes("application/json")) {
        return null;
    }

    return response.json();
}
