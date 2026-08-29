import type { Viewport } from "next";
import { BizHeader } from "@/components/business/BizHeader";
import { BizFooter } from "@/components/business/BizFooter";
import { biz } from "../business";

export const viewport: Viewport = {
  themeColor: "#F5F3EF",
  colorScheme: "light",
};

/**
 * JP Levi AI chrome. The gaming side keeps its own dark layouts, so the two
 * halves of the site read as separate properties on purpose.
 */
export default function BusinessLayout({ children }: { children: React.ReactNode }) {
  return (
    <div className="biz-scope flex min-h-screen flex-col">
      {/* Vertical rail down the left edge, desktop only. */}
      <div
        aria-hidden="true"
        className="pointer-events-none fixed inset-y-0 left-0 z-30 hidden border-r border-paper-3 bg-paper xl:flex xl:w-[5.75rem] xl:flex-col xl:items-center xl:justify-between xl:py-8"
      >
        <span className="biz-rail font-mono text-[0.62rem] uppercase tracking-label text-ink-soft">
          {biz.rail}
        </span>
        <span className="biz-rail font-mono text-[0.62rem] uppercase tracking-label text-ink-soft">
          {biz.coords}
        </span>
      </div>

      <div className="flex min-h-screen flex-col xl:pl-[5.75rem]">
        <BizHeader />
        <main id="main" className="flex-1">
          {children}
        </main>
        <BizFooter />
      </div>
    </div>
  );
}
