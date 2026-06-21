import { AUTH_COOKIE, authCookieOptions } from "@/lib/server/auth";
import { buildBackendApiUrl } from "@/lib/server/buildBackendApiUrl";
import { getApiResponseData } from "@/lib/server/getApiResponseData";
import { NextResponse } from "next/server";

type LoginResponseData = {
  token?: unknown;
};

export async function POST(request: Request) {
  const response = await fetch(buildBackendApiUrl("/auth/login"), {
    method: "POST",
    headers: {
      Accept: "application/json",
      "Content-Type": request.headers.get("Content-Type") ?? "application/json",
    },
    body: await request.text(),
  });

  const data = await response.json();

  if (!response.ok) {
    return NextResponse.json(data, { status: response.status });
  }

  const responseData = getApiResponseData<LoginResponseData>(data);
  const token = responseData?.token;

  if (typeof token !== "string") {
    return NextResponse.json(
      {
        success: false,
        message: "Resposta inválida do servidor.",
        data: null,
      },
      { status: 502 },
    );
  }

  const nextResponse = NextResponse.json({ success: true });
  nextResponse.cookies.set({
    ...authCookieOptions,
    name: AUTH_COOKIE,
    value: token,
  });

  return nextResponse;
}
