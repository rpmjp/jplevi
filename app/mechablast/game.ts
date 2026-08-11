/**
 * Single source of truth for MechaBlast facts.
 * The section's header, footer, and every page read from here.
 */

/**
 * Mirrors lib/game/config/legal_config.dart in the app. Keep the two in sync -
 * along with /LICENSE, store/terms_of_service.md, and store/privacy_policy.md.
 */
export const game = {
  name: "MechaBlast",
  /** One-line pitch, used in the hero and as the meta description seed. */
  pitch: "Cel-shaded, landscape, run-and-gun mobile platformer.",
  blurb:
    "Blast robots, beat bosses across 8 worlds, earn scrap, upgrade your mech. Free to play, with optional in-app purchases.",
  publisher: "JP LEVI INC.",
  publisherNote: "a New Jersey corporation",
  brand: "jplevi",
  bundleId: "com.jplevi.mechablast",
  supportEmail: "support@jplevi.com",
  copyrightYear: 2026,
  /** LegalConfig.copyright - the exact in-app notice. */
  copyright: "© 2026 JP LEVI INC. All rights reserved.",
  appVersion: "1.0.0",
  venue: "New Jersey (Essex County)",
  worlds: 8,
  /** Absolute path used as the section's default OG image. */
  ogImage: "/mechablast/og.png",
} as const;

export const routes = {
  home: "/mechablast/",
  privacy: "/mechablast/privacy/",
  terms: "/mechablast/terms/",
  support: "/mechablast/support/",
  press: "/mechablast/press/",
} as const;

/** Footer + header nav, in order. */
export const sectionNav = [
  { href: routes.support, label: "Support" },
  { href: routes.press, label: "Press" },
  { href: routes.privacy, label: "Privacy" },
  { href: routes.terms, label: "Terms" },
] as const;

export type StoreLink = {
  id: string;
  label: string;
  /** TODO: real store URL - placeholder until the listings are live. */
  href: string;
};

export const stores: StoreLink[] = [
  // TODO: real store URL
  { id: "app-store", label: "App Store", href: "#" },
  // TODO: real store URL
  { id: "google-play", label: "Google Play", href: "#" },
];

export const features = [
  {
    title: "Run-and-gun, built for landscape",
    body: "Twin-stick-feel controls laid out for thumbs, not ported from a desktop build.",
  },
  {
    title: `${game.worlds} worlds, ${game.worlds} bosses`,
    body: "Each world escalates the robot roster and ends in a hand-designed boss fight.",
  },
  {
    title: "Earn scrap, upgrade your mech",
    body: "Salvage from every run feeds weapons, armor, and mobility upgrades you choose.",
  },
  {
    title: "Cel-shaded art",
    body: "Hard inked outlines and flat color that stay crisp on every screen density.",
  },
  {
    title: "Free to play, fair by design",
    body: "Fixed, pre-disclosed purchases. No loot boxes, no randomised paid rewards, no gambling mechanics.",
  },
  {
    title: "Ads you can switch off",
    body: "Interstitials run between levels, never mid-play, and a one-time “Remove Ads” purchase turns them off. Rewarded ads stay opt-in.",
  },
  {
    title: "Analytics that stay off",
    body: "Anonymous, aggregate tuning data only, off until you opt in, and switchable in Settings.",
  },
];

export const screenshots = [
  { src: "/mechablast/shots/shot1.png", alt: "MechaBlast gameplay: mech firing on a robot patrol" },
  { src: "/mechablast/shots/shot2.png", alt: "MechaBlast gameplay: boss fight in a scrapyard world" },
  { src: "/mechablast/shots/shot3.png", alt: "MechaBlast gameplay: mech upgrade and loadout screen" },
  { src: "/mechablast/shots/shot4.png", alt: "MechaBlast gameplay: platforming across a collapsing bridge" },
];
