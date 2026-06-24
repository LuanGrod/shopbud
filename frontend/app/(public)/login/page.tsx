import { AuthCard } from "@/app/_components/auth/card";
import { AuthForm } from "@/app/_components/auth/form";
import { BrandMark } from "@/app/_components/brand-mark";
import Image from "next/image";

type LoginPageProps = {
    searchParams: Promise<{
        modo?: string;
    }>;
};

export default async function LoginPage({ searchParams }: LoginPageProps) {
    const { modo } = await searchParams;
    const initialMode = modo === "cadastro" ? "register" : "login";

    return (
        <main className="h-dvh overflow-hidden bg-brand-graphite">
            <div className="relative mx-auto h-dvh w-full max-w-107.5 overflow-hidden bg-background px-5 pb-8 pt-12">
                <div className="pointer-events-none relative z-0 flex shrink-0 flex-col items-center justify-center">
                    <BrandMark className="h-auto w-56" priority />
                    <Image
                        src="/assets/cesta-login.png"
                        alt=""
                        width={1536}
                        height={1024}
                        className="max-h-[38dvh] w-full max-w-11/12 object-contain drop-shadow-neutral-300 drop-shadow-xl"
                    />
                </div>
                <div className="absolute inset-x-5 bottom-8 z-10">
                    <AuthCard>
                        <AuthForm initialMode={initialMode} />
                    </AuthCard>
                </div>
            </div>
        </main>
    );
}
