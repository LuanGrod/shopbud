import { OnboardingCarousel } from "./onboarding-carousel";

type OnboardingPageProps = {
    searchParams: Promise<{
        next?: string;
    }>;
};

function getSafeNextPath(next?: string) {
    if (!next || !next.startsWith("/") || next.startsWith("//")) {
        return "/";
    }

    return next;
}

export default async function OnboardingPage({
    searchParams,
}: OnboardingPageProps) {
    const { next } = await searchParams;

    return <OnboardingCarousel nextPath={getSafeNextPath(next)} />;
}
