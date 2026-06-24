"use client";

import { ONBOARDING_COOKIE, ONBOARDING_COOKIE_MAX_AGE } from "@/lib/onboarding";
import { CaretRightIcon } from "@phosphor-icons/react";
import Image, { type StaticImageData } from "next/image";
import { useRouter } from "next/navigation";
import {
    CSSProperties,
    KeyboardEvent,
    PointerEvent,
    useMemo,
    useRef,
    useState,
} from "react";
import stepImage from "@/public/assets/step.png";
import stepOneImage from "@/public/assets/step1.png";
import stepTwoImage from "@/public/assets/step2.png";
import stepThreeImage from "@/public/assets/step3.png";

type OnboardingCarouselProps = {
    nextPath: string;
};

type OnboardingStep = {
    title: string;
    description: string;
    image: StaticImageData;
    imageAlt: string;
    blobStyle: CSSProperties;
};

const steps = [
    {
        title: "Compre sem esquecer nada",
        description:
            "Use sua lista no mercado e marque os produtos enquanto coloca tudo na cesta.",
        image: stepImage,
        imageAlt: "Celular com lista de compras ao lado de uma cesta de mercado.",
        blobStyle: {
            borderRadius: "44% 56% 50% 50% / 58% 42% 58% 42%",
            transform: "rotate(-8deg) translateY(6px)",
        },
    },
    {
        title: "Crie listas que você reutiliza",
        description:
            "Monte templates para seus mercados favoritos, com os produtos que você compra sempre.",
        image: stepOneImage,
        imageAlt: "Cesta com verduras ao lado de uma lista de compras marcada.",
        blobStyle: {
            borderRadius: "58% 42% 46% 54% / 44% 58% 42% 56%",
            transform: "rotate(6deg) translateY(8px)",
        },
    },
    {
        title: "Siga a ordem dos setores",
        description:
            "Organize os setores no caminho real do supermercado e evite voltar pelos corredores.",
        image: stepTwoImage,
        imageAlt: "Produtos separados por setores com uma rota pontilhada entre eles.",
        blobStyle: {
            borderRadius: "48% 52% 60% 40% / 50% 45% 55% 50%",
            transform: "rotate(-3deg) translateY(4px)",
        },
    },
    {
        title: "Controle o total da compra",
        description:
            "Informe preços e quantidades para acompanhar seus gastos e salvar o resumo no histórico.",
        image: stepThreeImage,
        imageAlt: "Sacola de compras com recibo, moedas e etiquetas de preço.",
        blobStyle: {
            borderRadius: "56% 44% 52% 48% / 42% 54% 46% 58%",
            transform: "rotate(7deg) translateY(6px)",
        },
    },
] satisfies OnboardingStep[];

const swipeThreshold = 48;

function loginPathFor(nextPath: string) {
    const params = new URLSearchParams();

    if (nextPath !== "/") {
        params.set("next", nextPath);
    }

    return params.size > 0 ? `/login?${params.toString()}` : "/login";
}

function setOnboardingSeenCookie() {
    const secure = window.location.protocol === "https:" ? "; Secure" : "";

    document.cookie = `${ONBOARDING_COOKIE}=1; Max-Age=${ONBOARDING_COOKIE_MAX_AGE}; Path=/; SameSite=Lax${secure}`;
}

