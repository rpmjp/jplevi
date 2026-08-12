import type { Metadata } from "next";
import Link from "next/link";
import { game, gameUrl, routes } from "../game";

const description = `Press information for ${game.name}. ${game.pitch} Press kit coming soon.`;

export const metadata: Metadata = {
  title: "Press",
  description,
  alternates: { canonical: routes.press },
  openGraph: {
    url: `${gameUrl}press/`,
    title: `${game.name}: Press`,
    description,
    images: [{ url: game.ogImage, width: 1200, height: 630, alt: `${game.name} key art` }],
    type: "article",
  },
};

const factSheet = [
  { label: "Title", value: game.name },
  { label: "Publisher", value: `${game.publisher} (${game.publisherNote})` },
  { label: "Genre", value: "Run-and-gun platformer" },
  { label: "Platforms", value: "iOS · Android" },
  { label: "Orientation", value: "Landscape" },
  { label: "Price", value: "Free to play, optional in-app purchases" },
];

export default function PressPage() {
  return (
    <section className="mx-auto max-w-shell px-5 pb-20 pt-14 sm:px-8 sm:pt-20">
      <nav aria-label="Breadcrumb" className="mb-10">
        <Link
          href={routes.home}
          className="font-mono text-[0.7rem] uppercase tracking-hud text-ink-dim transition-colors hover:text-mecha-cyan"
        >
          ← {game.name}
        </Link>
      </nav>

      <p className="cel-eyebrow">Press</p>
      <h1 className="mt-5 font-display text-4xl font-extrabold uppercase tracking-tight text-ink sm:text-5xl">
        {game.name}
      </h1>
      <p className="mt-6 max-w-[72ch] font-display text-xl font-medium leading-snug text-ink">
        {game.pitch}
      </p>

      <div className="mt-12 max-w-3xl cel-panel p-8 sm:p-10">
        <div className="flex flex-wrap items-center gap-3">
          <span aria-hidden="true" className="inline-block h-2 w-2 bg-mecha-cyan" />
          <p className="cel-eyebrow">Press kit coming soon</p>
        </div>
        <p className="mt-5 text-base leading-relaxed text-ink-muted">
          Logos, key art, screenshots, and the trailer will be published here as a downloadable kit.
          In the meantime, email us for assets or review access and we will send what you need.
        </p>
        <div className="mt-8">
          <a href={`mailto:${game.supportEmail}`} className="cel-btn-primary">
            {game.supportEmail}
          </a>
        </div>
      </div>

      {/* ---- Fact sheet ------------------------------------------------ */}
      <div className="mt-16 max-w-3xl">
        <h2 className="cel-eyebrow">Fact sheet</h2>
        <dl className="mt-6 border-t-2 border-mecha-line">
          {factSheet.map((row) => (
            <div
              key={row.label}
              className="flex flex-col gap-1 border-b-2 border-mecha-line py-4 sm:flex-row sm:gap-8"
            >
              <dt className="font-mono text-[0.7rem] uppercase tracking-hud text-ink-dim sm:w-40 sm:shrink-0">
                {row.label}
              </dt>
              <dd className="text-sm text-ink-muted">{row.value}</dd>
            </div>
          ))}
        </dl>
      </div>
    </section>
  );
}
