# JP Levi

Company site for **JP Levi Inc.** — a New Jersey builder's studio spanning gaming, AI/ML, web, and
software. This repo holds the parent brand's home plus the shell that division sections plug into.

## Stack

- Next.js 14 (App Router) · TypeScript · Tailwind CSS
- Static export (`output: "export"`) → deployable to Apache shared hosting (Hostinger)
- Fonts via `next/font/google`: Saira (display), Space Grotesk (body), IBM Plex Mono (mono)

## Commands

```bash
npm install      # install deps
npm run dev      # local dev at http://localhost:3000
npm run build    # static export into out/
npm run lint     # eslint
npm run typecheck
```

`npm run build` emits `out/` containing `index.html`, `gaming/index.html`, `404.html`, and
`.htaccess`. Upload the *contents* of `out/` to the host's `public_html/`.

## Structure

```
app/
  site.ts        single config module — company facts + division list (live | coming)
  layout.tsx     shared shell: fonts, metadata, header, footer, HUD backdrop
  page.tsx       parent home
  gaming/        section landing (stub — real route so the URL exists)
  not-found.tsx  custom 404 (exported as out/404.html)
components/      Wordmark, SiteHeader, SiteFooter, Panel, HudBackdrop
public/.htaccess Apache rules: 404 mapping, security headers, cache policy
```

### Adding a division

Everything nav-, footer-, and home-facing is generated from `divisions` in
[app/site.ts](app/site.ts). To open a new section: add its route under `app/<id>/`, then flip its
entry to `status: "live"` and give it an `href`. Nothing else needs editing.

## Design system

Cockpit-HUD: gunmetal base, **signal orange** for CTAs and energy, **teal** for HUD/telemetry lines
and corner brackets, **amber reserved for hazard/attention only**. Corner-bracket panels, a
restrained scanline/boot motion gated behind `prefers-reduced-motion`, visible keyboard focus, fully
responsive. The parent home runs the system quiet and corporate; sections can turn it up.

Tokens live in [tailwind.config.ts](tailwind.config.ts); component classes (`.panel`, `.hud-label`,
`.btn-primary`, …) in [app/globals.css](app/globals.css).

## Deploying

1. `npm run build`
2. Upload the contents of `out/` to `public_html/` (keep the dotfile `.htaccess`).

CI (`.github/workflows/ci.yml`) runs `npm ci` + `npm run build` on every push to `main` and asserts
the expected files exist in `out/`.
