"use client";

import Link from "next/link";
import { usePathname } from "next/navigation";
import {
    ClipboardTextIcon,
    ClockCounterClockwiseIcon,
    GearSixIcon,
    HouseIcon,
    ShoppingCartIcon,
} from "@phosphor-icons/react";

const navigationItems = [
    { href: "/", label: "Início", icon: HouseIcon },
    { href: "/templates", label: "Templates", icon: ClipboardTextIcon },
    {
        href: "/compra",
        label: "Compra",
        icon: ShoppingCartIcon,
    },
    {
        href: "/historico",
        label: "Histórico",
        icon: ClockCounterClockwiseIcon,
    },
    { href: "/configuracoes", label: "Ajustes", icon: GearSixIcon },
];

export function BottomNavigation() {
    const pathname = usePathname();

    return (
        <nav
            aria-label="Navegação principal"
            className="sticky bottom-0 z-20 border-t border-border bg-surface px-3 pb-[max(0.75rem,env(safe-area-inset-bottom))] pt-1 shadow-[0_-8px_24px_rgba(35,35,35,0.08)]"
        >
            <ul className="grid h-14 grid-cols-5 items-start gap-1">
                {navigationItems.map(({ href, label, icon: Icon }) => {
                    const isActive =
                        href === "/"
                            ? pathname === href
                            : pathname.startsWith(href);

                    return (
                        <li key={href}>
                            <Link
                                href={href}
                                aria-label={label}
                                aria-current={isActive ? "page" : undefined}
                                title={label}
                                className={`flex h-fit flex-col items-center justify-start gap-1 rounded-2xl px-1 text-[0.63rem] font-semibold leading-none transition text-primary`}
                            >
                                <span
                                    className={`flex items-center justify-center transition size-7`}
                                >
                                    <Icon
                                        aria-hidden="true"
                                        size={22}
                                        weight={isActive ? "fill" : "regular"}
                                    />
                                </span>
                                <span className="max-w-full">
                                    {label}
                                </span>
                            </Link>
                        </li>
                    );
                })}
            </ul>
        </nav>
    );
}
