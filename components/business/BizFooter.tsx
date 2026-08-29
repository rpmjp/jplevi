import Link from "next/link";
import { biz, bizFooterNav } from "@/app/business";
import { site } from "@/app/site";

export function BizFooter() {
  const year = new Date().getFullYear();

  return (
    <footer className="mt-28 border-t border-paper-3 bg-paper-2">
      <div className="mx-auto grid max-w-biz gap-12 px-5 py-16 sm:px-8 md:grid-cols-[minmax(0,1.5fr)_minmax(0,1fr)_minmax(0,1fr)]">
        <div>
          <p className="font-grotesk text-2xl font-black uppercase tracking-tight2 text-ink-ink">
            {biz.name}
          </p>
          <p className="mt-4 max-w-sm font-mono text-[0.85rem] leading-relaxed text-ink-body">
            {biz.lead}
          </p>
          <a href={`mailto:${biz.email}`} className="biz-link mt-6 inline-block">
            {biz.email}
          </a>
        </div>

        <nav aria-label="Footer">
          <h2 className="biz-label">Site</h2>
          <div className="biz-rule mt-3" />
          <ul className="mt-4 space-y-2.5">
            {bizFooterNav.map((item) => (
              <li key={item.href}>
                <Link
                  href={item.href}
                  className="font-mono text-[0.85rem] text-ink-body transition-colors hover:text-brand"
                >
                  {item.label}
                </Link>
              </li>
            ))}
          </ul>
        </nav>

        <div>
          <h2 className="biz-label">Company</h2>
          <div className="biz-rule mt-3" />
          <ul className="mt-4 space-y-2.5 font-mono text-[0.85rem] text-ink-body">
            <li>{site.legalName}</li>
            <li>{biz.location}</li>
            <li>
              {/* The gaming side is deliberately a quiet door, not a nav item. */}
              <Link href="/gaming/" className="transition-colors hover:text-brand">
                Games division
              </Link>
            </li>
          </ul>
        </div>
      </div>

      <div className="border-t border-paper-3">
        <div className="mx-auto flex max-w-biz flex-wrap items-center justify-between gap-3 px-5 py-6 sm:px-8">
          <p className="font-mono text-[0.7rem] uppercase tracking-label text-ink-soft">
            © {year} {site.legalName}. All rights reserved.
          </p>
          <p className="font-mono text-[0.7rem] uppercase tracking-label text-ink-soft">
            {biz.coords}
          </p>
        </div>
      </div>
    </footer>
  );
}
