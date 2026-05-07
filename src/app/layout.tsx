import type { Metadata } from "next";
import "./globals.css";

export const metadata: Metadata = {
  title: "CATFLOW - Gestão de Gatos",
  description: "Sistema de gestão para gatos e ONGs",
  manifest: "/manifest.json",
  other: {
    "apple-mobile-web-app-capable": "yes",
    "apple-mobile-web-app-status-bar-style": "default",
    "apple-mobile-web-app-title": "CATFLOW",
    "mobile-web-app-capable": "yes",
  },
};

export const viewport = {
  themeColor: "#14b8a6",
  width: "device-width",
  initialScale: 1,
  maximumScale: 1,
  userScalable: false,
};

export default function RootLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  return (
    <html lang="pt-BR">
      <head>
        <link rel="icon" href="/icons/icon-192.svg" />
        <link rel="apple-touch-icon" href="/icons/icon-192.svg" />
      </head>
      <body className="antialiased">
        {children}
        <script
          dangerouslySetInnerHTML={{
            __html: `
              if ('serviceWorker' in navigator) {
                window.addEventListener('load', () => {
                  navigator.serviceWorker.register('/service-worker.js')
                    .then(() => console.log('SW registered'))
                    .catch(() => console.log('SW failed'));
                });
              }
            `,
          }}
        />
      </body>
    </html>
  );
}
