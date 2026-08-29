import Link from "next/link";
import { BizHeader } from "@/components/business/BizHeader";
import { BizFooter } from "@/components/business/BizFooter";
import { biz, bizNav } from "./business";
import { site } from "./site";

/**
 * Root 404, exported as out/404.html and served for every unmatched path by
 * .htaccess. It wears the business chrome because that is the primary site.
 */
export default function NotFound() {
  return (
    <div className="biz-scope flex min-h-screen flex-col">
      <BizHeader />
      <main id="main" className="flex-1">
        <section className="mx-auto max-w-biz px-5 pb-20 pt-20 sm:px-8 sm:pt-28">
          <p className="biz-label !text-ember">Error 404</p>
          <h1 className="biz-display mt-5 text-[2.8rem] sm:text-[4.4rem]">
            No route to that page
            <span className="ml-1 inline-block h-[0.18em] w-[0.18em] rounded-full bg-brand align-baseline" />
          </h1>
          <p className="biz-lead mt-8 max-w-lg">
            Nothing is served at that address on {site.domain}. It may have moved, or it may never
            have existed.
          </p>

          <div className="mt-12 border-t border-paper-3 pt-8">
            <h2 className="biz-label">Try one of these</h2>
            <ul className="mt-6 flex flex-wrap gap-3">
              <li>
                <Link href="/" className="biz-btn">
                  Home
                </Link>
              </li>
              {bizNav.map((item) => (
                <li key={item.href}>
                  <Link href={item.href} className="biz-btn-ghost">
                    {item.label}
                  </Link>
                </li>
              ))}
              <li>
                <Link href="/mechablast/" className="biz-btn-ghost">
                  MechaBlast
                </Link>
              </li>
            </ul>
            <p className="mt-8 font-mono text-[0.8rem] text-ink-soft">
              Still stuck? {biz.email}
            </p>
          </div>
        </section>
      </main>
      <BizFooter />
    </div>
  );
}
