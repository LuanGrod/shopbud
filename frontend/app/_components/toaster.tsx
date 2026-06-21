"use client";

import { Toaster as SonnerToaster } from "sonner";

export function Toaster() {
  return (
    <SonnerToaster
      closeButton
      richColors
      position="top-center"
      toastOptions={{
        style: {
          borderRadius: "12px",
          fontFamily: "var(--font-sf-pro-text)",
        },
      }}
    />
  );
}
