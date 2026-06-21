import { AuthShell } from "@/app/_components/auth/shell";
import { AuthForm } from "@/app/_components/auth/form";

type LoginPageProps = {
  searchParams: Promise<{
    modo?: string;
  }>;
};

export default async function LoginPage({ searchParams }: LoginPageProps) {
  const { modo } = await searchParams;
  const initialMode = modo === "cadastro" ? "register" : "login";

  return (
    <AuthShell>
      <AuthForm initialMode={initialMode} />
    </AuthShell>
  );
}
