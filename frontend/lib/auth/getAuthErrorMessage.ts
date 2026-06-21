export function getAuthErrorMessage(data: unknown) {
    if (!data || typeof data !== "object") {
        return "Não foi possível concluir a solicitação.";
    }

    if ("msg" in data && typeof data.msg === "string") {
        return data.msg;
    }

    if ("message" in data && typeof data.message === "string") {
        return data.message;
    }

    if ("errors" in data && data.errors && typeof data.errors === "object") {
        const [firstError] = Object.values(data.errors).flat();
        if (typeof firstError === "string") {
            return firstError;
        }
    }

    return "Não foi possível concluir a solicitação.";
}
