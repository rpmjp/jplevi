import Link from "next/link";
import { Panel } from "@/components/Panel";
import { liveDivisions, site } from "./site";

export default function NotFound() {
  return (
    <section className="mx-auto flex max-w-shell flex-col justify-center px-5 pb-24 pt-24 sm:px-8 sm:pt-32">
      <Panel className="p-8 sm:p-12">
        <div className="flex flex-wrap items-center gap-3">
          <span aria-hidden="true" className="inline-block h-1.5 w-1.5 rounded-full bg-hazard" />
          <p className="font-mono text-[0.65rem] uppercase tracking-hud text-hazard">
            Signal lost · 404
          </p>
        </div>

        <h1 className="mt-6 font-display text-3xl font-bold tracking-tight text-ink sm:text-5xl">
          This page isn&rsquo;t on the map.
        </h1>
        <p className="mt-5 max-w-xl text-base leading-relaxed text-ink-muted">
          The address you followed doesn&rsquo;t match anything at {site.domain}. It may have moved,
          or it may not exist yet.
        </p>

        <div className="mt-9 flex flex-wrap items-center gap-4">
          <Link href="/" className="btn-primary">
            Return to {site.name}
          </Link>
          {liveDivisions.map((division) => (
            <Link key={division.id} href={division.href} className="btn-ghost">
              {division.name}
            </Link>
          ))}
        </div>
      </Panel>
    </section>
  );
}
