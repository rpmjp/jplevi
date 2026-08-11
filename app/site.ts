/**
 * Single source of truth for company facts.
 * Header, footer, and every page read from here - nothing gets hardcoded twice.
 */

export type DivisionStatus = "live" | "coming";

export type Division = {
  /** Stable key, also used as a React key and HUD index label. */
  id: string;
  /** Short name used in nav and cards. */
  name: string;
  /** One-line description of the division's remit. */
  blurb: string;
  /** Present only when status is "live". */
  href?: string;
  status: DivisionStatus;
};

export const site = {
  name: "JP Levi",
  legalName: "JP Levi Inc.",
  state: "New Jersey",
  tagline: "A builder's studio.",
  /** Confident one-liner for the hero. */
  statement: "We build the things we'd want to use.",
  /** Supporting line under the hero statement. */
  support:
    "JP Levi Inc. is a New Jersey builder's studio working across gaming, AI/ML, web, and software. One team, shipping under one roof.",
  /** Short "what we do / the studio" line. */
  studioLine:
    "Small team, long horizon. We design, engineer, and ship our own products end to end, and take on select work for people building something worth building.",
  email: {
    hello: "hello@jplevi.com",
  },
  domain: "jplevi.com",
  url: "https://jplevi.com",
  /** JP LEVI INC. incorporated in Newark, NJ on 2015-08-28. */
  founded: 2015,
} as const;

export const divisions: Division[] = [
  {
    id: "gaming",
    name: "Gaming",
    blurb: "Original games and the tooling behind them.",
    href: "/gaming/",
    status: "live",
  },
  {
    id: "ai",
    name: "AI / ML",
    blurb: "Applied models, agents, and the systems that keep them honest.",
    status: "coming",
  },
  {
    id: "web",
    name: "Web",
    blurb: "Fast, durable web properties built to outlast a redesign cycle.",
    status: "coming",
  },
  {
    id: "software",
    name: "Software",
    blurb: "Product engineering: platforms, services, and internal tooling.",
    status: "coming",
  },
];

/** Divisions with a real route, in config order. */
export const liveDivisions = divisions.filter(
  (d): d is Division & { href: string } => d.status === "live" && Boolean(d.href),
);

/** The first live section - the parent home's primary CTA target. */
export const primaryDivision = liveDivisions[0];

export const statusLabel: Record<DivisionStatus, string> = {
  live: "Live",
  coming: "In development",
};
