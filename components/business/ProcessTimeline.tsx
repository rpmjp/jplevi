import { process, stack } from "@/app/business";

/**
 * Engagement phases on a timeline spine. The top rules of the columns join into
 * one continuous line, with a node marking the start of each phase. No client
 * JS; the grid collapses to a vertical spine below the large breakpoint.
 */
export function ProcessTimeline() {
  return (
    <section aria-labelledby="process-heading" className="w-full px-6 pb-20 pt-0 sm:px-10 sm:pb-24 sm:pt-1">
      <div className="flex flex-wrap items-end justify-between gap-4">
        <div>
          <h2 id="process-heading" className="biz-h2">
            How a project runs
          </h2>
        </div>
        <p className="font-mono text-[0.72rem] uppercase tracking-label text-ink-soft">
          4 phases · fixed scope
        </p>
      </div>

      <ol className="mt-6 grid sm:grid-cols-2 lg:grid-cols-4">
        {process.map((phase, i) => (
          <li
            key={phase.n}
            className="relative border-t border-ink-ink pb-8 pr-0 pt-5 sm:pr-8 lg:pb-0"
          >
            {/* node on the spine */}
            <span
              aria-hidden="true"
              className="absolute -top-[4px] left-0 h-[7px] w-[7px] rounded-full bg-ink-ink"
            />
            {/* the last node closes the line */}
            {i === process.length - 1 ? (
              <span
                aria-hidden="true"
                className="absolute -top-[4px] right-0 hidden h-[7px] w-[7px] rounded-full border border-ink-ink bg-paper lg:block"
              />
            ) : null}

            <div className="flex items-center justify-between gap-3 pr-0 sm:pr-8">
              <span className="font-grotesk text-xl font-bold leading-none text-brand">
                {phase.n}
              </span>
              <span className="border border-paper-4 px-2 py-1 font-mono text-[0.62rem] uppercase tracking-label text-ink-soft">
                {phase.duration}
              </span>
            </div>

            <h3 className="mt-3 font-grotesk text-lg font-bold tracking-tight2 text-ink-ink">
              {phase.title}
            </h3>

            <p className="mt-2.5 font-sans text-[0.9rem] leading-[1.6] text-ink-body">
              {phase.body}
            </p>

            <ul className="mt-4 space-y-1.5">
              {phase.output.map((o) => (
                <li key={o} className="flex gap-2 font-mono text-[0.75rem] text-ink-ink">
                  <span aria-hidden="true" className="text-brand">
                    →
                  </span>
                  {o}
                </li>
              ))}
            </ul>
          </li>
        ))}
      </ol>

      {/* The tools those phases are carried out with. */}
      <div className="mt-10 border-t border-paper-4 pt-5">
        <h3 className="biz-label">Working stack</h3>
        <ul className="mt-4 flex flex-wrap gap-x-6 gap-y-2">
          {stack.map((item) => (
            <li key={item} className="font-mono text-[0.75rem] text-ink-body">
              {item}
            </li>
          ))}
        </ul>
      </div>
    </section>
  );
}
