import type { Metadata } from "next";
import Link from "next/link";
import { features, game, routes, screenshots, stores } from "./game";

const description = `${game.pitch} ${game.blurb}`;

export const metadata: Metadata = {
  title: {
    absolute: `${game.name}: ${game.pitch}`,
  },
  description,
  openGraph: {
    title: `${game.name}: ${game.pitch}`,
    description,
    images: [{ url: game.ogImage, width: 1200, height: 630, alt: `${game.name} key art` }],
    type: "website",
  },
  twitter: {
    card: "summary_large_image",
    title: `${game.name}: ${game.pitch}`,
    description,
    images: [game.ogImage],
  },
};

export default function MechaBlastHome() {
  return (
    <>
      {/* ---- Hero ---------------------------------------------------- */}
      <section className="mx-auto max-w-shell px-5 pb-16 pt-16 sm:px-8 sm:pt-24">
        <p className="cel-eyebrow">
          {game.publisher} · {game.brand}
        </p>

        <h1 className="mt-5 font-display text-5xl font-extrabold uppercase leading-[0.95] tracking-tight text-ink sm:text-7xl lg:text-8xl">
          Mecha<span className="text-mecha-cyan">Blast</span>
        </h1>

        <p className="mt-6 max-w-2xl font-display text-xl font-medium leading-snug text-ink sm:text-2xl">
          {game.pitch}
        </p>
        <p className="mt-4 max-w-2xl text-base leading-relaxed text-ink-muted">{game.blurb}</p>

        <div className="mt-10 flex flex-wrap items-center gap-5">
          {stores.map((store) => (
            <a key={store.id} href={store.href} className="cel-btn-primary">
              {store.label}
            </a>
          ))}
        </div>

        <p className="mt-6 font-mono text-[0.7rem] uppercase tracking-hud text-ink-dim">
          Free to play · Optional in-app purchases · Contains ads · No loot boxes
        </p>
      </section>

      {/* ---- What it is ---------------------------------------------- */}
      <section
        aria-labelledby="about-heading"
        className="mx-auto max-w-shell px-5 py-16 sm:px-8"
      >
        <div className="cel-panel p-8 sm:p-12">
          <p className="cel-eyebrow">What it is</p>
          <h2
            id="about-heading"
            className="mt-4 max-w-3xl font-display text-2xl font-bold leading-snug tracking-tight text-ink sm:text-3xl"
          >
            Blast robots, beat bosses across {game.worlds} worlds, earn scrap, upgrade your mech.
          </h2>
          <p className="mt-5 max-w-2xl text-base leading-relaxed text-ink-muted">
            {game.name} is a cel-shaded run-and-gun platformer built for landscape play on phones and
            tablets. It is a single-player game, free to play, with optional in-app purchases. Ads
            are served by Google AdMob: full-screen interstitials at natural breaks between levels,
            plus optional rewarded ads you choose to watch. A one-time &ldquo;Remove Ads&rdquo;
            purchase turns the interstitials off. There are no loot boxes and no randomised paid
            rewards.
          </p>
          <p className="mt-4 max-w-2xl text-sm leading-relaxed text-ink-muted">
            Full detail is in the{" "}
            <Link href={routes.privacy} className="cel-link">
              Privacy Policy
            </Link>{" "}
            and{" "}
            <Link href={routes.terms} className="cel-link">
              Terms of Service
            </Link>
            .
          </p>
        </div>
      </section>

      {/* ---- Features ------------------------------------------------ */}
      <section
        aria-labelledby="features-heading"
        className="mx-auto max-w-shell px-5 py-16 sm:px-8"
      >
        <p className="cel-eyebrow">Features</p>
        <h2
          id="features-heading"
          className="mt-4 font-display text-3xl font-bold uppercase tracking-tight text-ink sm:text-4xl"
        >
          Built to be played, not farmed.
        </h2>

        <ul className="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
          {features.map((feature, i) => (
            <li key={feature.title} className="cel-panel p-6">
              <span className="font-mono text-[0.7rem] tracking-hud text-mecha-cyan">
                {String(i + 1).padStart(2, "0")}
              </span>
              <h3 className="mt-4 font-display text-lg font-bold tracking-tight text-ink">
                {feature.title}
              </h3>
              <p className="mt-2 text-sm leading-relaxed text-ink-muted">{feature.body}</p>
            </li>
          ))}
        </ul>
      </section>

      {/* ---- Screenshots --------------------------------------------- */}
      <section
        aria-labelledby="shots-heading"
        className="mx-auto max-w-shell px-5 py-16 sm:px-8"
      >
        <p className="cel-eyebrow">Screenshots</p>
        <h2
          id="shots-heading"
          className="mt-4 font-display text-3xl font-bold uppercase tracking-tight text-ink sm:text-4xl"
        >
          In motion.
        </h2>

        <div className="mt-10 grid gap-6 sm:grid-cols-2">
          {screenshots.map((shot) => (
            // eslint-disable-next-line @next/next/no-img-element -- images.unoptimized is on; static export ships plain <img>.
            <img
              key={shot.src}
              src={shot.src}
              alt={shot.alt}
              width={1600}
              height={900}
              loading="lazy"
              decoding="async"
              className="h-auto w-full border-2 border-mecha-edge bg-mecha-panel shadow-cel"
            />
          ))}
        </div>
      </section>

      {/* ---- Trailer -------------------------------------------------- */}
      <section
        aria-labelledby="trailer-heading"
        className="mx-auto max-w-shell px-5 py-16 sm:px-8"
      >
        <p className="cel-eyebrow">Trailer</p>
        <h2
          id="trailer-heading"
          className="mt-4 font-display text-3xl font-bold uppercase tracking-tight text-ink sm:text-4xl"
        >
          Coming soon.
        </h2>

        <div className="mt-10 flex aspect-video w-full items-center justify-center border-2 border-dashed border-mecha-edge bg-mecha-panel">
          <p className="px-6 text-center font-mono text-[0.7rem] uppercase tracking-hud text-ink-dim">
            Trailer embed placeholder
          </p>
          {/*
            TODO: drop the trailer in here once it is cut. Keep it a plain
            <iframe> - no third-party script tags, so nothing loads until the
            user is on this page and nothing needs a runtime.

            <iframe
              className="h-full w-full"
              src="https://www.youtube-nocookie.com/embed/VIDEO_ID"
              title="MechaBlast announcement trailer"
              loading="lazy"
              allow="accelerometer; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
              allowFullScreen
            />
          */}
        </div>
      </section>

      {/* ---- Closing CTA ---------------------------------------------- */}
      <section className="mx-auto max-w-shell px-5 py-16 sm:px-8">
        <div className="cel-panel p-8 sm:p-12">
          <h2 className="max-w-2xl font-display text-2xl font-bold leading-snug tracking-tight text-ink sm:text-3xl">
            Suit up.
          </h2>
          <div className="mt-8 flex flex-wrap items-center gap-5">
            {stores.map((store) => (
              <a key={store.id} href={store.href} className="cel-btn-primary">
                {store.label}
              </a>
            ))}
            <Link href={routes.support} className="cel-btn-ghost">
              Support
            </Link>
          </div>
        </div>
      </section>
    </>
  );
}
