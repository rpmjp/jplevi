import type { Metadata, Viewport } from "next";
import { Saira, IBM_Plex_Sans_Condensed, IBM_Plex_Sans, Space_Grotesk, IBM_Plex_Mono } from "next/font/google";
import "./globals.css";
import { site } from "./site";
import { biz } from "./business";

const display = Saira({
  subsets: ["latin"],
  weight: ["500", "600", "700", "800"],
  variable: "--font-display",
  display: "swap",
});

// Business side display face: Plex Sans Condensed, drawn narrow so the
// poster-scale headlines keep their measure.
const grotesk = IBM_Plex_Sans_Condensed({
  subsets: ["latin"],
  weight: ["500", "600", "700"],
  variable: "--font-grotesk",
  display: "swap",
});

// Business side reading face; scoped in .biz-scope so gaming keeps Space Grotesk.
const business = IBM_Plex_Sans({
  subsets: ["latin"],
  weight: ["400", "500", "600"],
  variable: "--font-business",
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
  // Defaults describe the business site, which is what "/" now is. The gaming
  // side and MechaBlast override title, description, and openGraph themselves.
  title: {
    default: `${biz.name}: AI that works for your business.`,
    template: `%s | ${biz.name}`,
  },
  description: biz.lead,
  openGraph: {
    title: `${biz.name}: AI that works for your business.`,
    description: biz.lead,
    url: site.url,
    siteName: site.legalName,
    type: "website",
  },
  twitter: {
    card: "summary_large_image",
    title: `${biz.name}: AI that works for your business.`,
    description: biz.lead,
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
    <html lang="en" className={`${display.variable} ${grotesk.variable} ${business.variable} ${body.variable} ${mono.variable}`}>
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
