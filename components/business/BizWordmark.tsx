import Link from "next/link";
import { biz, bizRoutes } from "@/app/business";

/** JP / AI lockup: two stacked cells split by a blue slash. */
export function BizWordmark({ className = "" }: { className?: string }) {
  return (
    <Link
      href={bizRoutes.home}
      aria-label={`${biz.name} home`}
      className={`group inline-flex items-center gap-3 ${className}`}
    >
      <span className="relative inline-flex h-11 w-11 shrink-0 items-center justify-center border border-ink-ink">
        <span className="absolute left-1 top-0.5 font-grotesk text-[0.86rem] font-black leading-none text-ink-ink">
          JP
        </span>
        <span className="absolute bottom-0.5 right-1 font-grotesk text-[0.86rem] font-black leading-none text-ink-soft">
          AI
        </span>
        <span
          aria-hidden="true"
          className="absolute inset-0 origin-center rotate-[24deg] border-r border-brand"
        />
      </span>
      <span className="self-end pb-0.5 font-mono text-[0.64rem] font-medium uppercase tracking-label text-ink-ink transition-colors group-hover:text-brand">
        {biz.name}
      </span>
    </Link>
  );
}
