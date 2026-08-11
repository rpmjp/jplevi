import Link from "next/link";
import { game, routes } from "@/app/mechablast/game";

const legalLinks = [
  { href: routes.privacy, label: "Privacy" },
  { href: routes.terms, label: "Terms" },
  { href: routes.support, label: "Support" },
];

export function MechaFooter() {
  return (
    <footer className="mt-20 border-t-2 border-mecha-line bg-mecha-void">
      <div className="mx-auto flex max-w-shell flex-col gap-6 px-5 py-10 sm:px-8 md:flex-row md:items-center md:justify-between">
        <div>
          <p className="font-display text-base font-extrabold uppercase tracking-wordmark text-ink">
            Mecha<span className="text-mecha-cyan">Blast</span>
          </p>
          <p className="mt-2 font-mono text-[0.7rem] uppercase tracking-hud text-ink-dim">
            {game.copyright}
          </p>
        </div>

        <nav aria-label="Legal and support">
          <ul className="flex flex-wrap items-center gap-x-6 gap-y-2">
            {legalLinks.map((item) => (
              <li key={item.href}>
                <Link
                  href={item.href}
                  className="font-mono text-[0.7rem] uppercase tracking-hud text-ink-muted transition-colors hover:text-mecha-cyan"
                >
                  {item.label}
                </Link>
              </li>
            ))}
            <li>
              <a
                href={`mailto:${game.supportEmail}`}
                className="font-mono text-[0.7rem] uppercase tracking-hud text-ink-muted transition-colors hover:text-mecha-cyan"
              >
                {game.supportEmail}
              </a>
            </li>
          </ul>
        </nav>
      </div>
    </footer>
  );
}
