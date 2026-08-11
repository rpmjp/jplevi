import type { Metadata } from "next";
import Link from "next/link";
import { game, routes } from "../game";

const description = `Get help with ${game.name}: contact support, report a bug, or read the privacy policy and terms.`;

export const metadata: Metadata = {
  title: "Support",
  description,
  openGraph: {
    title: `${game.name}: Support`,
    description,
    images: [{ url: game.ogImage, width: 1200, height: 630, alt: `${game.name} key art` }],
    type: "article",
  },
};

const helpfulDetails = [
  "Your device model and OS version",
  "The game version (Settings → About)",
  "What happened, and what you expected instead",
  "A screenshot or screen recording if you have one",
];

export default function SupportPage() {
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

      <p className="cel-eyebrow">Support</p>
      <h1 className="mt-5 font-display text-4xl font-extrabold uppercase tracking-tight text-ink sm:text-5xl">
        Need a hand?
      </h1>
      <p className="mt-6 max-w-[72ch] text-base leading-relaxed text-ink-muted">
        Email us and a human will read it: bug reports, account-free progress problems, purchase
        trouble, or privacy requests.
      </p>

      <div className="mt-10">
        <a href={`mailto:${game.supportEmail}`} className="cel-btn-primary">
          {game.supportEmail}
        </a>
      </div>

      {/* ---- What to include ------------------------------------------ */}
      <div className="mt-16 grid max-w-5xl gap-6 md:grid-cols-2">
        <div className="cel-panel p-7">
          <h2 className="font-display text-xl font-bold tracking-tight text-ink">
            What to include
          </h2>
          <p className="mt-3 text-sm leading-relaxed text-ink-muted">
            The more of this you send, the faster we can reproduce the problem:
          </p>
          <ul className="mt-5 space-y-2.5">
            {helpfulDetails.map((detail) => (
              <li key={detail} className="flex gap-3 text-sm text-ink-muted">
                <span aria-hidden="true" className="mt-2 h-1.5 w-1.5 shrink-0 bg-mecha-cyan" />
                {detail}
              </li>
            ))}
          </ul>
        </div>

        <div className="cel-panel p-7">
          <h2 className="font-display text-xl font-bold tracking-tight text-ink">
            Purchases and refunds
          </h2>
          <p className="mt-3 text-sm leading-relaxed text-ink-muted">
            Purchases and refunds are handled by the app store you bought from (Apple&rsquo;s App
            Store or Google Play), not by {game.publisher} We cannot issue refunds or see your
            payment details.
          </p>
          <p className="mt-4 text-sm leading-relaxed text-ink-muted">
            To request a refund, or to restore a purchase such as &ldquo;Remove Ads&rdquo;, use your
            store&rsquo;s purchase history. If a purchase completed but the item never arrived in
            game, email us and we will help sort it out.
          </p>
        </div>

        <div className="cel-panel p-7 md:col-span-2">
          <h2 className="font-display text-xl font-bold tracking-tight text-ink">
            Before anything formal
          </h2>
          <p className="mt-3 max-w-[72ch] text-sm leading-relaxed text-ink-muted">
            Under section 20 of the{" "}
            <Link href={routes.terms} className="cel-link">
              Terms of Service
            </Link>
            , we ask that you contact us at{" "}
            <a href={`mailto:${game.supportEmail}`} className="cel-link">
              {game.supportEmail}
            </a>{" "}
            and give us 30 days to resolve a dispute informally before starting any formal
            proceeding. Refund and charge disputes are usually fastest through the app store.
          </p>
        </div>
      </div>

      {/* ---- Legal links ---------------------------------------------- */}
      <div className="mt-16 max-w-[72ch] border-t-2 border-mecha-line pt-8">
        <h2 className="cel-eyebrow">Legal</h2>
        <ul className="mt-5 space-y-3 text-sm">
          <li>
            <Link href={routes.privacy} className="cel-link">
              Privacy Policy
            </Link>
            <span className="ml-3 text-ink-dim">
              What we collect, why, and how to make a request.
            </span>
          </li>
          <li>
            <Link href={routes.terms} className="cel-link">
              Terms of Service
            </Link>
            <span className="ml-3 text-ink-dim">The licence covering your use of the game.</span>
          </li>
        </ul>
      </div>
    </section>
  );
}
