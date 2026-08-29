import type { Metadata } from "next";
import Link from "next/link";
import { biz, bizRoutes } from "../../business";

const description =
  "Selected work from JP LEVI INC.: shipped products, the systems behind them, and the engagements available to talk about.";

export const metadata: Metadata = {
  title: "Work",
  description,
  alternates: { canonical: bizRoutes.work },
  openGraph: { title: `Work | ${biz.name}`, description, url: `${biz.url}/work/`, type: "article" },
};

/** Only things that genuinely exist. Client engagements get added as they ship. */
const shipped = [
  {
    id: "mechablast",
    kind: "Product · Mobile game",
    title: "MechaBlast",
    body: "A cel-shaded run-and-gun platformer for iOS and Android. Built and published end to end under JP LEVI INC.: gameplay, art pipeline, economy, ads and IAP integration, store compliance, and the legal documents behind it.",
    notes: ["Flutter · Dart", "Google AdMob · consent flow", "Opt-in, anonymous analytics", "8 worlds, no loot boxes"],
    href: "/mechablast/",
    linkLabel: "Visit the game site ↗",
  },
  {
    id: "jplevi",
    kind: "Product · Web",
    title: "jplevi.com",
    body: "This site. A static Next.js export deployed to shared hosting through GitHub Actions, with two deliberately separate design systems: the business side you are reading and the dark game section it links to.",
    notes: ["Next.js · TypeScript · Tailwind", "Static export, no runtime", "CI build gate + auto-deploy", "Legal pages rendered verbatim from markdown"],
    href: "/mechablast/",
    linkLabel: "See the other half ↗",
  },
];

export default function WorkPage() {
  return (
    <section className="mx-auto max-w-biz px-5 pb-16 pt-14 sm:px-8 sm:pt-20">
      <p className="biz-label-blue">Work</p>
      <h1 className="biz-display mt-5 text-[2.6rem] sm:text-[4rem]">
        Things that shipped
        <span className="ml-1 inline-block h-[0.2em] w-[0.2em] rounded-full bg-brand align-baseline" />
      </h1>
      <p className="biz-lead mt-8 max-w-xl">
        This studio is newly open for client work, so this page is short and honest rather than
        padded. What is here is real and running.
      </p>

      <div className="mt-16 space-y-14">
        {shipped.map((item, i) => (
          <article key={item.id} className="grid gap-8 border-t border-paper-3 pt-10 lg:grid-cols-[minmax(0,1fr)_minmax(0,1.4fr)]">
            <div>
              <span className="font-mono text-[0.7rem] text-ink-soft">
                {String(i + 1).padStart(2, "0")}
              </span>
              <h2 className="biz-h2 mt-3">{item.title}</h2>
              <p className="mt-3 biz-label">{item.kind}</p>
            </div>
            <div>
              <p className="font-mono text-[0.92rem] leading-[1.8] text-ink-body">{item.body}</p>
              <ul className="mt-6 flex flex-wrap gap-2.5">
                {item.notes.map((n) => (
                  <li key={n} className="border border-paper-3 px-3 py-1.5 font-mono text-[0.72rem] text-ink-body">
                    {n}
                  </li>
                ))}
              </ul>
              <Link href={item.href} className="biz-link mt-6 inline-block">
                {item.linkLabel}
              </Link>
            </div>
          </article>
        ))}
      </div>

      <div className="mt-16 border-t border-paper-3 pt-10">
        <h2 className="biz-h2">Client engagements</h2>
        <p className="mt-5 max-w-prose2 font-mono text-[0.9rem] leading-[1.8] text-ink-body">
          Client systems are covered by their own agreements, so they are not written up here.
          If you want to see comparable work before hiring us, ask on a call and we will walk you
          through architecture and decisions in as much detail as the agreement allows.
        </p>
        <Link href={bizRoutes.contact} className="biz-btn mt-8">
          Ask about relevant work
        </Link>
      </div>
    </section>
  );
}
