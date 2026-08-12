import type { Metadata } from "next";
import { LegalPage } from "@/components/mechablast/LegalPage";
import { readLegalDocument } from "../legal";
import { game, gameUrl, routes } from "../game";

const description = `Privacy policy for ${game.name}, published by ${game.publisher}.`;

export const metadata: Metadata = {
  title: "Privacy Policy",
  description,
  alternates: { canonical: routes.privacy },
  openGraph: {
    url: `${gameUrl}privacy/`,
    title: `${game.name}: Privacy Policy`,
    description,
    images: [{ url: game.ogImage, width: 1200, height: 630, alt: `${game.name} key art` }],
    type: "article",
  },
};

export default function PrivacyPage() {
  // Read at build time; output: "export" prerenders this page.
  const source = readLegalDocument("privacy");
  return <LegalPage eyebrow="Legal · Privacy" source={source} />;
}
