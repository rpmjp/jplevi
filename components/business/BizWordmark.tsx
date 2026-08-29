import Link from "next/link";
import { BizMark } from "./BizMark";
import { biz, bizRoutes } from "@/app/business";

/** Mark plus name, linking home. */
export function BizWordmark({ className = "" }: { className?: string }) {
  return (
    <Link
      href={bizRoutes.home}
      aria-label={`${biz.name} home`}
      className={`group inline-flex items-center gap-3 ${className}`}
    >
      <BizMark className="h-10 w-10 shrink-0" />
      <span className="font-mono text-[0.64rem] font-medium uppercase tracking-label text-ink-ink transition-colors group-hover:text-brand">
        {biz.name}
      </span>
    </Link>
  );
}
