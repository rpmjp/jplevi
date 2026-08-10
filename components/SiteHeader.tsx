import Link from "next/link";
import { Wordmark } from "@/components/Wordmark";
import { divisions, site } from "@/app/site";

export function SiteHeader() {
  return (
    <header className="sticky top-0 z-40 border-b border-gun-500/70 bg-gun-900/85 backdrop-blur supports-[backdrop-filter]:bg-gun-900/70">
      <div className="mx-auto flex max-w-shell flex-wrap items-center gap-x-8 gap-y-3 px-5 py-4 sm:px-8">
        <Wordmark />

        <nav aria-label="Sections" className="order-3 w-full sm:order-2 sm:w-auto">
          <ul className="flex flex-wrap items-center gap-x-6 gap-y-2">
            {divisions.map((division) =>
              division.status === "live" && division.href ? (
                <li key={division.id}>
                  <Link
                    href={division.href}
                    className="font-mono text-[0.7rem] uppercase tracking-hud text-ink-muted transition-colors hover:text-hud"
                  >
                    {division.name}
                  </Link>
                </li>
              ) : (
                <li key={division.id}>
                  <span
                    className="font-mono text-[0.7rem] uppercase tracking-hud text-ink-dim/70"
                    title={`${division.name} — in development`}
                  >
                    {division.name}
                    <span className="sr-only"> (in development)</span>
                  </span>
                </li>
              ),
            )}
          </ul>
        </nav>

        <a
          href={`mailto:${site.email.hello}`}
          className="order-2 ml-auto font-mono text-[0.7rem] uppercase tracking-hud text-ink-muted transition-colors hover:text-signal sm:order-3"
        >
          Contact
        </a>
      </div>
      <div className="h-px w-full bg-gradient-to-r from-transparent via-hud/30 to-transparent" />
    </header>
  );
}
