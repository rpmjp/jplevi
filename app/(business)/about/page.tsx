import type { Metadata } from "next";
import Link from "next/link";
import { biz, bizRoutes, process } from "../../business";
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
  return (
    <section className="mx-auto max-w-biz px-5 pb-16 pt-14 sm:px-8 sm:pt-20">
      <p className="biz-label-blue">Company</p>
      <h1 className="biz-display mt-5 text-[2.6rem] sm:text-[4rem]">
        {biz.name}
        <span className="ml-1 inline-block h-[0.2em] w-[0.2em] rounded-full bg-brand align-baseline" />
      </h1>

      <div className="mt-12 grid gap-12 lg:grid-cols-[minmax(0,1.2fr)_minmax(0,1fr)]">
        <div>
          <p className="biz-lead max-w-xl">{biz.teamLine}</p>
          <p className="mt-4 border-l-2 border-brand pl-5 font-mono text-[0.85rem] font-semibold text-ink-ink">
            {biz.disciplines.join(" • ")}
          </p>

          <p className="mt-8 max-w-prose2 font-mono text-[0.92rem] leading-[1.8] text-ink-body">
            We work as a small senior team rather than an agency. You get direct access to the
            people doing the work, a shorter path from decision to deployment, and no margin paid
            on a layer of account management. It also means we take on a limited number of projects
            at a time, and we will say no to work we are not the right fit for.
          </p>
          <p className="mt-6 max-w-prose2 font-mono text-[0.92rem] leading-[1.8] text-ink-body">
            The studio operates as {site.legalName}, {biz.parentNote}, which also publishes its own
            products. Building and running things we own keeps us honest about what is actually
            hard to operate.
          </p>
        </div>

        <div className="border border-paper-3 p-7">
          <h2 className="biz-label">At a glance</h2>
          <div className="biz-rule mt-3" />
          <dl className="mt-5 space-y-4 font-mono text-[0.82rem]">
            {[
              ["Entity", site.legalName],
              ["Based", biz.location],
              ["Lead", `${biz.person}, ${biz.role}`],
              ["Availability", biz.availability],
              ["Contact", biz.email],
            ].map(([k, v]) => (
              <div key={k} className="flex justify-between gap-6 border-b border-paper-3 pb-3">
                <dt className="uppercase tracking-label text-ink-soft">{k}</dt>
                <dd className="text-right text-ink-ink">{v}</dd>
              </div>
            ))}
          </dl>
        </div>
      </div>

      <div className="mt-20">
        <h2 className="biz-h2">How we work</h2>
        <div className="biz-rule mt-6" />
        <ul className="mt-10 grid gap-9 sm:grid-cols-2">
          {principles.map((p) => (
            <li key={p.t}>
              <h3 className="biz-h3">{p.t}</h3>
              <p className="mt-2.5 font-mono text-[0.85rem] leading-relaxed text-ink-body">{p.b}</p>
            </li>
          ))}
        </ul>
      </div>

      <div className="mt-20">
        <h2 className="biz-h2">Engagement shape</h2>
        <div className="biz-rule mt-6" />
        <ol className="mt-10 grid gap-9 sm:grid-cols-2 lg:grid-cols-4">
          {process.map((step) => (
            <li key={step.n}>
              <span className="font-grotesk text-2xl font-black text-brand">{step.n}</span>
              <h3 className="biz-h3 mt-3">{step.title}</h3>
              <p className="mt-2.5 font-mono text-[0.82rem] leading-relaxed text-ink-body">{step.body}</p>
            </li>
          ))}
        </ol>
      </div>

      <div className="mt-20 border border-ink-ink p-9 sm:p-12">
        <h2 className="biz-h2">Want to work together?</h2>
        <Link href={bizRoutes.contact} className="biz-btn mt-8">
          Start a conversation
        </Link>
      </div>
    </section>
  );
}
