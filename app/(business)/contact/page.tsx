import type { Metadata } from "next";
import Link from "next/link";
import { biz, bizRoutes, services } from "../../business";

const description = `Start a conversation with ${biz.name}. What to include so the first reply is useful.`;

export const metadata: Metadata = {
  title: "Contact",
  description,
  alternates: { canonical: bizRoutes.contact },
  openGraph: { title: `Contact | ${biz.name}`, description, url: `${biz.url}/contact/`, type: "article" },
};

const include = [
  "What the problem is, in your own words. Not a spec, just the situation.",
  "What data you have, roughly, and where it lives.",
  "What a good outcome looks like, and how you would know you got it.",
  "Any hard constraints: deadline, budget range, compliance, on-premise requirements.",
];

const mailto = `mailto:${biz.email}?subject=${encodeURIComponent("Project enquiry")}&body=${encodeURIComponent(
  [
    "The problem:",
    "",
    "The data we would be working with:",
    "",
    "What a good outcome looks like:",
    "",
    "Constraints (deadline, budget range, compliance):",
    "",
  ].join("\n"),
)}`;

export default function ContactPage() {
  return (
    <section className="mx-auto max-w-biz px-5 pb-16 pt-14 sm:px-8 sm:pt-20">
      <p className="biz-label-blue">Contact</p>
      <h1 className="biz-display mt-5 text-[2.6rem] sm:text-[4rem]">
        Start a conversation
        <span className="ml-1 inline-block h-[0.2em] w-[0.2em] rounded-full bg-brand align-baseline" />
      </h1>
      <p className="biz-lead mt-8 max-w-xl">
        Email is the fastest route, and it reaches us directly rather than a queue. We reply to
        every serious enquiry, including the ones we turn down.
      </p>

      <div className="mt-10 flex flex-wrap items-center gap-4">
        <a href={mailto} className="biz-btn">
          Email {biz.email}
        </a>
        <a href={`mailto:${biz.email}`} className="biz-btn-ghost">
          Plain empty email
        </a>
        <a href={`tel:${biz.phoneHref}`} className="biz-btn-ghost">
          {biz.phone}
        </a>
      </div>

      <div className="mt-16 grid gap-12 border-t border-paper-3 pt-10 lg:grid-cols-2">
        <div>
          <h2 className="biz-h2">What to include</h2>
          <p className="mt-4 font-mono text-[0.88rem] leading-[1.8] text-ink-body">
            The first button above opens a mail draft with these as headings. Fill in what you can;
            partial is fine.
          </p>
          <ul className="mt-7 space-y-3.5">
            {include.map((line) => (
              <li key={line} className="flex gap-3 font-mono text-[0.85rem] leading-relaxed text-ink-body">
                <span aria-hidden="true" className="mt-2 h-1.5 w-1.5 shrink-0 bg-brand" />
                {line}
              </li>
            ))}
          </ul>
        </div>

        <div>
          <h2 className="biz-h2">What happens next</h2>
          <ol className="mt-7 space-y-6">
            {[
              ["A reply", "A straight answer on whether this is something we can help with."],
              ["A call", "Thirty minutes to pull the problem apart, at no charge."],
              ["A scope", "A written recommendation and a price, or a referral elsewhere if we are the wrong fit."],
            ].map(([t, b], i) => (
              <li key={t} className="flex gap-4">
                <span className="font-grotesk text-lg font-black text-brand">
                  {String(i + 1).padStart(2, "0")}
                </span>
                <div>
                  <h3 className="biz-h3">{t}</h3>
                  <p className="mt-1.5 font-mono text-[0.84rem] leading-relaxed text-ink-body">{b}</p>
                </div>
              </li>
            ))}
          </ol>
        </div>
      </div>

      <div className="mt-16 border-t border-paper-3 pt-10">
        <h2 className="biz-label">Not sure what you need?</h2>
        <ul className="mt-6 flex flex-wrap gap-3">
          {services.map((s) => (
            <li key={s.id}>
              <Link
                href={`${bizRoutes.capabilities}#${s.id}`}
                className="inline-block border border-paper-3 px-3.5 py-2 font-mono text-[0.75rem] text-ink-body transition-colors hover:border-ink-ink hover:text-brand"
              >
                {s.title}
              </Link>
            </li>
          ))}
        </ul>
      </div>
    </section>
  );
}
