import type { Metadata } from "next";
import Link from "next/link";
import { biz, bizRoutes, capabilities, pipeline } from "../../business";

const description =
  "What we build: private knowledge systems, MCP servers, agents and automation, machine learning, full-stack products, data engineering, managed hosting, and internal tools.";

export const metadata: Metadata = {
  title: "Services",
  description,
  alternates: { canonical: bizRoutes.services },
  openGraph: {
    title: `Services | ${biz.name}`,
    description,
    url: `${biz.url}/services/`,
    type: "article",
  },
};

/**
 * Colour tokens per block. The page alternates cream and night, so every
 * element needs both readings. Written out as literal class strings rather than
 * composed at runtime, so Tailwind can see all of them.
 */
const CREAM = {
  bg: "bg-paper",
  num: "text-brand",
  title: "text-ink-ink",
  tag: "text-ink-soft",
  plain: "text-ink-ink",
  label: "text-ink-soft",
  body: "text-ink-body",
  hair: "bg-paper-4",
  rule: "border-paper-3",
  dotGet: "bg-brand",
  summary: "text-ink-soft hover:text-brand",
  meta: "text-ink-ink",
};

const NIGHT = {
  bg: "bg-night",
  num: "text-brand-soft",
  title: "text-paper",
  tag: "text-paper/50",
  plain: "text-paper",
  label: "text-paper/45",
  body: "text-paper/70",
  hair: "bg-white/20",
  rule: "border-white/15",
  dotGet: "bg-brand-soft",
  summary: "text-paper/50 hover:text-brand-soft",
  meta: "text-paper",
};

