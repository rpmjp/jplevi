import type { Metadata } from "next";
import Link from "next/link";
import { Panel } from "@/components/Panel";
import { site } from "@/app/site";

export const metadata: Metadata = {
  title: { absolute: "Gaming | JP Levi" },
  description: "The gaming division of JP Levi Inc. Original games and the tooling behind them.",
  alternates: { canonical: "/gaming/" },
  openGraph: {
    title: "Gaming | JP Levi",
    description: "The gaming division of JP Levi Inc. Original games and the tooling behind them.",
    url: "https://jplevi.com/gaming/",
    type: "website",
  },
};

export default function GamingPage() {
  return (
    <section className="mx-auto max-w-shell px-5 pb-24 pt-20 sm:px-8 sm:pt-28">
      <nav aria-label="Breadcrumb" className="mb-8">
        <Link href="/" className="font-mono text-[0.7rem] uppercase tracking-hud text-ink-dim hover:text-hud">
          ← {site.name}
        </Link>
      </nav>

      <div className="boot-in max-w-3xl">
        <p className="hud-label">Division 01 · Gaming</p>
        <h1 className="mt-6 font-display text-4xl font-bold leading-[1.08] tracking-tight text-ink sm:text-5xl">
          Gaming
        </h1>
        <p className="mt-6 max-w-2xl text-base leading-relaxed text-ink-muted sm:text-lg">
          Original games and the tooling behind them. This section is under construction. Titles,
          devlogs, and press material land here.
        </p>
      </div>

      {/* First title out of the division. */}
      <Panel className="mt-14 p-8 sm:p-12">
        <div className="flex flex-wrap items-center gap-3">
          <span aria-hidden="true" className="inline-block h-1.5 w-1.5 rounded-full bg-hud" />
          <p className="font-mono text-[0.65rem] uppercase tracking-hud text-hud">Title 01</p>
        </div>
        <h2 className="mt-6 font-display text-2xl font-semibold tracking-tight text-ink sm:text-3xl">
          MechaBlast
        </h2>
        <p className="mt-3 max-w-2xl text-sm leading-relaxed text-ink-muted">
          Cel-shaded, landscape, run-and-gun mobile platformer. Blast robots, beat bosses across 8
          worlds, earn scrap, upgrade your mech.
        </p>
        <div className="mt-7">
          <Link href="/mechablast/" className="btn-primary">
            Visit MechaBlast
            <span aria-hidden="true">→</span>
          </Link>
        </div>
      </Panel>

      <Panel className="mt-6 p-8 sm:p-12">
        <div className="flex flex-wrap items-center gap-3">
          <span
            aria-hidden="true"
            className="inline-block h-1.5 w-1.5 animate-sweep rounded-full bg-hazard"
          />
          <p className="font-mono text-[0.65rem] uppercase tracking-hud text-hazard">
            Section under construction
          </p>
        </div>
        <h2 className="mt-6 max-w-2xl font-display text-2xl font-semibold leading-snug tracking-tight text-ink sm:text-3xl">
          Coming soon.
        </h2>
        <p className="mt-4 max-w-2xl text-sm leading-relaxed text-ink-muted">
          Want to hear first when something ships? Reach the studio directly.
        </p>
        <div className="mt-8 flex flex-wrap items-center gap-4">
          <a href={`mailto:${site.email.hello}`} className="btn-primary">
            {site.email.hello}
          </a>
          <Link href="/" className="btn-ghost">
            Back to {site.name}
          </Link>
        </div>
      </Panel>

      {/* Shipped work sits here rather than in the business nav. */}
      <p className="mt-10 font-mono text-[0.7rem] uppercase tracking-hud text-ink-dim">
        <Link href="/work/" className="transition-colors hover:text-hud">
          Shipped work →
        </Link>
      </p>
    </section>
  );
}
