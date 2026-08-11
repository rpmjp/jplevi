import Link from "next/link";
import { game, routes, sectionNav } from "@/app/mechablast/game";

export function MechaHeader() {
  return (
    <header className="sticky top-0 z-40 border-b-2 border-mecha-line bg-mecha-void/95 backdrop-blur supports-[backdrop-filter]:bg-mecha-void/80">
      <div className="mx-auto flex max-w-shell flex-wrap items-center gap-x-8 gap-y-3 px-5 py-4 sm:px-8">
        <Link
          href={routes.home}
          aria-label={`${game.name} home`}
          className="font-display text-lg font-extrabold uppercase tracking-wordmark text-ink"
        >
          Mecha<span className="text-mecha-cyan">Blast</span>
        </Link>

        <nav aria-label={`${game.name} section`} className="order-3 w-full sm:order-2 sm:w-auto">
          <ul className="flex flex-wrap items-center gap-x-6 gap-y-2">
            {sectionNav.map((item) => (
              <li key={item.href}>
                <Link
                  href={item.href}
                  className="font-mono text-[0.7rem] uppercase tracking-hud text-ink-muted transition-colors hover:text-mecha-cyan"
                >
                  {item.label}
                </Link>
              </li>
            ))}
          </ul>
        </nav>

        <Link
          href="/"
          className="order-2 ml-auto font-mono text-[0.7rem] uppercase tracking-hud text-ink-dim transition-colors hover:text-ink sm:order-3"
        >
          {game.brand}
        </Link>
      </div>
    </header>
  );
}