export default function ServicesPage() {
  return (
    <div className="w-full">
      {/* ---- Masthead ---------------------------------------------------- */}
      <section className="px-6 pb-12 pt-14 sm:px-10 sm:pt-20">
        <div className="flex flex-wrap items-end justify-between gap-6">
          <h1 className="biz-display text-[clamp(2.6rem,8vw,6.5rem)]">
            What we
            <br />
            actually do
            <span className="ml-3 inline-block h-[0.14em] w-[0.14em] rounded-full bg-brand align-baseline" />
          </h1>
          <p className="font-mono text-[0.72rem] uppercase tracking-label text-ink-soft">
            {String(capabilities.length).padStart(2, "0")} practices
          </p>
        </div>

        <p className="biz-lead mt-10 max-w-xl">
          Eight practices. Most projects use two or three of them, and the honest answer to which
          ones you need usually comes out of a short call rather than a page like this.
        </p>
      </section>

      {/* ---- Index -------------------------------------------------------
          A specimen table rather than a jump list: the whole row is the
          target, and hovering it inverts and swaps the tag for plain words. */}
      <nav aria-label="Practices" className="border-t border-ink-ink">
        <ul>
          {capabilities.map((c) => (
            <li key={c.id} className="border-b border-paper-4">
              <a
                href={`#${c.id}`}
                className="group grid grid-cols-[2.5rem_minmax(0,1fr)_auto] items-center gap-x-5 px-6 py-4 transition-colors hover:bg-night sm:px-10 lg:grid-cols-[3rem_minmax(0,17rem)_minmax(0,1fr)_auto] lg:gap-x-8"
              >
                <span className="font-mono text-[0.72rem] text-ink-soft transition-colors group-hover:text-brand-soft">
                  {c.n}
                </span>
                <span className="font-grotesk text-[1.05rem] font-bold uppercase leading-tight tracking-tight2 text-ink-ink transition-colors group-hover:text-paper sm:text-[1.2rem]">
                  {c.title.join(" ")}
                </span>

                {/* Tag and plain words occupy one cell, so nothing reflows. */}
                <span className="hidden lg:grid">
                  <span className="[grid-area:1/1] self-center font-mono text-[0.68rem] uppercase tracking-label text-ink-soft transition-opacity duration-200 group-hover:opacity-0">
                    {c.tag}
                  </span>
                  <span className="[grid-area:1/1] self-center font-sans text-[0.8rem] leading-[1.4] text-paper/70 opacity-0 transition-opacity duration-200 group-hover:opacity-100">
                    {c.plain}
                  </span>
                </span>

                <span
                  aria-hidden="true"
                  className="font-mono text-[0.9rem] text-ink-soft transition-all group-hover:translate-x-1 group-hover:text-brand-soft"
                >
                  →
                </span>
              </a>
            </li>
          ))}
        </ul>
      </nav>

      {/* ---- The three stages every engagement moves through -------------- */}
      <section className="px-6 py-14 sm:px-10">
        <ul className="grid gap-8 sm:grid-cols-3">
          {pipeline.map((stage) => (
            <li key={stage.id}>
              <div className="flex items-center gap-2.5">
                <span aria-hidden="true" className="h-2 w-2 border border-ink-ink" />
                <h2 className="biz-label !text-ink-ink">{stage.title}</h2>
              </div>
              <p className="mt-2 font-mono text-[0.8rem] leading-relaxed text-ink-body">
                {stage.body}
              </p>
            </li>
          ))}
        </ul>
      </section>

      {/* ---- The eight, alternating ground -------------------------------- */}
      {capabilities.map((c, i) => {
        const dark = i % 2 === 1;
        const t = dark ? NIGHT : CREAM;
        /* The pitch lands twice on the page, not eight times. */
        const showCta = i === 3;

        return (
          <article
            key={c.id}
            id={c.id}
            className={`scroll-mt-24 px-6 py-16 sm:px-10 sm:py-20 ${t.bg}`}
          >
            <div className="lg:grid lg:grid-cols-[minmax(0,6rem)_minmax(0,1fr)] lg:gap-x-10">
              {/* The number pins for as long as its own section is on screen. */}
              <div className="hidden lg:block">
                <span
                  className={`sticky top-28 block font-grotesk text-[4rem] font-black leading-none tracking-tight3 ${t.num}`}
                >
                  {c.n}
                </span>
              </div>

              <div>
                <h2
                  className={`font-grotesk text-[clamp(2rem,6.2vw,4.6rem)] font-black uppercase leading-[0.88] tracking-tight3 ${t.title}`}
                >
                  {c.title[0]} {c.title[1]}
                </h2>

                <div className="mt-6 flex flex-wrap items-center justify-between gap-4">
                  <p className={`font-mono text-[0.7rem] uppercase tracking-label ${t.tag}`}>
                    <span className="lg:hidden">{c.n} / </span>
                    {c.tag}
                  </p>
                  {c.id === "ops" ? (
                    <Link
                      href={bizRoutes.hosting}
                      className={`font-mono text-[0.7rem] uppercase tracking-label underline underline-offset-[6px] transition-colors ${t.num}`}
                    >
                      Hosting detail ↗
                    </Link>
                  ) : null}
                </div>

                <div className={`biz-rule-draw mt-6 ${t.hair}`} />

                <p
                  className={`mt-8 max-w-prose2 font-sans text-[1.15rem] leading-[1.5] ${t.plain}`}
                >
                  {c.plain}
                </p>

                <div className="mt-10 grid gap-8 sm:grid-cols-2 lg:gap-12">
                  <div>
                    <h3 className={`biz-label ${t.label}`}>You would want this if</h3>
                    <ul className="mt-4 space-y-2.5">
                      {c.scenarios.map((sc) => (
                        <li
                          key={sc}
                          className={`flex gap-3 font-sans text-[0.9rem] leading-relaxed ${t.body}`}
                        >
                          <span aria-hidden="true" className="mt-2 h-1.5 w-1.5 shrink-0 bg-ember" />
                          {sc}
                        </li>
                      ))}
                    </ul>
                  </div>

                  <div>
                    <h3 className={`biz-label ${t.label}`}>What you get</h3>
                    <ul className="mt-4 space-y-2.5">
                      {c.delivers.map((d) => (
                        <li
                          key={d}
                          className={`flex gap-3 font-sans text-[0.9rem] leading-relaxed ${t.body}`}
                        >
                          <span
                            aria-hidden="true"
                            className={`mt-2 h-1.5 w-1.5 shrink-0 ${t.dotGet}`}
                          />
                          {d}
                        </li>
                      ))}
                    </ul>
                  </div>
                </div>

                {/* For the technical reader, kept quiet so it does not lead. */}
                <details className={`mt-9 border-t pt-4 ${t.rule}`}>
                  <summary
                    className={`cursor-pointer font-mono text-[0.7rem] uppercase tracking-label transition-colors ${t.summary}`}
                  >
                    Technical detail
                  </summary>
                  <p
                    className={`mt-4 max-w-prose2 font-mono text-[0.84rem] leading-[1.7] ${t.body}`}
                  >
                    {c.blurb}
                  </p>
                  <dl className="mt-4 grid gap-x-10 gap-y-2 sm:grid-cols-2">
                    {c.meta.map((m) => (
                      <div key={m.label} className={`flex gap-3 border-b py-2 ${t.rule}`}>
                        <dt
                          className={`w-24 shrink-0 font-mono text-[0.66rem] uppercase tracking-label ${t.label}`}
                        >
                          {m.label}
                        </dt>
                        <dd className={`font-mono text-[0.78rem] ${t.meta}`}>{m.value}</dd>
                      </div>
                    ))}
                  </dl>
                </details>

                {showCta ? (
                  <div className="mt-10 flex flex-wrap items-center gap-x-6 gap-y-3">
                    <Link
                      href={bizRoutes.contact}
                      className="biz-btn !border-brand !bg-brand !px-5 !py-3 !text-white hover:!border-brand-soft hover:!bg-brand-soft"
                    >
                      {biz.railCta} ↗
                    </Link>
                    <a
                      href={`tel:${biz.phoneHref}`}
                      className={`font-mono text-[0.78rem] transition-colors ${t.body}`}
                    >
                      or call {biz.phone}
                    </a>
                  </div>
                ) : null}
              </div>
            </div>
          </article>
        );
      })}

      {/* ---- Close -------------------------------------------------------- */}
      <section className="px-6 py-16 sm:px-10 sm:py-20">
        <div className="border border-ink-ink p-9 sm:p-12">
          <h2 className="biz-h2">Not sure which of these you need?</h2>
          <p className="mt-5 max-w-prose2 font-sans text-[1rem] leading-[1.65] text-ink-body">
            Most people are not, and that is fine. Describe the problem in plain words and we will
            tell you which of these applies, or that none of them do and what to do instead.
          </p>
          <div className="mt-8 flex flex-wrap items-center gap-x-6 gap-y-3">
            <Link href={bizRoutes.contact} className="biz-btn">
              Start a conversation
            </Link>
            <a
              href={`tel:${biz.phoneHref}`}
              className="font-mono text-[0.78rem] text-ink-body transition-colors hover:text-brand"
            >
              or call {biz.phone}
            </a>
          </div>
        </div>
      </section>
    </div>
  );
}
