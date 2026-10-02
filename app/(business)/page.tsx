import type { Metadata } from "next";
import Link from "next/link";
import { HeroArt } from "@/components/business/HeroArt";
import { FeasibilityPanel } from "@/components/business/FeasibilityPanel";
import { CapabilityRail } from "@/components/business/CapabilityRail";
import { ProcessTimeline } from "@/components/business/ProcessTimeline";
import { biz, bizRoutes, guarantee, ownership, ownershipHeading, pipeline } from "../business";

const description = `${biz.lead} ${biz.person}, ${biz.role} in ${biz.location}.`;

export const metadata: Metadata = {
  title: { absolute: `${biz.name}: AI that works for your business.` },
  description,
  alternates: { canonical: "/" },
  openGraph: {
    title: `${biz.name}: AI that works for your business.`,
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
      <section className="relative w-full overflow-hidden px-6 pb-12 pt-8 sm:px-10 lg:min-h-[41rem] lg:pb-0 lg:pt-[14px] xl:min-h-[722px]">
        {/* Artwork sits behind the type. It is transparent where the headline
            falls, so no scrim is needed. */}
        <h1 className="biz-display relative z-10 tracking-[-0.056em]">
          <span className="block text-[3.6rem] leading-[0.749] tracking-[0em] sm:text-[6rem] lg:text-[min(10.4vw,159px)]">
            {biz.headline[0]}
          </span>
          <span className="block text-[2.3rem] leading-[0.822] sm:text-[3.6rem] lg:text-[min(6.05vw,93px)]">
            {biz.headline[1]}
          </span>
          <span className="block text-[2.3rem] leading-[0.822] sm:text-[3.6rem] lg:text-[min(6.05vw,93px)]">
            {biz.headline[2]}
            <span className="ml-2 inline-block h-[0.2em] w-[0.2em] rounded-full bg-brand align-baseline" />
          </span>
        </h1>

        <div className="pointer-events-none relative -mx-6 mt-6 w-[calc(100%+3rem)] sm:-mx-10 sm:w-[calc(100%+5rem)] lg:absolute lg:left-[36.7%] lg:top-[3px] lg:z-0 lg:mx-0 lg:mt-0 lg:w-[62%]">
          <HeroArt />
        </div>

        <div className="relative z-10 -mt-3 sm:-mt-8 lg:mt-7">
          <p className="biz-lead max-w-[26rem]">{biz.lead}</p>

          <div className="mt-4">
            <Link href={bizRoutes.contact} className="biz-link">
              Start a conversation ↗
            </Link>
          </div>

          <p className="mt-6 border-l-2 border-ember pl-4 font-mono text-[0.78rem] uppercase tracking-label text-ink-body">
            {biz.phases.join("  /  ")}
          </p>

          <div className="mt-7 border-l-2 border-brand pl-4">
            <p className="font-sans text-[0.95rem] text-ink-body">{biz.teamLine}</p>
            <p className="mt-1.5 font-sans text-[0.95rem] font-semibold text-ink-ink">
              {biz.disciplines.join(" • ")}
            </p>
          </div>
        </div>

        {/* Below xl the panel cannot overlap the hero, so it rides here
            instead of being hidden from the traffic that matters most. */}
        <div className="relative z-10 mt-10 xl:hidden">
          <FeasibilityPanel />
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
              <p className="mt-3 font-sans text-[0.95rem] leading-relaxed text-ink-body">
                {stage.body}
              </p>
            </li>
          ))}
        </ul>

      </section>

      {/* ---- Inverted band ---------------------------------------------- */}
      <section className="biz-invert relative w-full" aria-labelledby="ownership-heading">
        {/* One left column, held clear of the feasibility panel at xl so the
            heading, the rail and the promise all share the same right edge. */}
        <div className="px-6 pb-16 pt-12 sm:px-10 sm:pb-20 sm:pt-10">
          <div className="xl:mr-[540px]">
            {/* One line from lg up. At xl the size tracks the column left of
                the panel, so the line always ends before it. */}
            <h2
              id="ownership-heading"
              className="biz-display text-[2rem] leading-[0.95] tracking-[-0.02em] sm:text-[3rem] lg:whitespace-nowrap lg:text-[min(4.25vw,65px)] xl:text-[min(calc((100vw-620px)/17),65px)]"
            >
              {ownershipHeading}
              <span className="ml-2 inline-block h-[0.2em] w-[0.2em] rounded-full bg-brand align-baseline" />
            </h2>

            {/* One row from lg up: number and label side by side, the label
                scaling with the viewport so all four clear the panel at xl. */}
            <ol className="mt-10 grid grid-cols-2 gap-x-6 gap-y-6 border-t border-white/20 lg:flex lg:flex-nowrap lg:justify-between lg:gap-x-4">
              {ownership.map((item) => (
                <li key={item.n} className="relative pt-4">
                  {/* tick rising from the rule above, as in the reference */}
                  <span aria-hidden="true" className="absolute left-0 top-0 h-3 w-px bg-white/35" />
                  <span className="flex items-baseline gap-1.5">
                    <span className="font-grotesk text-[0.9rem] font-black text-paper">{item.n}</span>
                    <span aria-hidden="true" className="h-px w-2.5 shrink-0 self-center bg-ember" />
                    <span className="font-grotesk text-[0.75rem] font-medium uppercase tracking-[0.04em] text-paper/85 lg:whitespace-nowrap xl:text-[clamp(0.62rem,0.8vw,0.75rem)]">
                      {item.label}
                    </span>
                  </span>
                </li>
              ))}
            </ol>

            {/* The promise, where the differentiator used to sit on its own. */}
            <div className="mt-12 border-t border-white/20 pt-9">
              <h3 className="font-grotesk text-2xl font-bold uppercase leading-none tracking-normal text-paper sm:text-3xl">
                {guarantee.question}
              </h3>
              <p className="mt-3 font-mono text-[0.75rem] uppercase tracking-[0.08em] text-brand-soft">
                {guarantee.subhead}
              </p>
              <p className="mt-6 max-w-prose2 font-sans text-[1.02rem] leading-[1.7] text-paper/75">
                {guarantee.pitch}
              </p>

              <div className="mt-8 flex flex-wrap items-center gap-x-6 gap-y-3">
                <Link
                  href={bizRoutes.contact}
                  className="biz-btn !border-brand !bg-brand !text-white hover:!border-brand-soft hover:!bg-brand-soft"
                >
                  {biz.railCta} ↗
                </Link>
                <a
                  href={`tel:${biz.phoneHref}`}
                  className="font-grotesk text-[1.2rem] font-bold tracking-normal text-paper transition-colors hover:text-brand-soft"
                >
                  {biz.phone}
                </a>
              </div>
            </div>
          </div>
        </div>

        {/* The feasibility panel, overlapping up into the hero where the
            product mock used to sit. Interactive, so no pointer-events-none. */}
        <div className="absolute right-10 top-0 hidden w-[min(32vw,500px)] -translate-y-[4.5rem] xl:block">
          <FeasibilityPanel />
        </div>
      </section>

      {/* ---- Capabilities: rail drives the workbench below it ---- */}
      <CapabilityRail />

      {/* The phase timeline pins, then the sections below ride over it. */}
      <div className="relative">
        <div className="bg-paper lg:sticky lg:top-0 lg:z-0">
      {/* ---- Engagement ------------------------------------------------ */}
        <ProcessTimeline />

        </div>

        <div className="relative z-10 bg-paper">
      {/* ---- Contact CTA -------------------------------------------------- */}
      <section
          aria-labelledby="cta-heading"
          className="biz-invert relative w-full overflow-hidden px-6 py-20 sm:px-10 sm:py-24"
        >
        <div className="relative lg:grid lg:grid-cols-[minmax(0,1fr)_auto] lg:items-end lg:gap-12">
          <span aria-hidden="true" className="absolute right-0 top-0 h-px w-28 bg-brand" />
          <span aria-hidden="true" className="absolute right-0 top-0 h-28 w-px bg-brand" />
          <div>
            <p className="biz-label !text-brand-soft">Contact</p>
            <h2 id="cta-heading" className="biz-display mt-5 text-[2.5rem] sm:text-[4rem]">
              Tell me what is broken.
            </h2>
            <p className="mt-6 max-w-prose2 font-sans text-[1rem] leading-[1.65] text-paper/70">
              Describe the problem in a paragraph. If AI is the wrong tool for it, we will say so and
              tell you what we would use instead.
            </p>
          </div>
          <div className="mt-10 flex flex-wrap items-center gap-4 lg:mt-0 lg:flex-col lg:items-stretch">
            <a
              href={`mailto:${biz.email}`}
              className="biz-btn !border-brand !bg-brand !text-white hover:!border-brand-soft hover:!bg-brand-soft"
            >
              {biz.email}
            </a>
            <Link
              href={bizRoutes.contact}
              className="biz-btn-ghost !border-white/25 !text-paper hover:!border-paper"
            >
              What to include
            </Link>
          </div>
        </div>
      </section>
        </div>
      </div>
    </>
  );
}
