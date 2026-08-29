import Link from "next/link";
import { biz, bizFooterNav } from "@/app/business";
import { site } from "@/app/site";

/**
 * Dark footer, revealed by the page content sliding up over it.
 *
 * From lg up it is fixed behind the content, which carries a solid paper
 * background and a matching bottom margin so there is room to scroll into.
 * Below lg it sits in normal flow, where a fixed height would be fragile.
 */
export function BizFooter() {
  const year = new Date().getFullYear();

  return (
    <footer className="biz-footer bg-night text-paper lg:fixed lg:inset-x-0 lg:bottom-0 lg:z-0 lg:h-[var(--footer-h)] xl:left-[5.75rem]">
      <div className="flex h-full flex-col justify-between px-6 py-12 sm:px-10">
        <div className="grid gap-10 md:grid-cols-[minmax(0,1.5fr)_minmax(0,1fr)_minmax(0,1fr)]">
          <div>
            <p className="font-grotesk text-2xl font-bold uppercase tracking-tight2 text-paper">
              {biz.name}
            </p>
            <p className="mt-4 max-w-sm font-mono text-[0.82rem] leading-relaxed text-paper/65">
              {biz.lead}
            </p>
            <div className="mt-6 flex flex-col gap-2">
              <a
                href={`mailto:${biz.email}`}
                className="inline-block font-mono text-[0.8rem] font-medium uppercase tracking-label text-brand-soft underline decoration-brand-soft/30 underline-offset-[6px] transition-colors hover:decoration-brand-soft"
              >
                {biz.email}
              </a>
              <a
                href={`tel:${biz.phoneHref}`}
                className="inline-block font-mono text-[0.8rem] text-paper/65 transition-colors hover:text-brand-soft"
              >
                {biz.phone}
              </a>
            </div>
          </div>

          <nav aria-label="Footer">
            <h2 className="font-mono text-[0.66rem] uppercase tracking-label text-paper/45">
              Site
            </h2>
            <div className="mt-3 h-px w-full bg-white/15" />
            <ul className="mt-4 space-y-2.5">
              {bizFooterNav.map((item) => (
                <li key={item.href}>
                  <Link
                    href={item.href}
                    className="font-mono text-[0.82rem] text-paper/80 transition-colors hover:text-brand-soft"
                  >
                    {item.label}
                  </Link>
                </li>
              ))}
            </ul>
          </nav>

          <div>
            <h2 className="font-mono text-[0.66rem] uppercase tracking-label text-paper/45">
              Company
            </h2>
            <div className="mt-3 h-px w-full bg-white/15" />
            <ul className="mt-4 space-y-2.5 font-mono text-[0.82rem] text-paper/80">
              <li>{site.legalName}</li>
              <li>{biz.location}</li>
              <li>Est. {biz.founded}</li>
              <li>
                {/* The gaming side stays a quiet door, not a nav item. */}
                <Link href="/gaming/" className="transition-colors hover:text-brand-soft">
                  Games division
                </Link>
              </li>
            </ul>
          </div>
        </div>

        <div className="mt-10 flex flex-wrap items-center justify-between gap-3 border-t border-white/15 pt-5">
          <p className="font-mono text-[0.68rem] uppercase tracking-label text-paper/45">
            © {year} {site.legalName}. All rights reserved.
          </p>
        </div>
      </div>
    </footer>
  );
}
