import type { Metadata } from "next";
import Link from "next/link";
import { biz, bizRoutes, hostingTiers } from "../../business";

const description =
  "Offsite hosting and maintenance for the applications we build: provisioning, hardening, TLS, deploys, backups, monitoring, and patching, on infrastructure we manage for you.";

export const metadata: Metadata = {
  title: "Managed hosting",
  description,
  alternates: { canonical: bizRoutes.hosting },
  openGraph: { title: `Managed hosting | ${biz.name}`, description, url: `${biz.url}/hosting/`, type: "article" },
};

const included = [
  { t: "Provisioning", b: "Server sized to the workload, built from a repeatable configuration rather than by hand." },
  { t: "Hardening", b: "Firewall, key-only SSH, unattended security updates, fail2ban, least-privilege service accounts." },
  { t: "TLS", b: "Certificates issued and renewed automatically. HTTPS redirects and modern ciphers by default." },
  { t: "Deploys", b: "Push-to-deploy pipelines with a rollback path that has actually been tested." },
  { t: "Backups", b: "Scheduled, off-server, and restore-tested. A backup nobody has restored is a rumour." },
  { t: "Monitoring", b: "Uptime, disk, memory, and error alerting that reaches a human before your customers do." },
  { t: "Patching", b: "Regular OS and dependency updates, with a note to you when something needs a decision." },
  { t: "The application", b: "Not only the box underneath it. The software we built stays maintained by the people who built it." },
];

