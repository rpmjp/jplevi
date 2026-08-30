"use client";

import { useCallback, useEffect, useRef, useState } from "react";
import { bizRoutes, capabilities } from "@/app/business";

/**
 * Horizontal rail of capabilities driving a workbench panel beneath it.
 *
 * Selection follows whichever card is nearest the centre of the rail while
 * scrolling, and can also be driven by click or arrow keys. Implemented as a
 * tablist so it is operable without a pointer; scrolling is the enhancement,
 * not the only way in.
 */
export function CapabilityRail() {
  const railRef = useRef<HTMLDivElement | null>(null);
  const cardRefs = useRef<(HTMLButtonElement | null)[]>([]);
  const [active, setActive] = useState(0);
  const [reduced, setReduced] = useState(false);
  const programmatic = useRef(false);

  useEffect(() => {
    const mq = window.matchMedia("(prefers-reduced-motion: reduce)");
    const apply = () => setReduced(mq.matches);
    apply();
    mq.addEventListener("change", apply);
    return () => mq.removeEventListener("change", apply);
  }, []);

  /** Nearest card to the rail's centre wins. */
  const syncToScroll = useCallback(() => {
    const rail = railRef.current;
    if (!rail || programmatic.current) return;
    const mid = rail.scrollLeft + rail.clientWidth / 2;
    let best = 0;
    let bestDist = Infinity;
    cardRefs.current.forEach((el, i) => {
      if (!el) return;
      const centre = el.offsetLeft + el.offsetWidth / 2;
      const d = Math.abs(centre - mid);
      if (d < bestDist) {
        bestDist = d;
        best = i;
      }
    });
    setActive(best);
  }, []);

  useEffect(() => {
    const rail = railRef.current;
    if (!rail) return;
    let frame = 0;
    const onScroll = () => {
      cancelAnimationFrame(frame);
      frame = requestAnimationFrame(syncToScroll);
    };
    rail.addEventListener("scroll", onScroll, { passive: true });
    return () => {
      rail.removeEventListener("scroll", onScroll);
      cancelAnimationFrame(frame);
    };
  }, [syncToScroll]);

  const focusCard = useCallback(
    (i: number) => {
      const rail = railRef.current;
      const el = cardRefs.current[i];
      if (!rail || !el) return;
      setActive(i);
      programmatic.current = true;
      rail.scrollTo({
        left: el.offsetLeft - (rail.clientWidth - el.offsetWidth) / 2,
        behavior: reduced ? "auto" : "smooth",
      });
      window.setTimeout(() => {
        programmatic.current = false;
      }, reduced ? 0 : 420);
    },
    [reduced],
  );

  const onKeyDown = (e: React.KeyboardEvent) => {
    const last = capabilities.length - 1;
    let next: number | null = null;
    if (e.key === "ArrowRight") next = Math.min(active + 1, last);
    if (e.key === "ArrowLeft") next = Math.max(active - 1, 0);
    if (e.key === "Home") next = 0;
    if (e.key === "End") next = last;
    if (next === null) return;
    e.preventDefault();
    focusCard(next);
    cardRefs.current[next]?.focus();
  };

  const current = capabilities[active];

  return (
    <section aria-labelledby="capabilities-heading" className="w-full py-20 sm:py-24">
      <div className="flex flex-wrap items-end justify-between gap-6 px-6 sm:px-10">
        <div>
          <h2 id="capabilities-heading" className="biz-h2">
            What we build
          </h2>
        </div>
        <div className="flex items-center gap-5">
          <p className="hidden font-mono text-[0.66rem] uppercase tracking-label text-ink-soft sm:block">
            Drag or use arrows
          </p>
          <p className="font-mono text-[0.72rem] uppercase tracking-label text-ink-soft">
            {current.n} / {String(capabilities.length).padStart(2, "0")}
          </p>
          <div className="flex gap-2">
            <button
              type="button"
              onClick={() => focusCard(Math.max(active - 1, 0))}
              disabled={active === 0}
              aria-label="Previous capability"
              className="flex h-9 w-9 items-center justify-center border border-paper-4 text-ink-ink transition-colors hover:border-ink-ink disabled:opacity-30"
            >
              ←
            </button>
            <button
              type="button"
              onClick={() => focusCard(Math.min(active + 1, capabilities.length - 1))}
              disabled={active === capabilities.length - 1}
              aria-label="Next capability"
              className="flex h-9 w-9 items-center justify-center border border-paper-4 text-ink-ink transition-colors hover:border-ink-ink disabled:opacity-30"
            >
              →
            </button>
          </div>
        </div>
      </div>

      <div className="biz-rule mx-6 mt-4 !w-auto sm:mx-10" />

      {/* ---- rail ---- */}
      <div
        ref={railRef}
        role="tablist"
        aria-label="Capabilities"
        aria-orientation="horizontal"
        onKeyDown={onKeyDown}
        className="mt-4 flex snap-x snap-mandatory gap-4 overflow-x-auto px-6 pb-2 [scrollbar-width:none] sm:px-10 [&::-webkit-scrollbar]:hidden"
      >
        {capabilities.map((c, i) => {
          const on = i === active;
          return (
            <button
              key={c.id}
              ref={(el) => {
                cardRefs.current[i] = el;
              }}
              type="button"
              role="tab"
              id={`cap-tab-${c.id}`}
              aria-selected={on}
              aria-controls="cap-panel"
              tabIndex={on ? 0 : -1}
              onClick={() => focusCard(i)}
              className={`w-[15.5rem] shrink-0 snap-center border p-4 text-left transition-[border-color,background-color,box-shadow] sm:w-[17rem] ${
                on
                  ? "border-ink-ink bg-paper-2 shadow-[inset_0_3px_0_0_#1B3EF0]"
                  : "border-paper-3 bg-white/40 hover:border-paper-4"
              }`}
            >
              <span
                className={`font-mono text-[0.68rem] ${on ? "text-brand" : "text-ink-soft"}`}
              >
                {c.n}
              </span>
              <span
                className={`mt-3 block font-grotesk text-[1.05rem] font-bold leading-[1.12] tracking-tight2 ${on ? "text-brand-deep" : "text-ink-ink"}`}
              >
                {c.title[0]}
                <br />
                {c.title[1]}
              </span>
              <span className="mt-3 block font-mono text-[0.62rem] uppercase tracking-label text-ink-soft">
                {c.tag}
              </span>
            </button>
          );
        })}
      </div>

      {/* ---- workbench ---- */}
      <div className="px-6 sm:px-10">
        <div
          id="cap-panel"
          role="tabpanel"
          aria-labelledby={`cap-tab-${current.id}`}
          className="mt-3 border border-ink-ink bg-white/60 shadow-[inset_0_3px_0_0_#1B3EF0]"
        >
          <div className="flex flex-wrap items-center justify-between gap-3 border-b border-paper-3 px-6 py-2.5">
            <p className="font-mono text-[0.7rem] uppercase tracking-label text-ink-ink">
              {current.title.join(" ")}
            </p>
            <p className="flex items-center gap-2 font-mono text-[0.66rem] text-ink-soft">
              <span aria-hidden="true" className="h-1.5 w-1.5 rounded-full bg-live-deep" />
              {current.status}
            </p>
          </div>

          <div className="grid gap-6 p-5 sm:p-6 lg:grid-cols-[minmax(0,1.3fr)_minmax(0,1fr)]">
            <div>
              <p className="max-w-prose2 font-sans text-[0.96rem] leading-[1.65] text-ink-body">
                {current.blurb}
              </p>
              <ul className="mt-4 space-y-1.5">
                {current.delivers.map((d) => (
                  <li
                    key={d}
                    className="flex gap-3 font-sans text-[0.9rem] leading-relaxed text-ink-body"
                  >
                    <span aria-hidden="true" className="mt-2 h-1.5 w-1.5 shrink-0 bg-brand" />
                    {d}
                  </li>
                ))}
              </ul>
            </div>

            <dl className="border-t border-paper-3 pt-5 lg:border-l lg:border-t-0 lg:pl-6 lg:pt-0">
              {current.meta.map((m) => (
                <div key={m.label} className="border-b border-paper-3 py-2 first:pt-0">
                  <dt className="font-mono text-[0.62rem] uppercase tracking-label text-ink-soft">
                    {m.label}
                  </dt>
                  <dd className="mt-1 font-mono text-[0.8rem] text-ink-ink">{m.value}</dd>
                </div>
              ))}
            </dl>
          </div>
        </div>
      </div>
    </section>
  );
}
