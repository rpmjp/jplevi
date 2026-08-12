import type { Metadata } from "next";
import { LegalPage } from "@/components/mechablast/LegalPage";
import { readLegalDocument } from "../legal";
import { game, gameUrl, routes } from "../game";

const description = `Terms of Service and End User License Agreement for ${game.name}, published by ${game.publisher}.`;

export const metadata: Metadata = {
  title: "Terms of Service",
  description,
  alternates: { canonical: routes.terms },
  openGraph: {
    url: `${gameUrl}terms/`,
    title: `${game.name}: Terms of Service`,
    description,
    images: [{ url: game.ogImage, width: 1200, height: 630, alt: `${game.name} key art` }],
    type: "article",
  },
};

export default function TermsPage() {
  // Read at build time; output: "export" prerenders this page.
  const source = readLegalDocument("terms");
  return <LegalPage eyebrow="Legal · Terms" source={source} />;
}
