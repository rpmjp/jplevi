import type { Metadata, Viewport } from "next";
import { Saira, Archivo, Space_Grotesk, IBM_Plex_Mono } from "next/font/google";
import "./globals.css";
import { site } from "./site";

const display = Saira({
  subsets: ["latin"],
  weight: ["500", "600", "700", "800"],
  variable: "--font-display",
  display: "swap",
});

// Business side display face: heavy neo-grotesque for poster-scale headlines.
// Variable Archivo, loaded with its width axis so the display type can be
// condensed to match the reference rather than faked with a transform.
const grotesk = Archivo({
  subsets: ["latin"],
  axes: ["wdth"],
  variable: "--font-grotesk",
  display: "swap",
});

const body = Space_Grotesk({
  subsets: ["latin"],
  weight: ["400", "500", "700"],
  variable: "--font-body",
  display: "swap",
});

const mono = IBM_Plex_Mono({
  subsets: ["latin"],
  weight: ["400", "500", "600"],
  variable: "--font-mono",
  display: "swap",
});

export const metadata: Metadata = {
  metadataBase: new URL(site.url),
  title: {
    default: `${site.name}: ${site.tagline}`,
    template: `%s | ${site.name}`,
  },
  description: site.support,
  openGraph: {
    title: `${site.name}: ${site.tagline}`,
    description: site.support,
    url: site.url,
    siteName: site.legalName,
    type: "website",
  },
  twitter: {
    card: "summary_large_image",
    title: `${site.name}: ${site.tagline}`,
    description: site.support,
  },
};

export const viewport: Viewport = {
  themeColor: "#10161C",
  colorScheme: "dark",
};

/**
 * Root layout holds only the document shell and fonts. Site chrome lives in
 * app/(corporate)/layout.tsx so sections like /mechablast/ can supply their own.
 */
export default function RootLayout({ children }: { children: React.ReactNode }) {
  return (
    <html lang="en" className={`${display.variable} ${grotesk.variable} ${body.variable} ${mono.variable}`}>
      <body className="min-h-screen overflow-x-hidden">
        <a
          href="#main"
          className="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-50 focus:bg-brand focus:px-4 focus:py-2 focus:font-mono focus:text-xs focus:uppercase focus:tracking-label focus:text-white"
        >
          Skip to content
        </a>
        {children}
      </body>
    </html>
  );
}
