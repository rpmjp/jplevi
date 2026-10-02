import type { Metadata } from "next";
import Link from "next/link";
import {
  biz,
  bizRoutes,
  colophon,
  credentials,
  milestones,
  people,
  shipped,
} from "../../business";
import { site } from "@/app/site";

const description = `${biz.teamLine} ${site.legalName}, based in ${biz.location}.`;

export const metadata: Metadata = {
  title: "Company",
  description,
  alternates: { canonical: bizRoutes.about },
  openGraph: { title: `Company | ${biz.name}`, description, url: `${biz.url}/about/`, type: "profile" },
};

const principles = [
  {
    t: "Measure before believing",
    b: "Every retrieval system gets an evaluation set before it gets a launch date. If quality cannot be demonstrated, it is not done.",
  },
  {
    t: "The boring answer counts",
    b: "Plenty of problems labelled AI are really a database query, a scheduled job, or a form. We will tell you when that is the case.",
  },
  {
    t: "Own the whole stack",
    b: "Model, data, application, and server. Fewer seams means fewer places for a problem to hide between vendors.",
  },
  {
    t: "Leave documentation",
    b: "You should be able to end the engagement and keep running. Runbooks and handover are part of the work, not an upsell.",
  },
];

export default function CompanyPage() {
  const lead = people[0];
  /** Computed at build so the page does not quietly go stale. */
  const year = new Date().getFullYear();

  return (
    <div className="w-full">
      {/* ---- Masthead ---------------------------------------------------- */}
      <section className="px-6 pb-24 pt-14 sm:px-10 sm:pt-20">
        <p className="biz-label-blue">Company</p>
        <h1 className="biz-display mt-5 max-w-[18ch] text-[clamp(2.6rem,7.5vw,6rem)]">
          A small team that writes the code
          <span className="ml-3 inline-block h-[0.13em] w-[0.13em] rounded-full bg-brand align-baseline" />
        </h1>
      </section>

      {/* ---- Portrait band ------------------------------------------------
          The cutout breaks up out of the dark band into the cream above it.
          Hard edges, no shadow: it sits on the structure rather than floating. */}
      <section className="relative bg-night px-6 pb-16 pt-0 sm:px-10">
        <div className="lg:grid lg:grid-cols-[minmax(0,1fr)_minmax(0,22rem)] lg:items-end lg:gap-x-14">
          <div className="pt-16 lg:pt-24">
            <p className="max-w-prose2 font-sans text-[clamp(1.15rem,2.2vw,1.6rem)] leading-[1.45] text-paper">
              {lead.line}
            </p>

            <p className="mt-8 max-w-prose2 font-mono text-[0.9rem] leading-[1.8] text-paper/70">
              {site.legalName} has been a New Jersey corporation since {biz.founded}. It takes on a
              limited number of projects at a time and says no to work it is not the right fit for.
              The studio also publishes its own products, which keeps it honest about what is
              actually hard to operate rather than hard to demo.
            </p>

            <dl className="mt-10 flex flex-wrap gap-x-12 gap-y-5 border-t border-white/15 pt-7">
              {[
                ["Writing software", `${year - 1996} years`],
                ["In business", `${year - 2015} years`],
                ["In machine learning", `${year - 2020} years`],
              ].map(([k, v]) => (
                <div key={k}>
                  <dt className="font-mono text-[0.62rem] uppercase tracking-label text-paper/45">
                    {k}
                  </dt>
                  <dd className="mt-1.5 font-grotesk text-[1.6rem] font-black tracking-tight2 text-paper">
                    {v}
                  </dd>
                </div>
              ))}
            </dl>
          </div>

          {/* The portrait, and the specimen plate under it. */}
          <figure className="relative z-10 mt-12 lg:mt-0 lg:-translate-y-[10rem]">
            {lead.photo ? (
              <picture>
                <source srcSet={lead.photo} type="image/webp" />
                <img
                  src={lead.photoFallback ?? lead.photo}
                  alt={`${lead.name}, ${lead.role}`}
                  width={800}
                  height={800}
                  className="relative z-10 block w-full max-w-[22rem]"
                />
              </picture>
            ) : null}
            <figcaption className="relative z-10 border-t border-brand pt-4">
              <p className="font-grotesk text-[1.1rem] font-bold tracking-tight2 text-paper">
                {lead.name}
              </p>
              <p className="mt-1 font-mono text-[0.66rem] uppercase tracking-label text-paper/55">
                {lead.role} / {lead.location}
              </p>
            </figcaption>
          </figure>
        </div>
      </section>

      {/* ---- Milestones ---------------------------------------------------- */}
      <section className="px-6 py-16 sm:px-10 sm:py-20">
        <h2 className="biz-label">On the record</h2>
        <div className="biz-rule-draw mt-4 bg-paper-4" />
        <ol className="mt-10 grid gap-10 sm:grid-cols-2 lg:grid-cols-4">
          {milestones.map((m) => (
            <li key={m.year} className="border-t border-ink-ink pt-4">
              <span className="font-grotesk text-[clamp(2.2rem,4.5vw,3.4rem)] font-black leading-none tracking-tight3 text-brand">
                {m.year}
              </span>
              <p className="mt-3 font-mono text-[0.8rem] leading-relaxed text-ink-body">{m.label}</p>
            </li>
          ))}
        </ol>

        <dl className="mt-14 grid gap-x-10 gap-y-4 sm:grid-cols-3">
          {credentials.map((c) => (
            <div key={c.label} className="border-t border-paper-4 pt-4">
              <dt className="font-mono text-[0.62rem] uppercase tracking-label text-ink-soft">
                {c.label}
              </dt>
              <dd className="mt-2 font-grotesk text-[1.05rem] font-bold tracking-tight2 text-ink-ink">
                {c.value}
                {c.where ? <span className="text-brand"> / {c.where}</span> : null}
              </dd>
              {c.note ? (
                <p className="mt-1 font-mono text-[0.72rem] text-ink-soft">{c.note}</p>
              ) : null}
            </div>
          ))}
        </dl>
      </section>

      {/* ---- Shipped ------------------------------------------------------
          Renders only when there is something to show. */}
      {shipped.length > 0 ? (
        <section className="bg-night px-6 py-16 sm:px-10 sm:py-20">
          <h2 className="biz-label !text-paper/45">Shipped</h2>
          <div className="biz-rule-draw mt-4 bg-white/20" />
          <ol className="mt-10 grid gap-10 sm:grid-cols-2">
            {shipped.map((w, i) => (
              <li key={w.id} className="border-t border-white/20 pt-5">
                <span className="font-mono text-[0.7rem] text-brand-soft">
                  {String(i + 1).padStart(2, "0")}
                </span>
                <h3 className="mt-2 font-grotesk text-[1.6rem] font-black uppercase tracking-tight2 text-paper">
                  {w.name}
                </h3>
                <p className="mt-3 max-w-prose2 font-sans text-[0.95rem] leading-relaxed text-paper/70">
                  {w.body}
                </p>
              </li>
            ))}
          </ol>
        </section>
      ) : null}

      {/* ---- Principles, set as declarations ---------------------------------- */}
      <section className="px-6 py-16 sm:px-10 sm:py-20">
        <h2 className="biz-label">How we work</h2>
        <div className="biz-rule-draw mt-4 bg-paper-4" />
        <ul className="mt-12 space-y-14">
          {principles.map((p, i) => (
            <li
              key={p.t}
              className="lg:grid lg:grid-cols-[minmax(0,1.3fr)_minmax(0,1fr)] lg:items-baseline lg:gap-x-12"
            >
              <h3 className="font-grotesk text-[clamp(1.8rem,5vw,3.4rem)] font-black uppercase leading-[0.92] tracking-tight3 text-ink-ink">
                <span className="mr-4 align-top font-mono text-[0.7rem] font-medium tracking-label text-brand">
                  {String(i + 1).padStart(2, "0")}
                </span>
                {p.t}
              </h3>
              <p className="mt-4 max-w-prose2 font-mono text-[0.85rem] leading-[1.8] text-ink-body lg:mt-0">
                {p.b}
              </p>
            </li>
          ))}
        </ul>
      </section>

      {/* ---- Colophon --------------------------------------------------------
          A page about how carefully things are built should say how this one
          was. Everything here is verifiable by viewing source. */}
      <section className="px-6 pb-8 sm:px-10">
        <div className="border-t border-ink-ink pt-6">
          <h2 className="biz-label">Colophon</h2>
          <dl className="mt-5 grid gap-x-10 gap-y-3 sm:grid-cols-2 lg:grid-cols-4">
            {colophon.map((c) => (
              <div key={c.k} className="flex gap-3 border-b border-paper-3 py-2">
                <dt className="w-24 shrink-0 font-mono text-[0.64rem] uppercase tracking-label text-ink-soft">
                  {c.k}
                </dt>
                <dd className="font-mono text-[0.76rem] text-ink-ink">{c.v}</dd>
              </div>
            ))}
          </dl>
        </div>
      </section>

      {/* ---- Close ------------------------------------------------------------ */}
      <section className="px-6 py-16 sm:px-10 sm:py-20">
        <div className="border border-ink-ink p-9 sm:p-12">
          <h2 className="biz-h2">Want to work together?</h2>
          <p className="mt-5 max-w-prose2 font-sans text-[1rem] leading-[1.65] text-ink-body">
            Describe the problem in a paragraph. You get a written answer within one business day,
            including the ones we turn down.
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
