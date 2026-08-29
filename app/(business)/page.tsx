import type { Metadata } from "next";
import Link from "next/link";
import { HeroArt } from "@/components/business/HeroArt";
import { DocIntelligence } from "@/components/business/DocIntelligence";
import { biz, bizRoutes, ownership, ownershipHeading, pipeline, process, services, stack } from "../business";

const description = `${biz.lead} ${biz.person}, ${biz.role} in ${biz.location}.`;

export const metadata: Metadata = {
  title: { absolute: `${biz.name}: AI without the theater.` },
  description,
  alternates: { canonical: "/" },
  openGraph: {
    title: `${biz.name}: AI without the theater.`,
    description,
    url: biz.url,
    type: "website",
  },
};

export default function BusinessHome() {
  return (
    <>
      {/* ---- Hero ------------------------------------------------------- */}
      {/* Proportions taken from the reference at 1536x1024: rail 6%, artwork
          58% wide starting at 39%, headline cap heights 15.1% and 8.8%. */}
      <section className="relative w-full overflow-hidden px-6 pb-2 pt-3 sm:px-10 lg:min-h-[41rem] lg:pt-[14px] xl:min-h-[722px]">
        {/* Artwork sits behind the type. It is transparent where the headline
            falls, so no scrim is needed. */}
        <div className="pointer-events-none relative mt-10 lg:absolute lg:left-[36.7%] lg:top-[3px] lg:z-0 lg:mt-0 lg:w-[62%]">
          <HeroArt />
        </div>

        <div className="relative z-10">
          <h1 className="biz-display tracking-[-0.024em]">
            <span className="block text-[3.6rem] leading-[0.749] sm:text-[6rem] lg:text-[min(15.2vw,233px)]">
              {biz.headline[0]}
            </span>
            <span className="block text-[2.3rem] leading-[0.822] sm:text-[3.6rem] lg:text-[min(8.6vw,132px)]">
              {biz.headline[1]}
            </span>
            <span className="block text-[2.3rem] leading-[0.822] sm:text-[3.6rem] lg:text-[min(8.6vw,132px)]">
              {biz.headline[2]}
              <span className="ml-2 inline-block h-[0.2em] w-[0.2em] rounded-full bg-brand align-baseline" />
            </span>
          </h1>

          <p className="biz-lead mt-7 max-w-[26rem]">{biz.lead}</p>

          <div className="mt-4">
            <Link href={bizRoutes.contact} className="biz-link">
              Start a conversation ↗
            </Link>
          </div>

          <p className="mt-6 border-l-2 border-ember pl-4 font-mono text-[0.78rem] uppercase tracking-label text-ink-body">
            {biz.phases.join("  /  ")}
          </p>

          <div className="mt-7 border-l-2 border-brand pl-4">
            <p className="font-mono text-[0.82rem] text-ink-body">{biz.teamLine}</p>
            <p className="mt-1.5 font-mono text-[0.82rem] font-semibold text-ink-ink">
              {biz.disciplines.join(" • ")}
            </p>
          </div>
        </div>

        {/* The stage labels are baked into the artwork, so this copy is for
            screen readers and small screens only. */}
        <ul className="relative z-10 mt-12 grid gap-8 border-t border-paper-3 pt-8 sm:grid-cols-3 lg:sr-only">
          {pipeline.map((stage) => (
            <li key={stage.id}>
              <div className="flex items-center gap-3">
                <span aria-hidden="true" className="h-2 w-2 border border-ink-ink" />
                <h2 className="biz-label !text-ink-ink">{stage.title}</h2>
              </div>
              <p className="mt-3 font-mono text-[0.82rem] leading-relaxed text-ink-body">
                {stage.body}
              </p>
            </li>
          ))}
        </ul>

        <p className="relative z-10 mt-8 text-center font-mono text-[0.7rem] uppercase tracking-label text-ink-soft lg:mt-0">
          Scroll to explore ↓
        </p>
      </section>

      {/* ---- Inverted band ---------------------------------------------- */}
      <section className="biz-invert relative w-full" aria-labelledby="ownership-heading">
        <div className="px-6 pb-16 pt-12 sm:px-[50px] sm:pb-20 sm:pt-8">
          <h2
            id="ownership-heading"
            className="biz-display max-w-[62rem] text-[2rem] tracking-[-0.108em] sm:text-[3rem] lg:text-[min(5.3vw,81px)]"
            style={{ fontStretch: "63%" }}
          >
            {ownershipHeading}
            <span className="ml-2 inline-block h-[0.2em] w-[0.2em] rounded-full bg-brand align-baseline" />
          </h2>

          <ol className="mt-7 flex flex-wrap gap-x-[55px] gap-y-8 border-t border-white/20 pt-0">
            {ownership.map((item) => (
              <li key={item.n} className="relative pt-3">
                {/* tick rising from the rule above, as in the reference */}
                <span aria-hidden="true" className="absolute left-0 top-0 h-3 w-px bg-white/35" />
                <span className="flex items-baseline gap-2">
                  <span className="font-grotesk text-[0.85rem] font-black text-paper">{item.n}</span>
                  <span aria-hidden="true" className="h-px w-3 bg-ember" />
                  <span className="whitespace-nowrap font-mono text-[0.65rem] uppercase tracking-[0.08em] text-paper/85">
                    {item.label}
                  </span>
                </span>
              </li>
            ))}
          </ol>

          <p className="mt-12 max-w-prose2 font-mono text-[0.9rem] leading-[1.8] text-paper/70">
            No account layer, no handoff between the people who scope the work and the people who
            build it. You talk to the engineers who write the code and run the servers.
          </p>
        </div>

        {/* Product panel, overlapping up into the hero as in the reference. */}
        <div className="pointer-events-none absolute left-[70.9%] top-0 hidden w-[500px] -translate-y-[4.5rem] lg:block">
          <DocIntelligence />
        </div>
      </section>

      {/* ---- Services ---------------------------------------------------- */}
      <section aria-labelledby="services-heading" className="w-full px-6 py-20 sm:px-10">
        <div className="flex flex-wrap items-end justify-between gap-6">
          <div>
            <p className="biz-label-blue">Capabilities</p>
            <h2 id="services-heading" className="biz-h2 mt-4">
              What we build
            </h2>
          </div>
          <Link href={bizRoutes.capabilities} className="biz-link">
            All capabilities ↗
          </Link>
        </div>
        <div className="biz-rule mt-8" />

        <ul className="mt-10 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
          {services.map((service, i) => (
            <li key={service.id} className="biz-card flex flex-col">
              <span className="font-mono text-[0.7rem] text-ink-soft">
                {String(i + 1).padStart(2, "0")}
              </span>
              <h3 className="biz-h3 mt-4">{service.title}</h3>
              <p className="mt-3 flex-1 font-mono text-[0.82rem] leading-relaxed text-ink-body">
                {service.summary}
              </p>
              <Link href={service.href} className="biz-link mt-6 self-start !text-[0.7rem]">
                Detail ↗
              </Link>
            </li>
          ))}
        </ul>
      </section>

      {/* ---- Process ----------------------------------------------------- */}
      <section aria-labelledby="process-heading" className="w-full px-6 py-12 sm:px-10">
        <p className="biz-label-blue">Engagement</p>
        <h2 id="process-heading" className="biz-h2 mt-4">
          How a project runs
        </h2>
        <div className="biz-rule mt-8" />

        <ol className="mt-10 grid gap-10 sm:grid-cols-2 lg:grid-cols-4">
          {process.map((step) => (
            <li key={step.n}>
              <div className="flex items-center gap-3">
                <span className="font-grotesk text-2xl font-black text-brand">{step.n}</span>
                <span aria-hidden="true" className="h-px flex-1 bg-paper-4" />
              </div>
              <h3 className="biz-h3 mt-4">{step.title}</h3>
              <p className="mt-3 font-mono text-[0.82rem] leading-relaxed text-ink-body">
                {step.body}
              </p>
            </li>
          ))}
        </ol>
      </section>

      {/* ---- Stack ------------------------------------------------------- */}
      <section aria-labelledby="stack-heading" className="w-full px-6 py-12 sm:px-10">
        <h2 id="stack-heading" className="biz-label">
          Working stack
        </h2>
        <div className="biz-rule mt-3" />
        <ul className="mt-6 flex flex-wrap gap-x-3 gap-y-3">
          {stack.map((item) => (
            <li
              key={item}
              className="border border-paper-3 px-3 py-1.5 font-mono text-[0.72rem] text-ink-body"
            >
              {item}
            </li>
          ))}
        </ul>
      </section>

      {/* ---- Contact CTA -------------------------------------------------- */}
      <section aria-labelledby="cta-heading" className="w-full px-6 py-20 sm:px-10">
        <div className="border border-ink-ink p-9 sm:p-14">
          <p className="biz-label-blue">Contact</p>
          <h2 id="cta-heading" className="biz-display mt-5 text-[2rem] sm:text-[3rem]">
            Tell me what is broken.
          </h2>
          <p className="mt-6 max-w-prose2 font-mono text-[0.9rem] leading-[1.8] text-ink-body">
            Describe the problem in a paragraph. If AI is the wrong tool for it, we will say so and
            tell you what we would use instead.
          </p>
          <div className="mt-10 flex flex-wrap items-center gap-4">
            <a href={`mailto:${biz.email}`} className="biz-btn">
              {biz.email}
            </a>
            <Link href={bizRoutes.contact} className="biz-btn-ghost">
              What to include
            </Link>
          </div>
        </div>
      </section>
    </>
  );
}
