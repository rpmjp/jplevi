"use client";

import Link from "next/link";
import { usePathname } from "next/navigation";
import { BizWordmark } from "./BizWordmark";
import { biz, bizNav } from "@/app/business";

export function BizHeader() {
  const pathname = usePathname();

  return (
    <header className="sticky top-0 z-40 border-b border-paper-3 bg-paper/90 backdrop-blur supports-[backdrop-filter]:bg-paper/75">
      <div className="mx-auto grid min-h-[76px] max-w-biz grid-cols-[auto_1fr_auto] items-center gap-x-6 px-6 py-4 sm:px-10 lg:h-[88px] lg:py-0">
        <BizWordmark />

        <nav
          aria-label="Main"
          className="col-span-3 row-start-2 mt-3 border-t border-paper-3 pt-3 lg:col-span-1 lg:col-start-2 lg:row-start-1 lg:mt-0 lg:justify-self-center lg:border-0 lg:pt-0"
        >
          <ul className="flex flex-wrap items-center gap-x-7 gap-y-2 sm:gap-x-10">
            {bizNav.map((item) => (
              <li key={item.href}>
                <Link
                  href={item.href}
                  aria-current={
                    pathname === item.href.slice(0, -1) || pathname === item.href
                      ? "page"
                      : undefined
                  }
                  className={`relative py-2 font-sans text-[0.92rem] transition-colors after:absolute after:inset-x-0 after:-bottom-0.5 after:h-px after:origin-left after:bg-brand after:transition-transform hover:text-brand ${
                    pathname === item.href.slice(0, -1) || pathname === item.href
                      ? "text-ink-ink after:scale-x-100"
                      : "text-ink-body after:scale-x-0 hover:after:scale-x-100"
                  }`}
                >
                  {item.label}
                </Link>
              </li>
            ))}
          </ul>
        </nav>

        <div className="col-start-3 row-start-1 ml-auto flex items-center gap-x-6">
          {/* The number stays visible at every width; the status line is the
              first thing to go when space runs out. */}
          <a
            href={`tel:${biz.phoneHref}`}
            className="font-grotesk text-[1.05rem] font-bold tracking-tight2 text-ink-ink transition-colors hover:text-brand"
          >
            {biz.phone}
          </a>
          <p className="hidden items-center gap-2.5 lg:flex">
            <span aria-hidden="true" className="inline-block h-2 w-2 rounded-full bg-live-deep" />
            <span className="font-sans text-[0.88rem] text-ink-body">{biz.availability}</span>
          </p>
        </div>
      </div>
    </header>
  );
}
