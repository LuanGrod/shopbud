import { AUTH_COOKIE, authCookieOptions } from "@/lib/server/auth";
import { buildBackendApiUrl } from "@/lib/server/buildBackendApiUrl";
import { cookies } from "next/headers";
import { NextResponse } from "next/server";

export async function POST() {
  const token = (await cookies()).get(AUTH_COOKIE)?.value;

  if (token) {
    await fetch(buildBackendApiUrl("/auth/logout"), {
      method: "POST",
      headers: {
        Accept: "application/json",
        Authorization: `Bearer ${token}`,
      },
    }).catch(() => undefined);
  }

  const response = NextResponse.json({ success: true });
  response.cookies.set({
    ...authCookieOptions,
    name: AUTH_COOKIE,
    value: "",
    maxAge: 0,
  });

  return response;
}
