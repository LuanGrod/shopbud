"use client";

import type { CSSProperties, ReactNode } from "react";
import { useLayoutEffect, useRef, useState } from "react";

type AuthCardProps = {
    children: ReactNode;
};

export function AuthCard({ children }: AuthCardProps) {
    const contentRef = useRef<HTMLDivElement>(null);
    const [height, setHeight] = useState<number>();

    useLayoutEffect(() => {
        const content = contentRef.current;

        if (!content) {
            return;
        }

        const measuredContent = content;

        function updateHeight() {
            setHeight(measuredContent.getBoundingClientRect().height);
        }

        updateHeight();

        const observer = new ResizeObserver(updateHeight);
        observer.observe(measuredContent);

        return () => observer.disconnect();
    }, []);

    const style: CSSProperties | undefined =
        height === undefined ? undefined : { height };

    return (
        <div
            className="overflow-hidden rounded-4xl bg-surface shadow-2xl transition-[height] duration-300 ease-out motion-reduce:transition-none"
            style={style}
        >
            <div ref={contentRef} className="p-5">
                {children}
            </div>
        </div>
    );
}
