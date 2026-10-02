import Link from "next/link";
import { BizMark } from "./BizMark";
import { biz, bizRoutes } from "@/app/business";

/** Mark plus name, linking home. */
export function BizWordmark({ className = "" }: { className?: string }) {
  return (
    <Link
      href={bizRoutes.home}
      aria-label={`${biz.name} home`}
      className={`group inline-flex items-center gap-2 sm:gap-3 ${className}`}
    >
      <BizMark className="h-8 w-8 shrink-0 sm:h-10 sm:w-10" />
      <span className="font-sans text-[0.64rem] font-medium tracking-normal text-ink-ink transition-colors group-hover:text-brand">
        {biz.name}
      </span>
    </Link>
  );
}
