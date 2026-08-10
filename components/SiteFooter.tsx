import Link from "next/link";
import { Wordmark } from "@/components/Wordmark";
import { divisions, site, statusLabel } from "@/app/site";

export function SiteFooter() {
  // Evaluated at build time (server component, static export).
  const year = new Date().getFullYear();

  return (
    <footer className="mt-24 border-t border-gun-500/70 bg-gun-900">
      <div className="mx-auto grid max-w-shell gap-10 px-5 py-14 sm:px-8 md:grid-cols-[minmax(0,1.4fr)_minmax(0,1fr)_minmax(0,1fr)]">
        <div>
          <Wordmark />
          <p className="mt-4 max-w-sm text-sm leading-relaxed text-ink-muted">{site.tagline}</p>
          <p className="mt-4 font-mono text-[0.7rem] uppercase tracking-hud text-ink-dim">
            {site.legalName} · {site.state}, USA
          </p>
        </div>

        <nav aria-label="Sections">
          <h2 className="hud-label">Sections</h2>
          <div className="hud-rule mt-3" />
          <ul className="mt-4 space-y-2.5 text-sm">
            {divisions.map((division) => (
              <li key={division.id}>
                {division.status === "live" && division.href ? (
                  <Link href={division.href} className="link-quiet">
                    {division.name}
                  </Link>
                ) : (
                  <span className="text-ink-dim/80">
                    {division.name}
                    <span className="ml-2 font-mono text-[0.6rem] uppercase tracking-hud text-ink-dim/60">
                      {statusLabel[division.status]}
                    </span>
                  </span>
                )}
              </li>
            ))}
          </ul>
        </nav>

        <div>
          <h2 className="hud-label">Contact</h2>
          <div className="hud-rule mt-3" />
          <ul className="mt-4 space-y-2.5 text-sm">
            <li>
              <a
                href={`mailto:${site.email.hello}`}
                className="text-ink-muted transition-colors hover:text-signal"
              >
                {site.email.hello}
              </a>
            </li>
            <li className="text-ink-dim">{site.domain}</li>
          </ul>
        </div>
      </div>

      <div className="border-t border-gun-500/50">
        <div className="mx-auto flex max-w-shell flex-wrap items-center justify-between gap-3 px-5 py-5 sm:px-8">
          <p className="font-mono text-[0.65rem] uppercase tracking-hud text-ink-dim">
            © {year} {site.legalName}. All rights reserved.
          </p>
          <p className="font-mono text-[0.65rem] uppercase tracking-hud text-ink-dim/70">
            Built in {site.state}
          </p>
        </div>
      </div>
    </footer>
  );
}
