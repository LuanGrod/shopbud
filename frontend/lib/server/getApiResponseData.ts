export function getApiResponseData<TData>(payload: unknown): TData | null {
    if (!payload || typeof payload !== "object" || !("data" in payload)) {
        return null;
    }

    return payload.data as TData;
}
