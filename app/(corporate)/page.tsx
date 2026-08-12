import type { Metadata } from "next";
import Link from "next/link";
import { Panel } from "@/components/Panel";
import { divisions, primaryDivision, site, statusLabel } from "@/app/site";

export const metadata: Metadata = {
  alternates: { canonical: "/" },
};

export default function HomePage() {
  return (
    <>
      {/* ---- Hero ---------------------------------------------------- */}
      <section className="mx-auto max-w-shell px-5 pb-20 pt-20 sm:px-8 sm:pt-28">
        <div className="boot-in max-w-3xl">
          <p className="hud-label">
            {site.legalName} · {site.state}
          </p>
          <h1 className="mt-6 font-display text-4xl font-bold leading-[1.08] tracking-tight text-ink sm:text-6xl">
            {site.statement}
          </h1>
          <p className="mt-6 max-w-2xl text-base leading-relaxed text-ink-muted sm:text-lg">
            {site.support}
          </p>

          <div
            className="boot-in mt-10 flex flex-wrap items-center gap-4"
            style={{ "--boot-delay": "160ms" } as React.CSSProperties}
          >
            {primaryDivision ? (
              <Link href={primaryDivision.href} className="btn-primary">
                Enter {primaryDivision.name}
                <span aria-hidden="true">→</span>
              </Link>
            ) : null}
            <a href={`mailto:${site.email.hello}`} className="btn-ghost">
              Start a conversation
            </a>
          </div>
        </div>

        {/* Telemetry readout strip */}
        <div
          className="boot-in mt-16 flex flex-wrap items-center gap-x-8 gap-y-3 border-t border-gun-500/60 pt-5"
          style={{ "--boot-delay": "260ms" } as React.CSSProperties}
        >
          <span className="hud-label">Status</span>
          <span className="font-mono text-[0.7rem] uppercase tracking-hud text-ink-muted">
            <span className="mr-2 inline-block h-1.5 w-1.5 animate-sweep rounded-full bg-hud align-middle" />
            Operating
          </span>
          <span className="font-mono text-[0.7rem] uppercase tracking-hud text-ink-dim">
            {divisions.length} divisions
          </span>
          <span className="font-mono text-[0.7rem] uppercase tracking-hud text-ink-dim">
            Est. {site.founded}
          </span>
        </div>
      </section>

      {/* ---- Divisions ----------------------------------------------- */}
      <section
        aria-labelledby="divisions-heading"
        className="mx-auto max-w-shell px-5 py-16 sm:px-8"
      >
        <div className="flex items-end justify-between gap-6">
          <div>
            <p className="hud-label">Divisions</p>
            <h2
              id="divisions-heading"
              className="mt-3 font-display text-2xl font-semibold tracking-tight text-ink sm:text-3xl"
            >
              Four disciplines, one studio.
            </h2>
          </div>
        </div>
        <div className="hud-rule mt-6" />

        <ul className="mt-8 grid gap-5 sm:grid-cols-2">
          {divisions.map((division, i) => {
            const index = String(i + 1).padStart(2, "0");
            const isLive = division.status === "live" && Boolean(division.href);

            const inner = (
              <>
                <div className="flex items-start justify-between gap-4">
                  <span className="font-mono text-[0.7rem] tracking-hud text-ink-dim">{index}</span>
                  <span
                    className={`font-mono text-[0.6rem] uppercase tracking-hud ${
                      isLive ? "text-hud" : "text-hazard"
                    }`}
                  >
                    <span
                      aria-hidden="true"
                      className={`mr-2 inline-block h-1.5 w-1.5 rounded-full align-middle ${
                        isLive ? "bg-hud" : "bg-hazard/70"
                      }`}
                    />
                    {statusLabel[division.status]}
                  </span>
                </div>

                <h3
                  className={`mt-6 font-display text-xl font-semibold tracking-tight ${
                    isLive ? "text-ink" : "text-ink-muted"
                  }`}
                >
                  {division.name}
                </h3>
                <p className="mt-2 text-sm leading-relaxed text-ink-muted">{division.blurb}</p>

                <p
                  className={`mt-6 font-mono text-[0.65rem] uppercase tracking-hud ${
                    isLive ? "text-signal" : "text-ink-dim"
                  }`}
                >
                  {isLive ? "Enter section →" : "Not yet open"}
                </p>
              </>
            );

            return (
              <Panel
                as="li"
                key={division.id}
                className={isLive ? "transition-colors hover:border-gun-400" : "opacity-80"}
              >
                {isLive && division.href ? (
                  <Link
                    href={division.href}
                    className="block p-6 focus-visible:outline-offset-[-2px] sm:p-7"
                  >
                    {inner}
                  </Link>
                ) : (
                  <div className="p-6 sm:p-7">{inner}</div>
                )}
              </Panel>
            );
          })}
        </ul>
      </section>

      {/* ---- Studio + contact ---------------------------------------- */}
      <section aria-labelledby="studio-heading" className="mx-auto max-w-shell px-5 py-16 sm:px-8">
        <Panel className="p-8 sm:p-12">
          <p className="hud-label">The studio</p>
          <h2
            id="studio-heading"
            className="mt-4 max-w-3xl font-display text-2xl font-semibold leading-snug tracking-tight text-ink sm:text-3xl"
          >
            {site.studioLine}
          </h2>
          <div className="mt-8 flex flex-wrap items-center gap-4">
            <a href={`mailto:${site.email.hello}`} className="btn-primary">
              {site.email.hello}
            </a>
            <span className="font-mono text-[0.7rem] uppercase tracking-hud text-ink-dim">
              We read everything that comes in.
            </span>
          </div>
        </Panel>
      </section>
    </>
  );
}