export function OnboardingCarousel({ nextPath }: OnboardingCarouselProps) {
    const router = useRouter();
    const [activeIndex, setActiveIndex] = useState(0);
    const [dragOffset, setDragOffset] = useState(0);
    const [isDragging, setIsDragging] = useState(false);
    const dragStartXRef = useRef<number | null>(null);
    const targetLoginPath = useMemo(() => loginPathFor(nextPath), [nextPath]);
    const isLastStep = activeIndex === steps.length - 1;

    function goToStep(nextIndex: number) {
        setActiveIndex(Math.min(Math.max(nextIndex, 0), steps.length - 1));
    }

    function completeOnboarding() {
        setOnboardingSeenCookie();
        router.replace(targetLoginPath);
    }

    function goNext() {
        if (isLastStep) {
            completeOnboarding();
            return;
        }

        goToStep(activeIndex + 1);
    }

    function goPrevious() {
        goToStep(activeIndex - 1);
    }

    function handlePointerDown(event: PointerEvent<HTMLDivElement>) {
        dragStartXRef.current = event.clientX;
        setIsDragging(true);
        event.currentTarget.setPointerCapture(event.pointerId);
    }

    function handlePointerMove(event: PointerEvent<HTMLDivElement>) {
        if (dragStartXRef.current === null) {
            return;
        }

        const rawOffset = event.clientX - dragStartXRef.current;
        const isAtFirstStep = activeIndex === 0 && rawOffset > 0;
        const isAtLastStep = isLastStep && rawOffset < 0;
        const nextOffset = isAtFirstStep || isAtLastStep ? rawOffset * 0.25 : rawOffset;

        setDragOffset(nextOffset);
    }

    function handlePointerEnd(event: PointerEvent<HTMLDivElement>) {
        if (dragStartXRef.current === null) {
            return;
        }

        const offset = event.clientX - dragStartXRef.current;
        dragStartXRef.current = null;
        setIsDragging(false);
        setDragOffset(0);

        if (offset <= -swipeThreshold && !isLastStep) {
            goNext();
        }

        if (offset >= swipeThreshold && activeIndex > 0) {
            goPrevious();
        }
    }

    function handleKeyDown(event: KeyboardEvent<HTMLDivElement>) {
        if (event.key === "ArrowRight") {
            event.preventDefault();
            goNext();
        }

        if (event.key === "ArrowLeft") {
            event.preventDefault();
            goPrevious();
        }
    }

    return (
        <main className="min-h-screen bg-brand-graphite">
            <div className="mx-auto flex min-h-screen w-full max-w-107.5 flex-col overflow-hidden bg-background px-6 pb-7 pt-6">
                <section
                    aria-label="Introdução ao ShopBud"
                    className="flex flex-1 flex-col"
                >
                    <div
                        className="relative flex flex-1 touch-pan-y overflow-hidden outline-none"
                        onKeyDown={handleKeyDown}
                        onPointerCancel={handlePointerEnd}
                        onPointerDown={handlePointerDown}
                        onPointerMove={handlePointerMove}
                        onPointerUp={handlePointerEnd}
                        tabIndex={0}
                    >
                        <div
                            className="flex h-full w-full"
                            style={{
                                transform: `translateX(calc(${-activeIndex * 100}% + ${dragOffset}px))`,
                                transition: isDragging
                                    ? "none"
                                    : "transform 280ms cubic-bezier(0.22, 1, 0.36, 1)",
                            }}
                        >
                            {steps.map((step, index) => (
                                <article
                                    aria-hidden={activeIndex !== index}
                                    className="flex min-w-full flex-col items-center justify-center text-center"
                                    key={step.title}
                                >
                                    <div className="relative flex h-[42vh] min-h-64 max-h-92 w-full items-center justify-center">
                                        <div
                                            aria-hidden="true"
                                            className="absolute h-[76%] w-[82%] bg-brand-graphite/[0.06]"
                                            style={step.blobStyle}
                                        />
                                        <Image
                                            src={step.image}
                                            alt={step.imageAlt}
                                            className="relative z-10 h-full w-full object-contain"
                                            priority={step.image === stepImage}
                                            sizes="(max-width: 430px) 88vw, 380px"
                                        />
                                    </div>
                                    <div className="mt-7 min-h-36 max-w-84">
                                        <h1 className="font-heading text-4xl font-bold leading-tight text-foreground">
                                            {step.title}
                                        </h1>
                                        <p className="mx-auto mt-4 max-w-76 text-base font-medium leading-6 text-muted-foreground">
                                            {step.description}
                                        </p>
                                    </div>
                                </article>
                            ))}
                        </div>
                    </div>

                    <div
                        aria-label="Etapas do onboarding"
                        className="mt-5 flex items-center justify-center gap-3"
                    >
                        {steps.map((step, index) => (
                            <button
                                aria-current={activeIndex === index ? "step" : undefined}
                                aria-label={`Ir para etapa ${index + 1}: ${step.title}`}
                                className={`size-3 rounded-full transition ${
                                    activeIndex === index
                                        ? "bg-primary"
                                        : "bg-border hover:bg-muted-foreground/50"
                                }`}
                                key={step.title}
                                onClick={() => goToStep(index)}
                                type="button"
                            />
                        ))}
                    </div>

                    <button
                        className="mt-8 inline-flex min-h-14 w-full items-center justify-center gap-3 rounded-full bg-primary px-5 text-sm font-bold uppercase tracking-normal text-primary-foreground shadow-[0_2px_8px_rgba(0,0,0,0.08)] transition hover:bg-primary/95 focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed"
                        onClick={goNext}
                        type="button"
                    >
                        {isLastStep ? "Começar" : "Avançar"}
                        <CaretRightIcon aria-hidden="true" size={22} weight="bold" />
                    </button>

                    <div className="mt-auto pt-6 text-center">
                        <button
                            className="min-h-11 px-4 text-sm font-bold text-primary transition hover:text-secondary-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2"
                            onClick={completeOnboarding}
                            type="button"
                        >
                            Pular
                        </button>
                    </div>
                </section>
            </div>
        </main>
    );
}
