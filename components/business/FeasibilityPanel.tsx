"use client";

import Link from "next/link";
import { useMemo, useState } from "react";
import {
  biz,
  bizRoutes,
  capabilities,
  feasibilityPanel as copy,
  feasibilityProbes,
} from "@/app/business";

/** Readout colour by verdict. The honest answers are not dressed up as wins. */
const VERDICT_TONE: Record<string, { dot: string; text: string }> = {
  ai: { dot: "bg-brand", text: "text-brand-soft" },
  /** Ember marks a different kind of work, never a refusal: the copy says yes. */
  "not-ai": { dot: "bg-ember", text: "text-paper" },
};

/**
 * The free feasibility review, made self-serve.
 *
 * Replaces the decorative product mock that used to sit here. A visitor picks
 * the closest problem, gets a straight answer including "this is not an AI
 * problem", and lands on the capability that actually covers it. No backend:
 * the submit is a prefilled mailto, which survives the static export.
 */
export function FeasibilityPanel() {
  const [picked, setPicked] = useState<string | null>(null);
  const [detail, setDetail] = useState("");

  const probe = feasibilityProbes.find((p) => p.id === picked) ?? null;
  /** Several complaints share one capability, so the link follows the mapping. */
  const capability = capabilities.find((c) => c.id === probe?.capabilityId) ?? null;

  const mailto = useMemo(() => {
    if (!probe) return "";
    const subject = `Feasibility review: ${probe.label}`;
    const body = [
      `Problem: ${probe.label}`,
      "",
      detail.trim() || "(adding detail below)",
      "",
      "",
      "Sent from the feasibility panel on jplevi.com",
    ].join("\n");
    return `mailto:${biz.email}?subject=${encodeURIComponent(subject)}&body=${encodeURIComponent(body)}`;
  }, [probe, detail]);

  const tone = probe ? VERDICT_TONE[probe.verdict] : null;

  return (
    <div className="w-full border border-white/12 bg-night text-paper shadow-[0_24px_60px_-30px_rgba(0,0,0,0.8)]">
      {/* title bar */}
      <div className="flex flex-wrap items-center gap-x-3 gap-y-2 border-b border-white/12 px-5 py-3.5">
        <span className="flex h-6 w-6 shrink-0 items-center justify-center bg-brand font-grotesk text-[0.75rem] font-bold text-white">
          JP
        </span>
        <p className="font-sans text-[0.95rem] text-paper">{copy.title}</p>
        <span className="ml-auto flex shrink-0 items-center gap-1.5 font-sans text-[0.75rem] text-paper/70">
          <span aria-hidden="true" className="h-1.5 w-1.5 rounded-full bg-live" />
          {copy.status}
        </span>
      </div>

      {/* step 01: the problem */}
      <div className="border-b border-white/12 px-5 py-3.5">
        <p className="font-mono text-[0.75rem] uppercase tracking-[0.08em] text-paper/70">
          01 / {copy.step1}
        </p>
        <div className="mt-3 flex flex-wrap gap-1.5">
        {feasibilityProbes.map((p) => {
          const on = p.id === picked;
          return (
            <button
              key={p.id}
              type="button"
              aria-pressed={on}
              onClick={() => setPicked(on ? null : p.id)}
              className={
                "border px-2.5 py-1.5 text-left font-sans text-[0.875rem] leading-tight transition-colors " +
                (on
                  ? "border-brand bg-brand text-white"
                  : "border-white/20 text-paper/75 hover:border-paper/50 hover:text-paper")
              }
            >
              {p.label}
            </button>
          );
        })}
        </div>
      </div>

      {/* the readout */}
      <div aria-live="polite" className="min-h-[5.5rem] border-b border-white/12 px-5 py-3.5">
        {probe && tone ? (
          <>
            <p className="flex items-center gap-2 font-sans text-[0.9rem] font-medium">
              <span aria-hidden="true" className={`h-1.5 w-1.5 rounded-full ${tone.dot}`} />
              <span className={tone.text}>{probe.call}</span>
            </p>
            <p className="mt-1.5 font-sans text-[0.875rem] leading-relaxed text-paper/80">
              {probe.read}
            </p>
            {capability ? (
              <Link
                href={`${bizRoutes.services}#${capability.id}`}
                className="mt-2 inline-flex items-baseline gap-2 font-sans text-sm tracking-normal text-paper/70 transition-colors hover:text-paper"
              >
                <span className="text-paper/35">{capability.n}</span>
                {capability.title.join(" ")} ↗
              </Link>
            ) : null}
          </>
        ) : (
          <p className="font-sans text-[0.875rem] leading-relaxed text-paper/70">{copy.prompt}</p>
        )}
      </div>

      {/* step 02: their words */}
      <div className="border-b border-white/12 px-5 py-3.5">
        <p className="mb-2.5 font-mono text-[0.75rem] uppercase tracking-[0.08em] text-paper/70">
          02 / {copy.step2}
        </p>
        <label className="sr-only" htmlFor="feasibility-detail">
          {copy.step2}
        </label>
        <textarea
          id="feasibility-detail"
          rows={2}
          value={detail}
          onChange={(e) => setDetail(e.target.value)}
          placeholder={copy.placeholder}
          className="w-full resize-none border border-white/15 bg-white/[0.04] px-3 py-2 font-sans text-base leading-relaxed text-paper placeholder:text-paper/65 focus:border-brand-soft focus:outline-none"
        />
      </div>

      {/* send */}
      <div className="px-5 py-3.5">
        {probe ? (
          <a
            href={mailto}
            className="flex w-full items-center justify-center border border-brand bg-brand px-4 py-2.5 font-sans text-sm font-semibold tracking-normal text-white transition-colors hover:border-brand-soft hover:bg-brand-soft"
          >
            {copy.cta} ↗
          </a>
        ) : (
          <span className="flex w-full items-center justify-center border border-white/15 px-4 py-2.5 font-sans text-[0.75rem] tracking-normal text-paper/35">
            {copy.ctaIdle}
          </span>
        )}
        <p className="mt-3 font-sans text-[0.8rem] leading-relaxed text-paper/70">{copy.foot}</p>
      </div>
    </div>
  );
}
