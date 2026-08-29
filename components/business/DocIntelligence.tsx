/** Product panel from the reference: a document run through the pipeline. */
const NODES = [
  { id: "enterprise", label: ["Enterprise", "Accounts"], x: 148, y: 40, hot: false },
  { id: "market", label: ["Market", "Demand"], x: 44, y: 128, hot: false },
  { id: "revenue", label: ["Revenue", "Increase"], x: 172, y: 132, hot: true },
  { id: "sales", label: ["Sales", "Strategy"], x: 172, y: 222, hot: false },
];
const LINKS: [string, string][] = [
  ["enterprise", "revenue"], ["market", "revenue"], ["revenue", "sales"], ["enterprise", "market"],
];
const byId = Object.fromEntries(NODES.map((n) => [n.id, n]));

export function DocIntelligence() {
  return (
    <div className="w-full border border-white/12 bg-night text-paper shadow-[0_24px_60px_-30px_rgba(0,0,0,0.8)]">
      {/* title bar */}
      <div className="flex items-center gap-3 border-b border-white/12 px-5 py-3.5">
        <span className="flex h-6 w-6 items-center justify-center bg-brand font-grotesk text-[0.62rem] font-black text-white">
          JP
        </span>
        <p className="font-sans text-[0.82rem] text-paper">Document Intelligence</p>
      </div>

      <div className="grid grid-cols-[minmax(0,1fr)_minmax(0,1fr)]">
        {/* findings */}
        <div className="border-r border-white/12">
          <div className="border-b border-white/12 px-5 py-3">
            <p className="font-sans text-[0.72rem] text-paper/55">Document</p>
          </div>
          <div className="flex items-center justify-between gap-3 border-b border-white/12 px-5 py-3">
            <p className="font-sans text-[0.74rem] text-paper">Q2 Financial Report.pdf</p>
            <span className="flex shrink-0 items-center gap-1.5 font-sans text-[0.66rem] text-paper/70">
              <span aria-hidden="true" className="h-1.5 w-1.5 rounded-full bg-live" />
              Processed
            </span>
          </div>
          <div className="border-b border-white/12 px-5 py-2.5">
            <p className="font-mono text-[0.6rem] uppercase tracking-label text-paper/45">Key findings</p>
          </div>
          <div className="border-b border-white/12 px-5 py-3.5">
            <p className="font-sans text-[0.76rem] font-medium text-paper">Revenue Increase</p>
            <p className="mt-1.5 font-sans text-[0.68rem] leading-relaxed text-paper/60">
              Revenue increased 18.6% in Q2 primarily due to expansion in enterprise accounts.
            </p>
          </div>
          <div className="px-5 py-3.5">
            <p className="font-sans text-[0.76rem] font-medium text-paper">Top Drivers</p>
          </div>
        </div>

        {/* knowledge graph */}
        <div className="px-5 py-3">
          <p className="font-mono text-[0.6rem] uppercase tracking-label text-paper/45">Knowledge graph</p>
          <svg viewBox="0 0 240 280" className="mt-3 h-auto w-full" role="img" aria-label="Knowledge graph linking enterprise accounts, market demand, revenue increase, and sales strategy">
            <g stroke="#FFFFFF" strokeOpacity="0.28" strokeWidth="1">
              {LINKS.map(([a, b], i) => (
                <line key={i} x1={byId[a].x} y1={byId[a].y} x2={byId[b].x} y2={byId[b].y} />
              ))}
            </g>
            {NODES.map((n) => (
              <g key={n.id}>
                <circle
                  cx={n.x}
                  cy={n.y}
                  r="31"
                  fill={n.hot ? "#1B3EF0" : "#0B0B0C"}
                  stroke={n.hot ? "#5A76FF" : "#FFFFFF"}
                  strokeOpacity={n.hot ? 1 : 0.3}
                  strokeWidth="1"
                />
                {n.label.map((line, i) => (
                  <text
                    key={i}
                    x={n.x}
                    y={n.y - 2 + i * 11}
                    textAnchor="middle"
                    fontSize="8.5"
                    fill="#FFFFFF"
                    fillOpacity={n.hot ? 1 : 0.75}
                    fontFamily="var(--font-body), sans-serif"
                  >
                    {line}
                  </text>
                ))}
              </g>
            ))}
          </svg>
        </div>
      </div>
    </div>
  );
}
