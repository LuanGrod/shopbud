import { AUTH_COOKIE } from "@/lib/server/auth";
import { ONBOARDING_COOKIE } from "@/lib/onboarding";
import { NextRequest, NextResponse } from "next/server";

const publicRoutes = ["/login", "/cadastro", "/onboarding", "/vitrine-fontes"];
const protectedRoutes = [
  "/",
  "/templates",
  "/compra",
  "/historico",
  "/configuracoes",
];

export function proxy(request: NextRequest) {
  const { pathname } = request.nextUrl;
  const hasToken = request.cookies.has(AUTH_COOKIE);
  const hasSeenOnboarding = request.cookies.has(ONBOARDING_COOKIE);
  const isPublicRoute = publicRoutes.includes(pathname);
  const isProtectedRoute = protectedRoutes.some((route) =>
    route === "/" ? pathname === "/" : pathname.startsWith(route),
  );

  if (isProtectedRoute && !hasToken) {
    const nextPath = `${pathname}${request.nextUrl.search}`;
    const destinationUrl = new URL(
      hasSeenOnboarding ? "/login" : "/onboarding",
      request.url,
    );
    destinationUrl.searchParams.set("next", nextPath);

    return NextResponse.redirect(destinationUrl);
  }

  if (isPublicRoute && hasToken && pathname !== "/vitrine-fontes") {
    return NextResponse.redirect(new URL("/", request.url));
  }

  return NextResponse.next();
}

export const config = {
  matcher: ["/((?!api|_next/static|_next/image|favicon.ico|.*\\..*).*)"],
};
