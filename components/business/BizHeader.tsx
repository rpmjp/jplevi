import Link from "next/link";
import { BizWordmark } from "./BizWordmark";
import { biz, bizNav } from "@/app/business";

export function BizHeader() {
  return (
    <header className="sticky top-0 z-40 border-b border-paper-3 bg-paper/90 backdrop-blur supports-[backdrop-filter]:bg-paper/75">
      <div className="mx-auto flex max-w-biz flex-wrap items-center gap-x-10 gap-y-4 px-6 py-6 sm:px-10">
        <BizWordmark />

        <nav aria-label="Main" className="order-3 w-full lg:order-2 lg:ml-[295px] lg:w-auto">
          <ul className="flex flex-wrap items-center gap-x-[42px] gap-y-2">
            {bizNav.map((item) => (
              <li key={item.href}>
                <Link
                  href={item.href}
                  className="font-sans text-[1rem] text-ink-body transition-colors hover:text-brand"
                >
                  {item.label}
                </Link>
              </li>
            ))}
          </ul>
        </nav>

        <p className="order-2 ml-auto flex items-center gap-2.5 lg:order-3">
          <span aria-hidden="true" className="inline-block h-2 w-2 rounded-full bg-live-deep" />
          <span className="font-sans text-[1rem] text-ink-body">{biz.availability}</span>
        </p>
      </div>
    </header>
  );
}
