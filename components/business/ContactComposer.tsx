"use client";

import { useMemo, useRef, useState } from "react";
import { biz, feasibilityProbes } from "@/app/business";

/**
 * The contact page's composer.
 *
 * Three steps, because three fields convert about as well as one and every
 * field after that costs submissions. The message assembles in the open as it
 * is filled in, so nobody is asked to trust an invisible payload.
 *
 * Two ways out on purpose. A mailto opens the visitor's mail app, which wins
 * heavily on mobile, but silently does nothing for the large share of desktop
 * users with no mail client configured. So the finished message is also right
 * there to copy into webmail. No backend either way, which keeps the static
 * export and the "no server, no tracking" claim intact.
 */
export function ContactComposer() {
  const [picked, setPicked] = useState<string | null>(null);
  const [detail, setDetail] = useState("");
  const [from, setFrom] = useState("");
  const [copied, setCopied] = useState(false);
  const copyTimer = useRef<ReturnType<typeof setTimeout> | null>(null);

  const probe = feasibilityProbes.find((p) => p.id === picked) ?? null;

  const subject = probe ? `Enquiry: ${probe.label}` : "Project enquiry";

  /** Only what was actually filled in. Nothing is required. */
  const body = useMemo(() => {
    const lines: string[] = [];
    if (probe) lines.push(probe.label, "");
    if (detail.trim()) lines.push(detail.trim(), "");
    if (from.trim()) lines.push(`Reply to: ${from.trim()}`);
    return lines.join("\n").trimEnd();
  }, [probe, detail, from]);

  const mailto = `mailto:${biz.email}?subject=${encodeURIComponent(subject)}&body=${encodeURIComponent(body)}`;
  const plain = `To: ${biz.email}\nSubject: ${subject}\n\n${body}`;

  async function copy() {
    try {
      await navigator.clipboard.writeText(plain);
    } catch {
      return;
    }
    setCopied(true);
    if (copyTimer.current) clearTimeout(copyTimer.current);
    copyTimer.current = setTimeout(() => setCopied(false), 2600);
  }

  return (
    <div className="lg:grid lg:grid-cols-[minmax(0,1fr)_minmax(0,26rem)] lg:gap-x-14">
      {/* ---- the three steps ---- */}
      <div>
        <fieldset className="border-t border-ink-ink pt-5">
          <legend className="sr-only">What do you need help with, if you know</legend>
          <p className="font-mono text-[0.66rem] uppercase tracking-label text-ink-soft">
            01 / What do you need help with, if you know
          </p>
          <div className="mt-4 flex flex-wrap gap-1.5">
            {feasibilityProbes.map((p) => {
              const on = p.id === picked;
              return (
                <button
                  key={p.id}
                  type="button"
                  aria-pressed={on}
                  onClick={() => setPicked(on ? null : p.id)}
                  className={
                    "border px-3 py-2 text-left font-sans text-[0.8rem] leading-tight transition-colors " +
                    (on
                      ? "border-brand bg-brand text-white"
                      : "border-paper-4 text-ink-body hover:border-ink-ink hover:text-ink-ink")
                  }
                >
                  {p.label}
                </button>
              );
            })}
          </div>
        </fieldset>

        <div className="mt-10 border-t border-ink-ink pt-5">
          <label
            htmlFor="contact-detail"
            className="font-mono text-[0.66rem] uppercase tracking-label text-ink-soft"
          >
            02 / Describe it in your own words
          </label>
          <textarea
            id="contact-detail"
            rows={6}
            value={detail}
            onChange={(e) => setDetail(e.target.value)}
            placeholder="What is happening now, roughly what data or systems you already have, and what a good outcome would look like. Partial is fine."
            className="mt-4 w-full resize-y border border-paper-4 bg-white/60 px-4 py-3.5 font-sans text-[0.92rem] leading-relaxed text-ink-ink placeholder:text-ink-soft/70 focus:border-brand focus:outline-none"
          />
        </div>

        <div className="mt-10 border-t border-ink-ink pt-5">
          <label
            htmlFor="contact-from"
            className="font-mono text-[0.66rem] uppercase tracking-label text-ink-soft"
          >
            03 / Where do we reply
          </label>
          <input
            id="contact-from"
            type="email"
            inputMode="email"
            autoComplete="email"
            value={from}
            onChange={(e) => setFrom(e.target.value)}
            placeholder="you@company.com"
            className="mt-4 w-full border border-paper-4 bg-white/60 px-4 py-3.5 font-sans text-[0.92rem] text-ink-ink placeholder:text-ink-soft/70 focus:border-brand focus:outline-none"
          />
        </div>
      </div>

      {/* ---- the message, in the open ---- */}
      <div className="mt-12 lg:mt-0">
        <div className="lg:sticky lg:top-28">
          <p className="font-mono text-[0.66rem] uppercase tracking-label text-ink-soft">
            What gets sent
          </p>

          <pre
            aria-live="polite"
            className="mt-4 max-h-[24rem] overflow-auto whitespace-pre-wrap border border-ink-ink bg-night px-5 py-4 font-mono text-[0.76rem] leading-[1.7] text-paper/85"
          >
            {plain}
          </pre>

          <div className="mt-5 flex flex-wrap gap-3">
            <a
              href={mailto}
              className="biz-btn !border-brand !bg-brand !text-white hover:!border-brand-soft hover:!bg-brand-soft"
            >
              Open in email app ↗
            </a>
            <button type="button" onClick={copy} className="biz-btn-ghost">
              {copied ? "Copied" : "Copy message"}
            </button>
          </div>

          <p className="mt-4 font-mono text-[0.7rem] leading-relaxed text-ink-soft">
            No mail app on this machine? Copy it and paste into webmail. Nothing here is submitted
            anywhere, and nothing is stored.
          </p>
        </div>
      </div>
    </div>
  );
}
