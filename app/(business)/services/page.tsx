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

export default function ServicesPage() {
  return (
    <section className="w-full px-6 pb-16 pt-14 sm:px-10 sm:pt-20">
      <div className="flex flex-wrap items-end justify-between gap-6">
        <h1 className="biz-display text-[2.6rem] sm:text-[4rem]">
          What we actually do
          <span className="ml-2 inline-block h-[0.2em] w-[0.2em] rounded-full bg-brand align-baseline" />
        </h1>
        <p className="font-mono text-[0.72rem] uppercase tracking-label text-ink-soft">
          {String(capabilities.length).padStart(2, "0")} practices
        </p>
      </div>

      <p className="biz-lead mt-8 max-w-xl">
        Eight practices. Most projects use two or three of them, and the honest answer to which
        ones you need usually comes out of a short call rather than a page like this.
      </p>

      {/* jump list, so a prospect can be sent straight to one */}
      <nav aria-label="Jump to a service" className="mt-10 border-y border-paper-3 py-4">
        <ul className="flex flex-wrap gap-x-6 gap-y-2">
          {capabilities.map((c) => (
            <li key={c.id}>
              <a
                href={`#${c.id}`}
                className="font-mono text-[0.74rem] text-ink-body transition-colors hover:text-brand"
              >
                <span className="text-ink-soft">{c.n}</span> {c.title.join(" ")}
              </a>
            </li>
          ))}
        </ul>
      </nav>

      {/* the three stages every engagement moves through */}
      <ul className="mt-12 grid gap-8 sm:grid-cols-3">
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

      <div className="mt-16 space-y-0">
        {capabilities.map((c) => (
          <article
            key={c.id}
            id={c.id}
            className="grid scroll-mt-24 gap-8 border-t border-paper-4 py-12 lg:grid-cols-[minmax(0,0.8fr)_minmax(0,1.6fr)]"
          >
            <div>
              <span className="font-mono text-[0.72rem] text-brand">{c.n}</span>
              <h2 className="biz-h2 mt-3">
                {c.title[0]} {c.title[1]}
              </h2>
              <p className="mt-3 font-mono text-[0.7rem] uppercase tracking-label text-ink-soft">
                {c.tag}
              </p>
              {c.id === "ops" ? (
                <Link href={bizRoutes.hosting} className="biz-link mt-5 inline-block !text-[0.7rem]">
                  Hosting detail ↗
                </Link>
              ) : null}
            </div>

            <div>
              {/* Plain words first. The technical framing is available below it. */}
              <p className="max-w-prose2 font-sans text-[1.05rem] leading-[1.6] text-ink-ink">
                {c.plain}
              </p>

              <div className="mt-7 grid gap-7 sm:grid-cols-2">
                <div>
                  <h3 className="biz-label">You would want this if</h3>
                  <ul className="mt-3 space-y-2">
                    {c.scenarios.map((sc) => (
                      <li
                        key={sc}
                        className="flex gap-2.5 font-sans text-[0.9rem] leading-relaxed text-ink-body"
                      >
                        <span aria-hidden="true" className="mt-2 h-1.5 w-1.5 shrink-0 bg-ember" />
                        {sc}
                      </li>
                    ))}
                  </ul>
                </div>

                <div>
                  <h3 className="biz-label">What you get</h3>
                  <ul className="mt-3 space-y-2">
                    {c.delivers.map((d) => (
                      <li
                        key={d}
                        className="flex gap-2.5 font-sans text-[0.9rem] leading-relaxed text-ink-body"
                      >
                        <span aria-hidden="true" className="mt-2 h-1.5 w-1.5 shrink-0 bg-brand" />
                        {d}
                      </li>
                    ))}
                  </ul>
                </div>
              </div>

              {/* For the technical reader, kept quiet so it does not lead. */}
              <details className="mt-7 border-t border-paper-3 pt-4">
                <summary className="cursor-pointer font-mono text-[0.7rem] uppercase tracking-label text-ink-soft transition-colors hover:text-brand">
                  Technical detail
                </summary>
                <p className="mt-4 max-w-prose2 font-mono text-[0.84rem] leading-[1.7] text-ink-body">
                  {c.blurb}
                </p>
                <dl className="mt-4 grid gap-x-8 gap-y-2 sm:grid-cols-2">
                  {c.meta.map((m) => (
                    <div key={m.label} className="flex gap-3 border-b border-paper-3 py-2">
                      <dt className="w-24 shrink-0 font-mono text-[0.66rem] uppercase tracking-label text-ink-soft">
                        {m.label}
                      </dt>
                      <dd className="font-mono text-[0.78rem] text-ink-ink">{m.value}</dd>
                    </div>
                  ))}
                </dl>
              </details>

              <div className="mt-6 flex flex-wrap items-center gap-x-6 gap-y-3">
                <Link href={bizRoutes.contact} className="biz-btn !px-5 !py-3">
                  {biz.railCta} ↗
                </Link>
                <a
                  href={`tel:${biz.phoneHref}`}
                  className="font-mono text-[0.78rem] text-ink-body transition-colors hover:text-brand"
                >
                  or call {biz.phone}
                </a>
              </div>
            </div>
          </article>
        ))}
      </div>

      <div className="mt-8 border border-ink-ink p-9 sm:p-12">
        <h2 className="biz-h2">Not sure which of these you need?</h2>
        <p className="mt-5 max-w-prose2 font-sans text-[1rem] leading-[1.65] text-ink-body">
          Most people are not, and that is fine. Describe the problem in plain words and we will
          tell you which of these applies, or that none of them do and what to do instead.
        </p>
        <Link href={bizRoutes.contact} className="biz-btn mt-8">
          Start a conversation
        </Link>
      </div>
    </section>
  );
}
