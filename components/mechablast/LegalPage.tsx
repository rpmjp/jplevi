import Link from "next/link";
import { Markdown } from "./Markdown";
import { game, routes } from "@/app/mechablast/game";

/**
 * Shell for the two legal documents. The document's own markdown supplies every
 * heading, including the <h1> — nothing here rewrites or re-titles the text.
 */
export function LegalPage({ eyebrow, source }: { eyebrow: string; source: string }) {
  return (
    <article className="mx-auto max-w-shell px-5 pb-20 pt-14 sm:px-8 sm:pt-20">
      <nav aria-label="Breadcrumb" className="mb-10">
        <Link
          href={routes.home}
          className="font-mono text-[0.7rem] uppercase tracking-hud text-ink-dim transition-colors hover:text-mecha-cyan"
        >
          ← {game.name}
        </Link>
      </nav>

      <p className="cel-eyebrow">{eyebrow}</p>

      <div className="prose prose-invert prose-mecha mt-6 max-w-[72ch] prose-headings:font-display prose-a:break-words">
        <Markdown>{source}</Markdown>
      </div>

      <div className="mt-16 max-w-[72ch] border-t-2 border-mecha-line pt-8">
        <p className="text-sm text-ink-muted">
          Questions about this document? Email{" "}
          <a href={`mailto:${game.supportEmail}`} className="cel-link">
            {game.supportEmail}
          </a>
          .
        </p>
        <div className="mt-5 flex flex-wrap gap-x-6 gap-y-2">
          <Link href={routes.privacy} className="font-mono text-[0.7rem] uppercase tracking-hud text-ink-dim hover:text-mecha-cyan">
            Privacy
          </Link>
          <Link href={routes.terms} className="font-mono text-[0.7rem] uppercase tracking-hud text-ink-dim hover:text-mecha-cyan">
            Terms
          </Link>
          <Link href={routes.support} className="font-mono text-[0.7rem] uppercase tracking-hud text-ink-dim hover:text-mecha-cyan">
            Support
          </Link>
        </div>
      </div>
    </article>
  );
}
