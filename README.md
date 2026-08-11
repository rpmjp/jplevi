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
  layout.tsx        document shell only: fonts, base metadata, skip link
  site.ts           single config module — company facts + division list (live | coming)
  not-found.tsx     custom 404 (exported as out/404.html); carries its own chrome
  icon.svg          corporate favicon
  (corporate)/      route group wearing the HUD chrome — header, footer, backdrop
    layout.tsx
    page.tsx        parent home
    gaming/         division landing
  mechablast/       game section — owns its chrome, does NOT inherit the corporate one
    layout.tsx      MechaBlast header + footer
    game.ts         game facts; mirrors lib/game/config/legal_config.dart in the app
    legal.ts        build-time reader for content/mechablast/*.md
    icon.svg        section favicon
    page.tsx  privacy/  terms/  support/  press/
components/
  mechablast/       MechaHeader, MechaFooter, Markdown, LegalPage
content/mechablast/ privacy.md, terms.md — rendered verbatim at build time
public/mechablast/  og.png + shots/shot1–4.png
public/.htaccess    Apache rules: 404 mapping, security headers, cache policy
```

### Chrome and route groups

The root layout deliberately holds no header or footer. Corporate pages get theirs from
`app/(corporate)/layout.tsx`; MechaBlast supplies its own in `app/mechablast/layout.tsx`. A route
group `(corporate)` does not appear in the URL, so `/` and `/gaming/` are unchanged. Any future
section that needs different chrome follows the MechaBlast pattern.

### Legal documents

`content/mechablast/privacy.md` and `terms.md` are read with `fs.readFileSync` inside server
components and rendered by react-markdown (+ remark-gfm for tables, + rehype-raw so authored HTML
and comments behave). This happens at **build time only** — under `output: "export"` there is no
request-time filesystem access, and the rendered pages ship ~200 B of route JS.

**The rendering is verbatim.** Nothing rewrites, reflows, or re-titles the source, and the markdown
supplies its own `<h1>`. To update the legal text, replace the markdown file and rebuild.

### Adding a division

Everything nav-, footer-, and home-facing is generated from `divisions` in
[app/site.ts](app/site.ts). To open a new section: add its route under `app/<id>/`, then flip its
entry to `status: "live"` and give it an `href`. Nothing else needs editing.

## Design system

Two systems share one set of tokens.

**Corporate** — cockpit-HUD: gunmetal base, **signal orange** for CTAs and energy, **teal** for HUD/telemetry lines
and corner brackets, **amber reserved for hazard/attention only**. Corner-bracket panels, a
restrained scanline/boot motion gated behind `prefers-reduced-motion`, visible keyboard focus, fully
responsive. The parent home runs the system quiet and corporate; sections can turn it up.

**MechaBlast** — cel-shaded arcade: near-black ground (`mecha.void`), one bold **cyan** accent,
2px outlines and hard blur-free drop shadows (`shadow-cel`) instead of gradients. Buttons press into
their own shadow on hover; that motion is disabled under `prefers-reduced-motion`.

Tokens live in [tailwind.config.ts](tailwind.config.ts); component classes (`.panel`, `.hud-label`,
`.btn-primary`, `.cel-panel`, `.cel-btn-primary`, `.prose-mecha`, …) in
[app/globals.css](app/globals.css).

Every text/ground pair in both palettes meets **WCAG AA** (≥4.5:1); the lowest is `ink-dim` at
5.2:1 on `gun-800`. Text colors are used at full opacity — dimming them with a `/60`-style modifier
drops them below AA.

## Deploying

1. `npm run build`
2. Upload the contents of `out/` to `public_html/` (keep the dotfile `.htaccess`).

CI (`.github/workflows/ci.yml`) runs `npm ci` + `npm run build` on every push to `main` and asserts
the expected files exist in `out/`.
