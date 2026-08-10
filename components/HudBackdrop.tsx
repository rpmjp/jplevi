/**
 * Fixed, non-interactive cockpit backdrop: faint grid, a slow teal scan sweep,
 * and scanlines. Pure CSS — the motion is disabled by prefers-reduced-motion.
 */
export function HudBackdrop() {
  return (
    <div aria-hidden="true" className="pointer-events-none fixed inset-0 -z-10 overflow-hidden">
      <div className="absolute inset-0 bg-gun-800" />
      <div className="absolute inset-0 bg-grid bg-grid-cell opacity-60" />
      <div className="scanlines absolute inset-0 opacity-70" />
      {/* Slow horizontal sweep line. */}
      <div className="absolute inset-x-0 top-0 h-24 animate-scan bg-gradient-to-b from-transparent via-hud/[0.06] to-transparent" />
      {/* Warm horizon glow, keeps the base from reading as flat black. */}
      <div className="absolute -bottom-40 left-1/2 h-96 w-[120vw] -translate-x-1/2 rounded-[50%] bg-signal/[0.06] blur-3xl" />
      <div className="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-hud/25 to-transparent" />
    </div>
  );
}
