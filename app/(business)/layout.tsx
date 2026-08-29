import type { Viewport } from "next";
import { BizHeader } from "@/components/business/BizHeader";
import { BizFooter } from "@/components/business/BizFooter";
import Link from "next/link";
import { biz, bizRoutes } from "../business";

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
      <div className="pointer-events-none fixed inset-y-0 left-0 z-30 hidden border-r border-paper-3 bg-paper xl:flex xl:w-[5.75rem] xl:flex-col xl:items-center xl:justify-between xl:py-6">
        <span
          aria-hidden="true"
          className="biz-rail shrink-0 font-mono text-[0.56rem] uppercase tracking-label text-ink-soft"
        >
          {biz.rail}
        </span>
        {/* Always-visible way in, whatever the reader has scrolled to. */}
        <div className="flex shrink-0 flex-col items-center gap-4">
          <a
            href={`tel:${biz.phoneHref}`}
            className="biz-rail shrink-0 pointer-events-auto font-grotesk text-[1rem] font-bold tracking-tight2 text-ink-ink transition-colors hover:text-brand"
          >
            {biz.phone}
          </a>
          <Link
            href={bizRoutes.contact}
            className="biz-rail shrink-0 pointer-events-auto bg-brand px-2.5 py-3 font-mono text-[0.6rem] font-semibold uppercase tracking-label text-white transition-colors hover:bg-ink-ink"
          >
            {biz.railCta} ↗
          </Link>
        </div>
      </div>

      {/* Content rides above the footer and hides it until scrolled past. */}
      <div className="relative z-10 flex min-h-screen flex-col bg-paper xl:pl-[5.75rem] lg:mb-[var(--footer-h)]">
        <BizHeader />
        <main id="main" className="flex-1">
          {children}
        </main>
      </div>
      {/* Black backstop: anything the content does not cover reads as footer,
          including the seam above it and rubber-band overscroll at the bottom. */}
      <div
        aria-hidden="true"
        className="pointer-events-none fixed inset-0 z-0 hidden bg-night lg:block"
      />

      <BizFooter />
    </div>
  );
}
