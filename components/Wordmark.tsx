import Link from "next/link";
import { site } from "@/app/site";

/** JP Levi wordmark — "JP" in signal orange, "LEVI" in hull white. */
export function Wordmark({ className = "" }: { className?: string }) {
  return (
    <Link
      href="/"
      aria-label={`${site.name} — home`}
      className={`group inline-flex items-baseline gap-[0.3rem] font-display text-lg font-bold uppercase tracking-wordmark ${className}`}
    >
      <span className="text-signal transition-colors group-hover:text-signal-soft">JP</span>
      <span className="text-ink">LEVI</span>
    </Link>
  );
}
