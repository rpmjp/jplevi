import type { Config } from "tailwindcss";

/**
 * Cockpit-HUD design tokens.
 * Gunmetal base + one signal-orange accent (CTAs/energy), teal for HUD/telemetry
 * lines, amber reserved strictly for hazard/attention states.
 */
const config: Config = {
  content: [
    "./app/**/*.{ts,tsx}",
    "./components/**/*.{ts,tsx}",
  ],
  theme: {
    extend: {
      colors: {
        gun: {
          900: "#0B0F13", // deepest hull
          800: "#10161C", // page base
          700: "#151D25", // panel
          600: "#1C2630", // raised panel
          500: "#26333F", // hairline / border
          400: "#37485A", // strong border
        },
        signal: {
          DEFAULT: "#FF6A1F", // signal orange — CTAs, energy
          soft: "#FF8A4C",
          deep: "#C74A0E",
        },
        hud: {
          DEFAULT: "#3ECFC6", // teal — telemetry, brackets, readouts
          soft: "#7FE3DC",
          deep: "#1D8A84",
        },
        hazard: {
          DEFAULT: "#F5B33C", // amber — attention/hazard only
          deep: "#B87E15",
        },
        /**
         * JP Levi AI (business) palette - warm paper, near-black ink, one blue.
         */
        paper: {
          DEFAULT: "#F5F3EF", // page ground
          2: "#EDEAE3", // panel
          3: "#E2DED5", // hairline
          4: "#CFC9BC", // strong rule
        },
        ink: {
          DEFAULT: "#DCE4EC", // primary text (dark themes)
          muted: "#93A2B1",
          dim: "#7C8B9C",
          ink: "#0B0B0C", // near-black, business headings
          body: "#33343A", // business body copy
          soft: "#5E6069", // business tertiary
        },
        brand: {
          DEFAULT: "#1B3EF0", // electric blue - links, CTAs, accents
          deep: "#1430B8",
          soft: "#5A76FF",
        },
        ember: "#E4572E", // micro accent (ticks, brackets)
        live: {
          DEFAULT: "#16A34A", // on dark grounds
          deep: "#146C33", // on paper - meets 3:1 non-text contrast
        },
        night: {
          DEFAULT: "#0B0B0C", // inverted band ground
          2: "#141416",
        },
        /**
         * MechaBlast section palette — cel-shaded arcade.
         * Near-black ground + one bold cyan accent, hard outlines, no gradients.
         */
        mecha: {
          void: "#05070A", // page ground
          panel: "#0C1117", // card / panel fill
          raised: "#131A22", // raised surface
          line: "#232E3A", // hairline
          edge: "#3A4A5C", // strong outline
          cyan: "#2BE7F5", // the accent — 11.9:1 on void
          "cyan-soft": "#8DF3FA",
          "cyan-deep": "#0A7E8A",
        },
      },
      fontFamily: {
        display: ["var(--font-display)", "ui-sans-serif", "system-ui", "sans-serif"],
        grotesk: ["var(--font-grotesk)", "ui-sans-serif", "system-ui", "sans-serif"],
        sans: ["var(--font-body)", "ui-sans-serif", "system-ui", "sans-serif"],
        mono: ["var(--font-mono)", "ui-monospace", "SFMono-Regular", "monospace"],
      },
      letterSpacing: {
        hud: "0.22em",
        label: "0.18em",
        tight2: "-0.03em",
        tight3: "-0.045em",
        wordmark: "0.06em",
      },
      maxWidth: {
        shell: "78rem",
        biz: "84rem",
        prose2: "68ch",
      },
      boxShadow: {
        panel: "0 1px 0 0 rgba(255,255,255,0.03) inset, 0 24px 48px -32px rgba(0,0,0,0.9)",
        signal: "0 0 0 1px rgba(255,106,31,0.35), 0 12px 32px -16px rgba(255,106,31,0.55)",
        // Hard, blur-free offsets — the cel-shaded "inked" edge.
        cel: "5px 5px 0 0 #05070A",
        "cel-sm": "3px 3px 0 0 #05070A",
        "cel-cyan": "5px 5px 0 0 #0A7E8A",
      },
      backgroundImage: {
        grid:
          "linear-gradient(to right, rgba(62,207,198,0.055) 1px, transparent 1px), linear-gradient(to bottom, rgba(62,207,198,0.055) 1px, transparent 1px)",
      },
      backgroundSize: {
        // Distinct key so it doesn't collide with the `bg-grid` image utility.
        "grid-cell": "72px 72px",
      },
      keyframes: {
        scan: {
          "0%": { transform: "translateY(-100%)" },
          "100%": { transform: "translateY(100vh)" },
        },
        boot: {
          "0%": { opacity: "0", transform: "translateY(8px)" },
          "100%": { opacity: "1", transform: "translateY(0)" },
        },
        sweep: {
          "0%, 100%": { opacity: "0.25" },
          "50%": { opacity: "0.7" },
        },
      },
      animation: {
        scan: "scan 9s linear infinite",
        boot: "boot 700ms cubic-bezier(0.22, 1, 0.36, 1) both",
        sweep: "sweep 4s ease-in-out infinite",
      },
    },
  },
  plugins: [require("@tailwindcss/typography")],
};

export default config;
