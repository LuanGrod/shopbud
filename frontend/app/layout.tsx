import type { Metadata } from "next";
import localFont from "next/font/local";
import { Toaster } from "./_components/toaster";
import "./globals.css";

const baloo = localFont({
  variable: "--font-baloo-2",
  src: [
    {
      path: "../public/fonts/Baloo2/baloo2-regular.woff2",
      weight: "400",
      style: "normal",
    },
    {
      path: "../public/fonts/Baloo2/baloo2-bold.woff2",
      weight: "700",
      style: "normal",
    },
  ],
});

const sfProDisplay = localFont({
  variable: "--font-sf-pro-display",
  src: [
    {
      path: "../public/fonts/SF-Pro/Display/sf-pro-display-thin.woff2",
      weight: "100",
      style: "normal",
    },
    {
      path: "../public/fonts/SF-Pro/Display/sf-pro-display-ultralight.woff2",
      weight: "200",
      style: "normal",
    },
    {
      path: "../public/fonts/SF-Pro/Display/sf-pro-display-light.woff2",
      weight: "300",
      style: "normal",
    },
    {
      path: "../public/fonts/SF-Pro/Display/sf-pro-display-regular.woff2",
      weight: "400",
      style: "normal",
    },
    {
      path: "../public/fonts/SF-Pro/Display/sf-pro-display-medium.woff2",
      weight: "500",
      style: "normal",
    },
    {
      path: "../public/fonts/SF-Pro/Display/sf-pro-display-semibold.woff2",
      weight: "600",
      style: "normal",
    },
    {
      path: "../public/fonts/SF-Pro/Display/sf-pro-display-bold.woff2",
      weight: "700",
      style: "normal",
    },
    {
      path: "../public/fonts/SF-Pro/Display/sf-pro-display-heavy.woff2",
      weight: "800",
      style: "normal",
    },
    {
      path: "../public/fonts/SF-Pro/Display/sf-pro-display-black.woff2",
      weight: "900",
      style: "normal",
    },
  ],
});

const sfProText = localFont({
  variable: "--font-sf-pro-text",
  src: [
    {
      path: "../public/fonts/SF-Pro/Text/sf-pro-text-light.woff2",
      weight: "300",
      style: "normal",
    },
    {
      path: "../public/fonts/SF-Pro/Text/sf-pro-text-regular.woff2",
      weight: "400",
      style: "normal",
    },
    {
      path: "../public/fonts/SF-Pro/Text/sf-pro-text-medium.woff2",
      weight: "500",
      style: "normal",
    },
    {
      path: "../public/fonts/SF-Pro/Text/sf-pro-text-semibold.woff2",
      weight: "600",
      style: "normal",
    },
    {
      path: "../public/fonts/SF-Pro/Text/sf-pro-text-bold.woff2",
      weight: "700",
      style: "normal",
    },
    {
      path: "../public/fonts/SF-Pro/Text/sf-pro-text-heavy.woff2",
      weight: "800",
      style: "normal",
    },
  ],
});

export const metadata: Metadata = {
  title: {
    default: "ShopBud",
    template: "%s | ShopBud",
  },
  description: "Lista de compras inteligente para supermercado.",
  applicationName: "ShopBud",
  manifest: "/favicon/site.webmanifest",
  icons: {
    icon: [
      { url: "/favicon/favicon.ico", sizes: "48x48" },
      { url: "/favicon/favicon.svg", type: "image/svg+xml" },
      { url: "/favicon/favicon-96x96.png", sizes: "96x96", type: "image/png" },
    ],
    apple: [
      {
        url: "/favicon/apple-touch-icon.png",
        sizes: "180x180",
        type: "image/png",
      },
    ],
  },
};

export default function RootLayout({
  children,
}: Readonly<{
  children: React.ReactNode;
}>) {
  return (
    <html
      lang="pt-BR"
      className={`${sfProText.variable} ${sfProDisplay.variable} ${baloo.variable} h-full antialiased`}
    >
      <body className="min-h-full flex flex-col">
        {children}
        <Toaster />
      </body>
    </html>
  );
}