export default function HostingPage() {
  return (
    <div className="w-full">
      {/* ---- Masthead ---------------------------------------------------- */}
      <section className="px-6 pb-14 pt-14 sm:px-10 sm:pt-20">
        <p className="biz-label-blue">Infrastructure</p>
        <h1 className="biz-display mt-5 max-w-[15ch] text-[clamp(2.6rem,7.5vw,6rem)]">
          We keep it running
          <span className="ml-3 inline-block h-[0.13em] w-[0.13em] rounded-full bg-brand align-baseline" />
        </h1>
        <p className="biz-lead mt-10 max-w-xl">
          Building the thing is half the job. Someone has to keep it online, patched, and backed up
          afterwards. For most clients that someone is us.
        </p>
      </section>

      {/* ---- What offsite actually means -----------------------------------
          The hardware sits with an infrastructure partner. Nothing on this page
          claims the metal is ours, because it is not. Everything above it is. */}
      <section className="bg-night px-6 py-16 sm:px-10 sm:py-20">
        <p className="biz-label !text-paper/45">Offsite, and managed</p>
        <p className="mt-7 max-w-[24ch] font-grotesk text-[clamp(1.9rem,5.5vw,4rem)] font-black uppercase leading-[0.92] tracking-tight3 text-paper">
          One place to call when something breaks
        </p>
        <div className="mt-10 grid gap-10 border-t border-white/15 pt-9 lg:grid-cols-2 lg:gap-x-16">
          <p className="max-w-prose2 font-sans text-[1.02rem] leading-[1.6] text-paper/75">
            The hardware sits in a data centre run by an infrastructure partner, which is how almost
            every hosting arrangement works whether or not anyone says so. Everything above it is
            ours: the server, the stack, the deploys, and the application itself.
          </p>
          <p className="max-w-prose2 font-sans text-[1.02rem] leading-[1.6] text-paper/75">
            You deal with one company rather than a hosting account, a developer, and a support
            queue that each blame the other two. When something breaks it is our problem, and you do
            not have to work out whose.
          </p>
        </div>
      </section>

      {/* ---- Tiers --------------------------------------------------------- */}
      <section className="px-6 py-16 sm:px-10 sm:py-20">
        <div className="flex flex-wrap items-end justify-between gap-6">
          <h2 className="biz-h2">Two ways in</h2>
          <p className="font-mono text-[0.7rem] uppercase tracking-label text-ink-soft">
            Quoted per engagement
          </p>
        </div>
        <div className="biz-rule-draw mt-5 bg-paper-4" />

        <div className="mt-12 space-y-16">
          {hostingTiers.map((tier) => (
            <article key={tier.n} className="border-t border-ink-ink pt-8">
              <div className="lg:grid lg:grid-cols-[minmax(0,1fr)_minmax(0,1fr)] lg:gap-x-14">
                <div>
                  <span className="font-mono text-[0.72rem] text-brand">{tier.n}</span>
                  <h3 className="mt-3 font-grotesk text-[clamp(1.8rem,4.5vw,3rem)] font-black uppercase leading-[0.95] tracking-tight3 text-ink-ink">
                    {tier.name}
                  </h3>
                  <p className="mt-5 max-w-prose2 font-sans text-[1.02rem] leading-[1.6] text-ink-ink">
                    {tier.forWhom}
                  </p>
                </div>

                <dl className="mt-8 lg:mt-0">
                  {tier.specs.map(([k, v]) => (
                    <div key={k} className="flex gap-5 border-b border-paper-3 py-3">
                      <dt className="w-32 shrink-0 font-mono text-[0.66rem] uppercase tracking-label text-ink-soft">
                        {k}
                      </dt>
                      <dd className="font-mono text-[0.82rem] text-ink-ink">{v}</dd>
                    </div>
                  ))}
                </dl>
              </div>

              {/* Sizes, where the tier has them. We move you between these as
                  load changes rather than making it your problem. */}
              {"sizes" in tier ? (
                <div className="mt-10">
                  <p className="font-mono text-[0.66rem] uppercase tracking-label text-ink-soft">
                    Sizes, moved between as load changes
                  </p>
                  <ul className="mt-4 grid gap-4 sm:grid-cols-3">
                    {tier.sizes.map((s) => (
                      <li key={s.label} className="border border-paper-4 p-5">
                        <p className="font-grotesk text-[1.1rem] font-bold tracking-tight2 text-ink-ink">
                          {s.label}
                        </p>
                        <ul className="mt-3 space-y-1.5 font-mono text-[0.78rem] text-ink-body">
                          <li>{s.cpu}</li>
                          <li>{s.ram}</li>
                          <li>{s.disk}</li>
                          <li>{s.net}</li>
                        </ul>
                      </li>
                    ))}
                  </ul>
                </div>
              ) : null}
            </article>
          ))}
        </div>
      </section>

      {/* ---- What managed covers -------------------------------------------- */}
      <section className="px-6 pb-16 sm:px-10 sm:pb-20">
        <div className="border-t border-ink-ink pt-10">
          <h2 className="biz-h2">What managed covers</h2>
          <ul className="mt-10 grid gap-x-12 gap-y-9 sm:grid-cols-2">
            {included.map((item, i) => (
              <li key={item.t}>
                <div className="flex items-baseline gap-3">
                  <span className="font-mono text-[0.7rem] text-brand">
                    {String(i + 1).padStart(2, "0")}
                  </span>
                  <h3 className="biz-h3">{item.t}</h3>
                </div>
                <p className="mt-2.5 font-mono text-[0.84rem] leading-relaxed text-ink-body">
                  {item.b}
                </p>
              </li>
            ))}
          </ul>
        </div>
      </section>

      {/* ---- Who it suits, and the exit ---------------------------------------- */}
      <section className="px-6 pb-16 sm:px-10 sm:pb-20">
        <div className="grid gap-10 border-t border-paper-3 pt-10 lg:grid-cols-2 lg:gap-x-16">
          <div>
            <h2 className="biz-h2">Who this suits</h2>
            <p className="mt-5 max-w-prose2 font-mono text-[0.88rem] leading-[1.8] text-ink-body">
              Teams running a handful of applications who do not have, and do not want, a full-time
              operations hire. If you already have a platform team, you do not need this.
            </p>
          </div>
          <div>
            <h2 className="biz-h2">No lock-in</h2>
            <p className="mt-5 max-w-prose2 font-mono text-[0.88rem] leading-[1.8] text-ink-body">
              Configuration lives in your repository. If you want to take it in-house you get the
              keys, the runbook, and a handover call rather than an argument.
            </p>
          </div>
        </div>
      </section>

      {/* ---- Quote ---------------------------------------------------------- */}
      <section className="px-6 pb-16 sm:px-10 sm:pb-20">
        <div className="border border-ink-ink p-9 sm:p-12">
          <h2 className="biz-h2">Pricing</h2>
          <p className="mt-5 max-w-prose2 font-mono text-[0.9rem] leading-[1.8] text-ink-body">
            Quoted per engagement, based on which tier you need, how many applications you run, and
            how much of the software we are maintaining. Tell us what you are running and we will
            send a number.
          </p>
          <div className="mt-8 flex flex-wrap items-center gap-x-6 gap-y-3">
            <Link href={bizRoutes.contact} className="biz-btn">
              Request a quote
            </Link>
            <a
              href={`tel:${biz.phoneHref}`}
              className="font-mono text-[0.78rem] text-ink-body transition-colors hover:text-brand"
            >
              or call {biz.phone}
            </a>
          </div>
        </div>
      </section>
    </div>
  );
}
