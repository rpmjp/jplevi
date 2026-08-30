import type { Metadata } from "next";
import Link from "next/link";
import { ContactComposer } from "@/components/business/ContactComposer";
import { biz, bizRoutes, capabilities, feasibility, guarantee } from "../../business";

const description = `Start a conversation with ${biz.name}. What to include so the first reply is useful.`;

export const metadata: Metadata = {
  title: "Contact",
  description,
  alternates: { canonical: bizRoutes.contact },
  openGraph: { title: `Contact | ${biz.name}`, description, url: `${biz.url}/contact/`, type: "article" },
};

export default function ContactPage() {
  return (
    <div className="w-full">
      {/* ---- Masthead ---------------------------------------------------- */}
      <section className="px-6 pb-14 pt-14 sm:px-10 sm:pt-20">
        <p className="biz-label-blue">Contact</p>
        <h1 className="biz-display mt-5 max-w-[16ch] text-[clamp(2.6rem,7.5vw,6rem)]">
          Tell us what is broken
          <span className="ml-3 inline-block h-[0.13em] w-[0.13em] rounded-full bg-brand align-baseline" />
        </h1>
      </section>

      {/* ---- The commitment, set at the size it deserves ------------------
          This is the single strongest thing on the page, so it is the
          biggest thing on the page. */}
      <section className="bg-night px-6 py-16 sm:px-10 sm:py-20">
        <p className="biz-label !text-paper/45">Our commitment</p>
        <p className="mt-7 max-w-[20ch] font-grotesk text-[clamp(2.2rem,6.5vw,5rem)] font-black uppercase leading-[0.9] tracking-tight3 text-paper">
          {guarantee.headline}
        </p>
        <p className="mt-8 max-w-prose2 font-sans text-[1.05rem] leading-[1.6] text-paper/70">
          {guarantee.body}
        </p>

        <div className="mt-10 flex flex-wrap items-center gap-x-8 gap-y-4 border-t border-white/15 pt-8">
          <a
            href={`tel:${biz.phoneHref}`}
            className="font-grotesk text-[clamp(1.4rem,3vw,2rem)] font-black tracking-tight2 text-paper transition-colors hover:text-brand-soft"
          >
            {biz.phone}
          </a>
          <a
            href={`mailto:${biz.email}`}
            className="font-mono text-[0.82rem] text-paper/70 underline decoration-white/30 underline-offset-[6px] transition-colors hover:text-paper"
          >
            {biz.email}
          </a>
        </div>
      </section>

      {/* ---- The composer -------------------------------------------------- */}
      <section aria-labelledby="composer-heading" className="px-6 py-16 sm:px-10 sm:py-20">
        <div className="flex flex-wrap items-end justify-between gap-6">
          <h2 id="composer-heading" className="biz-h2">
            Write it here
          </h2>
          <p className="font-mono text-[0.7rem] uppercase tracking-label text-ink-soft">
            Three steps
          </p>
        </div>
        <div className="biz-rule-draw mt-5 bg-paper-4" />
        <p className="mt-6 max-w-prose2 font-mono text-[0.88rem] leading-[1.8] text-ink-body">
          Answer what you can. Partial is fine, and the message builds itself beside you so you can
          see exactly what arrives.
        </p>

        <div className="mt-12">
          <ContactComposer />
        </div>
      </section>

      {/* ---- What happens next ---------------------------------------------
          One list, not three. The composer above is now the "what to include",
          so it does not need saying twice. */}
      <section className="px-6 pb-16 sm:px-10 sm:pb-20">
        <div className="border-t border-ink-ink pt-10">
          <div className="flex flex-wrap items-end justify-between gap-6">
            <h2 className="biz-h2">{feasibility.title}</h2>
            <p className="font-mono text-[0.7rem] uppercase tracking-label text-brand">No charge</p>
          </div>
          <p className="mt-6 max-w-prose2 font-sans text-[1.02rem] leading-[1.65] text-ink-body">
            {feasibility.lead}
          </p>
          <ol className="mt-12 space-y-10">
            {feasibility.steps.map((step) => (
              <li
                key={step.n}
                className="lg:grid lg:grid-cols-[minmax(0,1.2fr)_minmax(0,1fr)] lg:items-baseline lg:gap-x-12"
              >
                <h3 className="font-grotesk text-[clamp(1.5rem,4vw,2.6rem)] font-black uppercase leading-[0.95] tracking-tight3 text-ink-ink">
                  <span className="mr-4 align-top font-mono text-[0.7rem] font-medium tracking-label text-brand">
                    {step.n}
                  </span>
                  {step.title}
                </h3>
                <p className="mt-3 max-w-prose2 font-mono text-[0.85rem] leading-[1.8] text-ink-body lg:mt-0">
                  {step.body}
                </p>
              </li>
            ))}
          </ol>
        </div>
      </section>

      {/* ---- Routing, for anyone who still is not sure --------------------- */}
      <section className="px-6 pb-16 sm:px-10 sm:pb-20">
        <div className="border-t border-paper-3 pt-8">
          <h2 className="biz-label">Or read what we build first</h2>
          <ul className="mt-6 flex flex-wrap gap-2">
            {capabilities.map((s) => (
              <li key={s.id}>
                <Link
                  href={`${bizRoutes.services}#${s.id}`}
                  className="inline-block border border-paper-3 px-3.5 py-2 font-mono text-[0.75rem] text-ink-body transition-colors hover:border-ink-ink hover:text-brand"
                >
                  <span className="text-ink-soft">{s.n}</span> {s.title.join(" ")}
                </Link>
              </li>
            ))}
          </ul>
        </div>
      </section>
    </div>
  );
}
