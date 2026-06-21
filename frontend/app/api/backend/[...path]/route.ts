import { AUTH_COOKIE } from "@/lib/server/auth";
import { buildBackendApiUrl } from "@/lib/server/buildBackendApiUrl";
import { cookies } from "next/headers";

type BackendRouteContext = RouteContext<"/api/backend/[...path]">;

async function forwardToBackend(
  request: Request,
  context: BackendRouteContext,
) {
  const token = (await cookies()).get(AUTH_COOKIE)?.value;

  if (!token) {
    return Response.json({ message: "Unauthenticated." }, { status: 401 });
  }

  const { path } = await context.params;
  const requestUrl = new URL(request.url);
  const backendUrl = buildBackendApiUrl(path.join("/"), requestUrl.search);
  const method = request.method.toUpperCase();
  const hasBody = method !== "GET" && method !== "HEAD";

  return fetch(backendUrl, {
    method,
    headers: buildForwardHeaders(request, token),
    body: hasBody ? await request.text() : undefined,
  });
}

function buildForwardHeaders(request: Request, token: string) {
  const headers = new Headers();
  headers.set("Accept", request.headers.get("Accept") ?? "application/json");
  headers.set("Authorization", `Bearer ${token}`);

  const contentType = request.headers.get("Content-Type");
  if (contentType) {
    headers.set("Content-Type", contentType);
  }

  return headers;
}

export const GET = forwardToBackend;
export const POST = forwardToBackend;
export const PUT = forwardToBackend;
export const PATCH = forwardToBackend;
export const DELETE = forwardToBackend;
