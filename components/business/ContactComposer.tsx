"use client";

import { useMemo, useRef, useState } from "react";
import { biz, contactEndpoint, feasibilityProbes } from "@/app/business";

type Status = "idle" | "sending" | "sent" | "error";

/**
 * The contact page's composer.
 *
 * Posting to a real endpoint is the primary path, because a mailto captures
 * nothing: it leaves no record if the visitor never sends, and silently does
 * nothing at all for the large share of desktop users with no mail client
 * configured. The mail draft and the copyable message stay as fallbacks, which
 * is what makes this safe rather than a single point of failure.
 *
 * With no endpoint configured the form hides itself and the page still works
 * exactly as it did before, so nothing breaks while a key is pending.
 */
export function ContactComposer() {
  const [picked, setPicked] = useState<string | null>(null);
  const [detail, setDetail] = useState("");
  const [from, setFrom] = useState("");
  const [copied, setCopied] = useState(false);
  const [status, setStatus] = useState<Status>("idle");
  const copyTimer = useRef<ReturnType<typeof setTimeout> | null>(null);
  /** Bots fill hidden fields. People do not. */
  const trap = useRef<HTMLInputElement | null>(null);

  const live = contactEndpoint.endpoint.length > 0;
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

  const canSend = from.trim().length > 3 && from.includes("@") && detail.trim().length > 0;

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

  async function send(e: React.FormEvent) {
    e.preventDefault();
    if (!canSend || status === "sending") return;
    if (trap.current?.value) return; // honeypot tripped
    setStatus("sending");
    try {
      const res = await fetch(contactEndpoint.endpoint, {
        method: "POST",
        headers: { "Content-Type": "application/json", Accept: "application/json" },
        body: JSON.stringify({
          ...(contactEndpoint.accessKey ? { access_key: contactEndpoint.accessKey } : {}),
          subject,
          email: from.trim(),
          topic: probe ? probe.label : "Not specified",
          message: detail.trim(),
        }),
      });
      if (!res.ok) throw new Error(String(res.status));
      setStatus("sent");
    } catch {
      setStatus("error");
    }
  }

  return (
    <form onSubmit={send} className="lg:grid lg:grid-cols-[minmax(0,1fr)_minmax(0,26rem)] lg:gap-x-14">
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
            name="message"
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
            name="email"
            type="email"
            inputMode="email"
            autoComplete="email"
            value={from}
            onChange={(e) => setFrom(e.target.value)}
            placeholder="you@company.com"
            className="mt-4 w-full border border-paper-4 bg-white/60 px-4 py-3.5 font-sans text-[0.92rem] text-ink-ink placeholder:text-ink-soft/70 focus:border-brand focus:outline-none"
          />
        </div>

        {/* Not for people. Kept out of the tab order and off the screen. */}
        <input
          ref={trap}
          type="text"
          name="company_website"
          tabIndex={-1}
          autoComplete="off"
          aria-hidden="true"
          className="absolute left-[-9999px] h-px w-px opacity-0"
        />
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

          {status === "sent" ? (
            <div
              role="status"
              className="mt-5 border border-brand bg-brand/5 px-5 py-4 font-sans text-[0.9rem] leading-relaxed text-ink-ink"
            >
              <p className="font-grotesk text-[1.05rem] font-bold tracking-tight2">Sent.</p>
              <p className="mt-1.5 text-ink-body">
                {guaranteeLine} If you would rather have a copy for your own records, the message is
                still above.
              </p>
            </div>
          ) : (
            <>
              <div className="mt-5 flex flex-wrap gap-3">
                {live ? (
                  <button
                    type="submit"
                    disabled={!canSend || status === "sending"}
                    className="biz-btn !border-brand !bg-brand !text-white transition-opacity hover:!border-brand-soft hover:!bg-brand-soft disabled:!cursor-not-allowed disabled:!border-paper-4 disabled:!bg-transparent disabled:!text-ink-soft"
                  >
                    {status === "sending" ? "Sending" : "Send"}
                  </button>
                ) : null}
                <a
                  href={mailto}
                  className={
                    live
                      ? "biz-btn-ghost"
                      : "biz-btn !border-brand !bg-brand !text-white hover:!border-brand-soft hover:!bg-brand-soft"
                  }
                >
                  Open in email app ↗
                </a>
              </div>

              <button
                type="button"
                onClick={copy}
                className="mt-4 font-mono text-[0.72rem] uppercase tracking-label text-ink-soft underline decoration-paper-4 underline-offset-[6px] transition-colors hover:text-brand"
              >
                {copied ? "Copied to clipboard" : "Or copy the message"}
              </button>

              {status === "error" ? (
                <p
                  role="alert"
                  className="mt-4 border-l-2 border-ember pl-4 font-sans text-[0.85rem] leading-relaxed text-ink-body"
                >
                  That did not go through. Use the mail draft or copy the message above and send it
                  yourself, and it will still reach us.
                </p>
              ) : null}

              <p className="mt-4 font-mono text-[0.7rem] leading-relaxed text-ink-soft">
                {live
                  ? "Sending delivers it straight to us. No mail app on this machine? Send is the one to use. Nothing is tracked, and the message is not stored anywhere else."
                  : "No mail app on this machine? Copy it and paste into webmail."}
              </p>
            </>
          )}
        </div>
      </div>
    </form>
  );
}

const guaranteeLine = "You get a reply from a human within one business day.";
