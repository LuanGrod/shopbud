"use client";

import { useRouter } from "next/navigation";
import { useState } from "react";

export function LogoutButton() {
  const router = useRouter();
  const [isSubmitting, setIsSubmitting] = useState(false);

  async function handleLogout() {
    setIsSubmitting(true);
    await fetch("/api/auth/logout", { method: "POST" });
    router.replace("/login");
    router.refresh();
  }

  return (
    <button
      type="button"
      disabled={isSubmitting}
      onClick={handleLogout}
      className="min-h-12 w-full px-4 py-3 text-left text-sm font-semibold text-destructive transition disabled:cursor-not-allowed disabled:opacity-70"
    >
      {isSubmitting ? "Saindo..." : "Sair"}
    </button>
  );
}
