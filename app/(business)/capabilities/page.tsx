import type { Metadata } from "next";
import Link from "next/link";
import { biz, bizRoutes, pipeline, services } from "../../business";

const description =
  "RAG and GraphRAG, generative AI, machine learning, full-stack product work, and managed hosting. What each engagement actually delivers.";

export const metadata: Metadata = {
  title: "Capabilities",
  description,
  alternates: { canonical: bizRoutes.capabilities },
  openGraph: { title: `Capabilities | ${biz.name}`, description, url: `${biz.url}/capabilities/`, type: "article" },
};

export default function CapabilitiesPage() {
  return (
    <section className="mx-auto max-w-biz px-5 pb-16 pt-14 sm:px-8 sm:pt-20">
      <p className="biz-label-blue">Capabilities</p>
      <h1 className="biz-display mt-5 text-[2.6rem] sm:text-[4rem]">
        What we actually do
        <span className="ml-1 inline-block h-[0.2em] w-[0.2em] rounded-full bg-brand align-baseline" />
      </h1>
      <p className="biz-lead mt-8 max-w-xl">
        Six overlapping practices. Most projects use two or three of them, and the honest answer to
        &ldquo;which one do I need&rdquo; usually comes out of a short scoping call rather than this page.
      </p>

      {/* Pipeline framing */}
      <ul className="mt-14 grid gap-8 border-y border-paper-3 py-8 sm:grid-cols-3">
        {pipeline.map((stage) => (
          <li key={stage.id}>
            <h2 className="biz-label !text-ink-ink">{stage.title}</h2>
            <p className="mt-2 font-mono text-[0.82rem] leading-relaxed text-ink-body">{stage.body}</p>
          </li>
        ))}
      </ul>

      <div className="mt-16 space-y-16">
        {services.map((service, i) => (
          <article key={service.id} id={service.id} className="grid gap-8 lg:grid-cols-[minmax(0,1fr)_minmax(0,1.3fr)]">
            <div>
              <span className="font-mono text-[0.7rem] text-ink-soft">
                {String(i + 1).padStart(2, "0")}
              </span>
              <h2 className="biz-h2 mt-3">{service.title}</h2>
            </div>
            <div>
              <p className="font-mono text-[0.92rem] leading-[1.8] text-ink-body">{service.summary}</p>
              <ul className="mt-6 space-y-3">
                {service.detail.map((d) => (
                  <li key={d} className="flex gap-3 font-mono text-[0.85rem] leading-relaxed text-ink-body">
                    <span aria-hidden="true" className="mt-2 h-1.5 w-1.5 shrink-0 bg-brand" />
                    {d}
                  </li>
                ))}
              </ul>
              {service.id === "hosting" ? (
                <Link href={bizRoutes.hosting} className="biz-link mt-6 inline-block">
                  Hosting detail ↗
                </Link>
              ) : null}
            </div>
          </article>
        ))}
      </div>

      <div className="mt-20 border border-ink-ink p-9 sm:p-12">
        <h2 className="biz-h2">Not sure which of these you need?</h2>
        <p className="mt-5 max-w-prose2 font-mono text-[0.9rem] leading-[1.8] text-ink-body">
          That is what scoping is for. Describe the problem and we will tell you which of these
          applies, or that none of them do.
        </p>
        <Link href={bizRoutes.contact} className="biz-btn mt-8">
          Start a conversation
        </Link>
      </div>
    </section>
  );
}
