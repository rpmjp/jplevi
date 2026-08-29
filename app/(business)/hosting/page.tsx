import type { Metadata } from "next";
import Link from "next/link";
import { biz, bizRoutes } from "../../business";

const description =
  "Managed VPS hosting for the applications we build: provisioning, hardening, TLS, deploys, backups, monitoring, and patching.";

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
  { t: "Capacity review", b: "Periodic check that you are not paying for idle hardware or running out of headroom." },
];

export default function HostingPage() {
  return (
    <section className="mx-auto max-w-biz px-5 pb-16 pt-14 sm:px-8 sm:pt-20">
      <p className="biz-label-blue">Infrastructure</p>
      <h1 className="biz-display mt-5 text-[2.6rem] sm:text-[4rem]">
        Managed VPS hosting
        <span className="ml-1 inline-block h-[0.2em] w-[0.2em] rounded-full bg-brand align-baseline" />
      </h1>
      <p className="biz-lead mt-8 max-w-xl">
        Building the thing is half the job. Someone has to keep it running, patched, and backed up
        afterwards. For most clients that someone is us.
      </p>

      <ul className="mt-16 grid gap-x-10 gap-y-9 border-t border-paper-3 pt-10 sm:grid-cols-2">
        {included.map((item, i) => (
          <li key={item.t}>
            <div className="flex items-baseline gap-3">
              <span className="font-mono text-[0.7rem] text-ink-soft">
                {String(i + 1).padStart(2, "0")}
              </span>
              <h2 className="biz-h3">{item.t}</h2>
            </div>
            <p className="mt-2.5 font-mono text-[0.84rem] leading-relaxed text-ink-body">{item.b}</p>
          </li>
        ))}
      </ul>

      <div className="mt-16 grid gap-8 border-t border-paper-3 pt-10 lg:grid-cols-2">
        <div>
          <h2 className="biz-h2">Who this suits</h2>
          <p className="mt-5 font-mono text-[0.88rem] leading-[1.8] text-ink-body">
            Teams running a handful of applications who do not have, and do not want, a full-time
            operations hire. If you already have a platform team, you do not need this.
          </p>
        </div>
        <div>
          <h2 className="biz-h2">No lock-in</h2>
          <p className="mt-5 font-mono text-[0.88rem] leading-[1.8] text-ink-body">
            The server is yours. Configuration lives in your repository, and if you want to take it
            in-house you get the keys, the runbook, and a handover call rather than an argument.
          </p>
        </div>
      </div>

      <div className="mt-16 border border-ink-ink p-9 sm:p-12">
        <h2 className="biz-h2">Pricing</h2>
        <p className="mt-5 max-w-prose2 font-mono text-[0.9rem] leading-[1.8] text-ink-body">
          Quoted per engagement, based on how many applications you run and what uptime you need.
          Infrastructure is billed at cost. Tell us what you are running and we will send a number.
        </p>
        <Link href={bizRoutes.contact} className="biz-btn mt-8">
          Request a quote
        </Link>
      </div>
    </section>
  );
}
